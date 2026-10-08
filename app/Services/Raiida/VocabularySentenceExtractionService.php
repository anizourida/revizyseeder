<?php

namespace App\Services\Raiida;

use App\Models\Raiida\BookPage;
use App\Models\Raiida\Page;
use App\Models\Raiida\VocabularyItem;
use App\Models\Raiida\VocabularySentence;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class VocabularySentenceExtractionService
{
    /**
     * Common phrases / instructions to filter out.
     */
    protected array $blacklistedPhrases = [
        'qui veut répéter',
        'qui veut nommer',
        'qui veut épeler',
        'qui veut passer',
        'qui veut compléter',
        'lecture de la vidéo',
        'répétons ensemble',
        'plan de la séance',
        'prenez vos ardoises',
        'rangez vos ardoises',
        'ouvrez le livret',
        'c’est à vous maintenant',
        'maintenant, on va apprendre',
        'maintenant on va faire',
        'la séance d’aujourd’hui est terminée',
        'réservé à l’enseignant',
        'activités de vocabulaire',
        'activités sur livret',
        'espace réservé',
        'je montre l’image',
        'je dis le mot',
        'je passe entre les rangs',
        'à tour de rôle',
        'observez cette scène',
        'observez le mot',
        'soyez attentifs',
        'va nous montrer',
        'va décrire la scène',
        'écrivez le numéro',
        'ecrivez le numéro',
        'la bonne réponse est',
        'situation :',
        'je vais vous expliquer',
        'graphème',
        'phonème',
        'lecture du graphème',
        'écriture du graphème',
        'prendre la parole',
        'prend la parole',
        'prenez la parole',
        'prise de parole',
        'acte de parole',
        'actes de parole',
        'à propos du modelage',
        'a propos du modelage',
        'jouer un dialogue',
    ];

    /**
     * Extract sentences for all or filtered French vocabulary items.
     *
     * @param array{
     *   grade?: string,
     *   period?: string,
     *   week?: string,
     *   lesson_id?: string,
     *   force?: bool
     * } $options
     * @return array{
     *   total_vocabs: int,
     *   sentences_created: int,
     *   vocabs_with_sentences: int,
     *   vocabs_without_sentences: int
     * }
     */
    public function extractSentences(array $options = []): array
    {
        $query = VocabularyItem::query()
            ->where(function ($q) {
                $q->where('subject', 'FR')
                  ->orWhere('subject', 'French')
                  ->orWhere('subject', 'Français')
                  ->orWhereNull('subject');
            });

        if (! empty($options['grade'])) {
            $query->where('grade', strtoupper(trim($options['grade'])));
        }
        if (! empty($options['period'])) {
            $query->where('period', strtoupper(trim($options['period'])));
        }
        if (! empty($options['week'])) {
            $query->where('week', strtoupper(trim($options['week'])));
        }
        if (! empty($options['lesson_id'])) {
            $query->where('lesson_id', trim($options['lesson_id']));
        }

        $vocabs = $query->orderBy('grade')
            ->orderBy('period')
            ->orderBy('week')
            ->orderBy('word')
            ->get();

        $stats = [
            'total_vocabs' => $vocabs->count(),
            'sentences_created' => 0,
            'vocabs_with_sentences' => 0,
            'vocabs_without_sentences' => 0,
        ];

        // Group vocabulary items by (grade, period, week) to cache slide data efficiently
        $grouped = $vocabs->groupBy(function ($v) {
            return $v->grade . '|' . $v->period . '|' . $v->week;
        });

        $includeRevision = ! (bool) ($options['no_revision'] ?? false);

        foreach ($grouped as $groupKey => $groupVocabs) {
            [$grade, $period, $week] = explode('|', $groupKey);
            $presentationTexts = $this->collectPresentationTextsForWeek($grade, $period, $week, $includeRevision);
            $ocrTexts = $this->collectOcrTextsForWeek($grade, $period, $week, $includeRevision);

            foreach ($groupVocabs as $vocab) {
                $createdForVocab = $this->processVocabItem($vocab, $presentationTexts, $ocrTexts, (bool) ($options['force'] ?? false));
                if ($createdForVocab > 0) {
                    $stats['vocabs_with_sentences']++;
                    $stats['sentences_created'] += $createdForVocab;
                } else {
                    $stats['vocabs_without_sentences']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Process a single vocabulary item and save discovered sentences.
     */
    public function processVocabItem(
        VocabularyItem $vocab,
        array $presentationTexts,
        array $ocrTexts,
        bool $force = false
    ): int {
        if ($force) {
            VocabularySentence::where('vocabulary_item_id', $vocab->id)->delete();
        } else {
            // If sentences already exist for this vocabulary item, skip
            if (VocabularySentence::where('vocabulary_item_id', $vocab->id)->exists()) {
                return VocabularySentence::where('vocabulary_item_id', $vocab->id)
                    ->whereNotNull('sentence')
                    ->where('sentence', '!=', '')
                    ->count();
            }
        }

        $candidates = $this->findSentencesForWord($vocab, $presentationTexts, $ocrTexts);

        // Supplement with appropriate generated model sentences if available
        $generated = $this->generateAppropriateSentencesForVocab($vocab);
        foreach ($generated as $genSentence) {
            $norm = mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', $genSentence));
            $alreadyExists = false;
            foreach ($candidates as $existingCand) {
                if (mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', $existingCand['sentence'])) === $norm) {
                    $alreadyExists = true;
                    break;
                }
            }
            if (! $alreadyExists) {
                $candidates[] = [
                    'sentence' => $genSentence,
                    'session' => 'GEN',
                    'slide' => null,
                    'type' => 'generated',
                ];
            }
        }

        if (empty($candidates)) {
            // Find default file asset for this lesson
            $defaultAssetId = \App\Models\Raiida\FileAsset::where('filename', 'like', $vocab->lesson_id . '%')
                ->orWhere('presentation_json_path', 'like', '%' . $vocab->lesson_id . '%')
                ->value('id');

            // Save placeholder record so vocabulary is copied and accounted for
            VocabularySentence::create([
                'vocabulary_item_id' => $vocab->id,
                'file_asset_id' => $defaultAssetId,
                'word' => $vocab->word,
                'base_word' => $vocab->base_word,
                'grade' => $vocab->grade,
                'subject' => $vocab->subject ?: 'FR',
                'period' => $vocab->period,
                'week' => $vocab->week,
                'lesson_id' => $vocab->lesson_id,
                'sentence' => null,
                'sentence_ar' => null,
                'source_session' => null,
                'source_slide' => null,
                'source_type' => 'slide',
                'image_path' => $vocab->image_path,
                'audio_path' => null,
            ]);

            return 0;
        }

        // Rank candidates by pedagogical score so the best choice is first
        usort($candidates, function ($a, $b) use ($vocab) {
            $scoreA = $this->scoreSentence($a['sentence'], $vocab->word, $a['session'] ?? null, $a['type'] ?? 'slide')['score'];
            $scoreB = $this->scoreSentence($b['sentence'], $vocab->word, $b['session'] ?? null, $b['type'] ?? 'slide')['score'];

            return $scoreB <=> $scoreA;
        });

        $created = 0;
        // Keep up to 6 diverse, high-quality sentences per vocabulary item
        $maxPerVocab = 6;
        $savedCandidates = array_slice($candidates, 0, $maxPerVocab);

        foreach ($savedCandidates as $cand) {
            $assetId = $cand['file_asset_id'] ?? null;
            if (! $assetId && ! empty($cand['session'])) {
                if (preg_match('/^(SEM\d+)_(S\d+)$/i', $cand['session'], $sm)) {
                    $prefix = 'FR_' . $vocab->grade . '_' . $vocab->period . '_' . $sm[1] . '_' . $sm[2];
                } else {
                    $prefix = 'FR_' . $vocab->grade . '_' . $vocab->period . '_' . $vocab->week . '_' . $cand['session'];
                }

                $assetId = \App\Models\Raiida\FileAsset::where('filename', 'like', $prefix . '%')
                    ->orWhere('presentation_json_path', 'like', '%' . $prefix . '%')
                    ->value('id');
            }

            if (! $assetId) {
                $assetId = \App\Models\Raiida\FileAsset::where('filename', 'like', $vocab->lesson_id . '%')
                    ->orWhere('presentation_json_path', 'like', '%' . $vocab->lesson_id . '%')
                    ->value('id');
            }

            VocabularySentence::create([
                'vocabulary_item_id' => $vocab->id,
                'file_asset_id' => $assetId,
                'word' => $vocab->word,
                'base_word' => $vocab->base_word,
                'grade' => $vocab->grade,
                'subject' => $vocab->subject ?: 'FR',
                'period' => $vocab->period,
                'week' => $vocab->week,
                'lesson_id' => $vocab->lesson_id,
                'sentence' => $cand['sentence'],
                'sentence_ar' => null,
                'source_session' => $cand['session'] ?? null,
                'source_slide' => $cand['slide'] ?? null,
                'source_type' => $cand['type'] ?? 'slide',
                'image_path' => $vocab->image_path,
                'audio_path' => null,
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Build intelligent search terms for a vocabulary item including conjugations, plurals, and elisions.
     *
     * @return string[]
     */
    public function buildSearchTerms(VocabularyItem $vocab): array
    {
        $word = trim($vocab->word);
        $baseWord = trim($vocab->base_word ?? '');
        $terms = array_filter([$word, $baseWord]);

        // Verbs & reflexive verbs (e.g. s'appeler)
        if (stripos($word, 'appeler') !== false || stripos($baseWord, 'appeler') !== false) {
            $terms = array_merge($terms, [
                "s'appeler", "s’appeler",
                "m'appelle", "m’appelle",
                "t'appelles", "t’appelles",
                "s'appelle", "s’appelle",
                "nous appelons", "vous appelez",
                "s'appellent", "s’appellent",
                "appelle", "appelles", "appeler",
            ]);
        }

        // Generic 1st group verbs if base ends in 'er'
        $cleanWord = preg_replace('/^(?:s[’\']|se\s+)/ui', '', $word);
        $cleanBase = preg_replace('/^(?:s[’\']|se\s+)/ui', '', $baseWord);
        foreach ([$cleanWord, $cleanBase] as $w) {
            if (str_ends_with(mb_strtolower($w), 'er') && mb_strlen($w) > 3) {
                $stem = mb_substr($w, 0, -2);
                $terms[] = $stem . 'e';
                $terms[] = $stem . 'es';
                $terms[] = $stem . 'ent';
                $terms[] = $stem . 'ons';
                $terms[] = $stem . 'ez';
            }
        }

        // Irregular plurals (-eau -> -eaux)
        foreach ([$word, $baseWord] as $w) {
            if (str_ends_with(mb_strtolower($w), 'eau')) {
                $terms[] = $w . 'x';
            }
        }

        // 3rd group / irregular verbs conjugations
        $lowerWord = mb_strtolower($word);
        $lowerBase = mb_strtolower($baseWord);
        foreach ([$lowerWord, $lowerBase] as $w) {
            if ($w === 'boire') {
                $terms = array_merge($terms, ['bois', 'boit', 'buvons', 'buvez', 'boivent', 'boire']);
            } elseif ($w === 'voir') {
                $terms = array_merge($terms, ['vois', 'voit', 'voyons', 'voyez', 'voient', 'voir']);
            } elseif ($w === 'prendre') {
                $terms = array_merge($terms, ['prends', 'prend', 'prenons', 'prenez', 'prennent', 'prendre']);
            } elseif ($w === 'faire') {
                $terms = array_merge($terms, ['fais', 'fait', 'faisons', 'faites', 'font', 'faire']);
            } elseif ($w === 'aller') {
                $terms = array_merge($terms, ['vais', 'vas', 'va', 'allons', 'allez', 'vont', 'aller']);
            } elseif ($w === 'dire') {
                $terms = array_merge($terms, ['dis', 'dit', 'disons', 'dites', 'disent', 'dire']);
            } elseif ($w === 'lire') {
                $terms = array_merge($terms, ['lis', 'lit', 'lisons', 'lisez', 'lisent', 'lire']);
            } elseif ($w === 'écrire') {
                $terms = array_merge($terms, ['écris', 'écrit', 'écrivons', 'écrivez', 'écrivent', 'écrire']);
            } elseif ($w === 'mettre') {
                $terms = array_merge($terms, ['mets', 'met', 'mettons', 'mettez', 'mettent', 'mettre']);
            } elseif ($w === 'résoudre') {
                $terms = array_merge($terms, ['résous', 'résout', 'résolvons', 'résolvez', 'résolvent', 'résolu', 'résoudre']);
            }
        }

        // Elision variations (l'eau, d'eau)
        if (str_starts_with(mb_strtolower($word), "l’") || str_starts_with(mb_strtolower($word), "l'")) {
            $bare = preg_replace("/^l['’]/ui", "", $word);
            $terms[] = $bare;
            $terms[] = "l'" . $bare;
            $terms[] = "l’" . $bare;
            $terms[] = "d'" . $bare;
            $terms[] = "d’" . $bare;
        }

        return array_values(array_unique(array_filter($terms)));
    }

    /**
     * Search candidate texts for full sentences containing the vocabulary word or base word.
     *
     * @return array<int, array{sentence: string, session?: string, slide?: int, file_asset_id?: int, type: string}>
     */
    public function findSentencesForWord(
        VocabularyItem $vocab,
        array $presentationTexts,
        array $ocrTexts
    ): array {
        $word = trim($vocab->word);
        $baseWord = trim($vocab->base_word ?? '');
        $searchTerms = $this->buildSearchTerms($vocab);

        $found = [];
        $seenSentences = [];

        // 1. Search presentation texts
        foreach ($presentationTexts as $item) {
            $rawText = $item['text'];
            $sentences = $this->splitIntoSentences($rawText);

            foreach ($sentences as $sentence) {
                if (! $this->isValidSentenceForVocab($sentence, $searchTerms, $word, $baseWord)) {
                    continue;
                }

                $normalizedKey = mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', $sentence));
                if (isset($seenSentences[$normalizedKey])) {
                    continue;
                }
                $seenSentences[$normalizedKey] = true;

                $found[] = [
                    'sentence' => $sentence,
                    'session' => $item['session'] ?? null,
                    'slide' => $item['slide_id'] ?? null,
                    'file_asset_id' => $item['file_asset_id'] ?? null,
                    'type' => 'slide',
                ];
            }
        }

        // 2. Search OCR texts if needed
        foreach ($ocrTexts as $ocrItem) {
            $sentences = $this->splitIntoSentences($ocrItem['text']);
            foreach ($sentences as $sentence) {
                if (! $this->isValidSentenceForVocab($sentence, $searchTerms, $word, $baseWord)) {
                    continue;
                }

                $normalizedKey = mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', $sentence));
                if (isset($seenSentences[$normalizedKey])) {
                    continue;
                }
                $seenSentences[$normalizedKey] = true;

                $found[] = [
                    'sentence' => $sentence,
                    'session' => null,
                    'slide' => $ocrItem['page_number'] ?? null,
                    'type' => 'ocr',
                ];
            }
        }

        return $found;
    }

    /**
     * Check if a sentence is a valid sentence candidate for the vocabulary word.
     */
    public function isValidSentenceForVocab(
        ?string $sentence,
        array $searchTerms,
        string $fullWord,
        string $baseWord
    ): bool {
        if (! is_string($sentence)) {
            return false;
        }

        $sentence = trim($sentence);
        if ($sentence === '') {
            return false;
        }

        // Questions are not optimal vocabulary model sentences, except canonical dialogue questions (e.g. Tu as quel âge ?)
        if (str_ends_with($sentence, '?')) {
            if (in_array(mb_strtolower(trim($fullWord)), ['l’âge', "l'âge", 'âge']) && preg_match('/^Tu\s+as\s+quel\s+âge\s*\?$/ui', $sentence)) {
                // Allowed model question
            } else {
                return false;
            }
        }

        $lowerSentence = mb_strtolower($sentence);

        // Filter blacklisted phrases / teacher instructions
        foreach ($this->blacklistedPhrases as $blacklisted) {
            if (str_contains($lowerSentence, $blacklisted)) {
                return false;
            }
        }

        // Strict regex filters for classroom directions, meta-instructions, exercise templates, phonics, and questions
        $instructionPatterns = [
            '/^(?:Nous allons|On va|Je vais|Vous allez|Il faut|Il convient|Maintenant,?\s*(?:nous|on|je|vous|écrivez|observez|lisez|regardez)|Aujourd[\'’\`´]hui)/ui',
            '/^(?:Sur vos|Dans vos|Sur le|Sur votre|Prenez|Rangez|Ouvrez|Fermez|À la maison|A la maison)/ui',
            '/^(?:Écrivez|Ecrivez|Lisez|Regardez|Écoutez|Ecoutez|Observez|Trouvez|Complétez|Soulignez|Entourez|Cochez|Reliez|Mettez|Placez|Répétez|Montrez|Devinez|Dites|Faîtes|Faites|Posez|Répondez|Corrigez|Jouez)/ui',
            '/^(?:Chacun|Tout le monde|À tour de rôle|A tour de rôle)/ui',
            '/^(?:(?:Une|La)\s+(?:bonne\s+)?réponse(?:\s+correcte)?\s+est|Les\s+(?:bonnes\s+)?réponses(?:\s+correctes)?\s+sont|Les\s+deux\s+mots|Le\s+mot\s+qui|(?:Une|La)\s+phrase\s+correcte|(?:Une|La)\s+réponse\s+peut\s+être)/ui',
            '/^(?:Il y a des noms|C’est le mot|C\'est le mot|Observez le mot|On dit (?:un|une|le|la))\b/ui',
            '/^(?:Situation|Dialogue|Consigne|Activité|Exercice|Conjugaison|Grammaire|Orthographe|Vocabulaire|Lecture|Acte de parole)\b/ui',
            '/^(?:Les (?:deux|trois|quatre|cinq|six )?images? qui|L[\'’]image qui)\b/ui',
            '/\b(?:passer au tableau|passez au tableau|entre les rangs|mot invisible|mots invisibles|nom masculin|nom féminin|mode diaporama)\b/ui',
            '/\//', // slashes like un / une
            '/^(?:Que dit|Que fait|Que faisait|Pourquoi|Où sont|Qui est|Quel est|Quelle est|Quels sont|Quelles sont|Comment|Qu’est-ce qu’on dit|Qu\'est-ce qu\'on dit)\b/ui',
            '/\b(?:j’entends le son|j\'entends le son|je vois la lettre|entendez(?:-vous)? le son|fait le son|font le son)\b/ui',
            '/^Dans le mot\b/ui',
            '/^(?:Lire|Écrire|Ecrire|Dire|Parler|Écouter|Ecouter)\s+(?:des|les|un|une|le|la)\s+[a-zà-öø-ÿ]+(?:\.)?$/ui',
            '/^(?:Lire|Écrire|Ecrire)\s+(?:et\s+comprendre|correctement|des\s+(?:mots|textes|phrases).*(?:correctement|avec\s+fluidité))\b/ui',
            '/^Répondre\s+(?:correctement\s+)?aux\s+exercices/ui',
            '/^[a-zà-öø-ÿ]\b/u', // starts with lowercase letter (fragment)
            '/[.]{3,}|[…]{1,}|_{2,}/u', // dotted or underscore blanks
            '/\b(?:lui\s+dit|leur\s+dit|me\s+dit|te\s+dit|nous\s+dit|vous\s+dit)\b/ui',
            '/^(?:Il|Elle|On|Le professeur|L[\'’]enseignant|L[\'’]enseignante|L[\'’]élève)\s+(?:lui\s+)?dit\b/ui',
            '/\bdit\s*:\s*[«"“]/ui',
            '/\b(?:dit|répond|demande)\s+[«"“]/ui',
            '/\b(?:ça veut dire|veut dire|signifie|sens du mot)\b/ui',
            '/\b(?:prendre|prend|prenez|prenons|prise\s+de)\s+la\s+parole\b/ui',
            '/\b(?:acte|actes)\s+de\s+parole\b/ui',
            '/\b(?:jouer\s+un\s+dialogue|joue\s+le\s+dialogue|jouez\s+le\s+dialogue)\b/ui',
            '/\b(?:étape|etape)\s+\d+\b/ui',
            '/\b(?:à\s+propos\s+du|a\s+propos\s+du)\s+modelage\b/ui',
            '/\b(?:qui\s+veut\s+répéter|qui\s+veut\s+épeler|qui\s+veut\s+passer|qui\s+veut\s+lire|qui\s+veut\s+nommer|qui\s+veut\s+compléter)\b/ui',
            '/\b(?:plan\s+de\s+la\s+séance|réservé\s+à\s+l’enseignant|réservé\s+à\s+l\'enseignant)\b/ui',
            '/\b(?:la\s+leçon\s+de\s+français\s+commence)\b/ui',
            '/\b(?:s’appeler\s+au\s+présent|s\'appeler\s+au\s+présent)\b/ui',
            '/^(?:Tu dois|Vous devez|L’enseignant|L\'enseignant|L’élève|L\'élève)\b/ui',
            '/^(?:Poser|Répondre à)\s+la\s+question\b/ui',
            '/^Je dis (?:mon|ma|mes|où|comment|qui|ce que)\b/ui',
            '/^Je cherche (?:un|une)\b/ui',
            '/\b(?:Questions en rafale|Questions en rafales)\b/ui',
            '/\b(?!Tu\b)(?:[A-ZÀ-ÖØ-ß][a-zà-öø-ÿ]+|Il|Elle)\s+as\b/u', // 3rd person singular with 'as' (OCR typo, excluding valid 'Tu as')
            '/\b(?:sur|sous|dans|de|du|des|le|la|les|un|une|et|à|en|pour|avec)$/ui', // dangling preposition
            '/^[A-Z]{2,}\d*\s+/u', // curriculum objective code prefixes (e.g. OL1, PE2, LF1)
            '/^Les\s+(?:deux|trois|quatre|cinq|six)?\s*images?\s+qui\s+ont\s+bougé/ui', // animation/game prompt
            '/^(?:Points?\s+de\s+langue|Mots?\s+avec\s+difficultés)/ui',
            '/^(?:Lire\s+et\s+comprendre|Écrire\s+correctement|Écris\s+des\s+phrases|Produire,\s*à\s+l[\'’]écrit|Utiliser\s+les\s+outils)\b/ui',
            '/^(?:Avec\s+votre\s+voisin|Après,\s*vous\s+allez|En\s+cas\s+d[\'’]indisponibilité|Cherchez\s+les\s+réponses|Lis\s+les\s+mots|Voici\s+le\s+paragraphe|Parle\s+de\s+ton\s+école)\b/ui',
            '/^Je\s+lis\s+(?:en\s+silence|la\s+question)\b/ui',
            '/^(?:Amine|Lina|Yasmine)\s+écrit\s*:\s*j[\'’]apprends/ui',
            '/\b(?:le|la|les|l’|l\')\s+(?:lit|écrit|prend|met|voit)\s*[.!?]?$/ui', // pronoun replacement exercise fragments
            '/^C[\'’]est\s+une\s+opération\s*[.!?]?$/ui', // ambiguous standalone phrase
        ];

        foreach ($instructionPatterns as $pattern) {
            if (preg_match($pattern, $sentence)) {
                return false;
            }
        }

        // Real pedagogical sentences must end with terminal punctuation (. or ! or ?)
        if (! preg_match('/[.!?] *$/u', $sentence)) {
            return false;
        }

        // Filter out bullet points, arrows, or matching activity dots (e.g., "Tu • s’appelle Sami.")
        if (str_contains($sentence, '•') || preg_match('/[\x{2022}\x{25E6}\x{2023}\x{2043}]/u', $sentence)) {
            return false;
        }

        // Filter out fill-in-the-blank blanks (Arabic Tatweel ـ, underscores, multiple dots, dashes, box symbols)
        if (
            preg_match('/[\x{0640}_]/u', $sentence) ||
            preg_match('/[.]{2,}|[…]{1,}|[-—–]{2,}/u', $sentence) ||
            preg_match('/[□▢⬜]/u', $sentence) ||
            preg_match('/\(\s*\.{2,}\s*\)|\(\s*_{1,}\s*\)/u', $sentence)
        ) {
            return false;
        }

        // Filter out syllable breakdowns or arrows (e.g., "t eau > teau > bateau")
        if (str_contains($sentence, '>') || str_contains($sentence, '->') || str_contains($sentence, '→') || str_contains($sentence, '<')) {
            return false;
        }

        // Filter out word list lines (e.g., containing bullet dashes '–' or '-')
        if (str_contains($sentence, '–') || preg_match('/\s+-\s+/', $sentence)) {
            return false;
        }

        // Filter out comma-separated lists of 4+ items
        if (substr_count($sentence, ',') >= 3 && ! str_ends_with($sentence, '.')) {
            return false;
        }

        // Must start with uppercase letter
        if (! preg_match('/^[A-ZÀ-ÖØ-ß]/u', $sentence)) {
            return false;
        }

        // Must contain at least 3 words
        $words = preg_split('/\s+/u', $sentence, -1, PREG_SPLIT_NO_EMPTY);
        if (! is_array($words) || count($words) < 3) {
            return false;
        }

        // Check if exactly equals the vocab word itself
        if (mb_strtolower(trim($sentence, " .!?:;\"'")) === mb_strtolower(trim($fullWord))) {
            return false;
        }

        // Check if any search term matches as a standalone word/phrase
        $matchesTerm = false;
        foreach ($searchTerms as $term) {
            if (mb_strlen($term) < 2) {
                continue;
            }

            $escaped = preg_quote($term, '/');
            $pattern = '/(?<![\p{L}\p{N}\-_])(?:' . $escaped . '|' . $escaped . 's)(?![\p{L}\p{N}\-_])/iu';
            if (preg_match($pattern, $sentence)) {
                $matchesTerm = true;
                break;
            }
        }

        return $matchesTerm;
    }

    /**
     * Collect all slide texts for a given grade, period, and week across all sessions, optionally including revision weeks.
     *
     * @return array<int, array{session: string, slide_id: int, file_asset_id: ?int, text: string}>
     */
    protected function collectPresentationTextsForWeek(
        string $grade,
        string $period,
        string $week,
        bool $includeRevisionWeeks = true
    ): array {
        $gradeNorm = str_ireplace('N', '', $grade);
        $periodNorm = str_ireplace('P', '', $period);
        $weekNorm = str_ireplace('SEM', '', $week);

        $weeksToScan = [$weekNorm];
        if ($includeRevisionWeeks) {
            // Revision weeks in standard periods are SEM5 and SEM6
            if (! in_array('5', $weeksToScan, true)) {
                $weeksToScan[] = '5';
            }
            if (! in_array('6', $weeksToScan, true)) {
                $weeksToScan[] = '6';
            }
        }

        $files = [];
        foreach ($weeksToScan as $w) {
            $dirPatterns = [
                storage_path("app/presentation_data/FR_N{$gradeNorm}_P{$periodNorm}_SEM{$w}_*/data.json"),
                storage_path("app/presentation_data/FR_N{$gradeNorm}_P{$periodNorm}_S{$w}_*/data.json"),
                storage_path("app/presentation_data/FR_N{$gradeNorm}_P{$periodNorm}_semaine_{$w}_*/data.json"),
            ];
            foreach ($dirPatterns as $pattern) {
                $matched = glob($pattern);
                if ($matched) {
                    $files = array_merge($files, $matched);
                }
            }
        }
        $files = array_unique($files);

        $results = [];
        foreach ($files as $filePath) {
            try {
                $dirName = basename(dirname($filePath));
                // Extract session (e.g., S1, S2, S3...)
                $session = 'S1';
                if (preg_match('/_(S[1-6](?:_V\d+)?)$/i', $dirName, $sMatches)) {
                    $session = strtoupper($sMatches[1]);
                }

                // If from revision week, prefix session so it is transparent (e.g., SEM5_S1)
                if (preg_match('/_SEM([56])_/i', $dirName, $semMatches)) {
                    $session = 'SEM' . $semMatches[1] . '_' . $session;
                }

                $fileAssetId = \App\Models\Raiida\FileAsset::where('presentation_json_path', 'like', "%{$dirName}%")
                    ->orWhere('filename', 'like', "{$dirName}%")
                    ->value('id');

                $json = json_decode((string) file_get_contents($filePath), true);
                if (! is_array($json) || empty($json['slides'])) {
                    continue;
                }

                foreach ($json['slides'] as $slide) {
                    $slideId = (int) ($slide['id'] ?? 0);
                    $elements = $slide['elements'] ?? [];

                    foreach ($elements as $elem) {
                        if (isset($elem['content']) && is_string($elem['content'])) {
                            $text = trim($elem['content']);
                            if ($text !== '') {
                                $results[] = [
                                    'session' => $session,
                                    'slide_id' => $slideId,
                                    'file_asset_id' => $fileAssetId,
                                    'text' => $text,
                                ];
                            }
                        }
                    }
                }
            } catch (Throwable $e) {
                Log::warning('Failed reading presentation json: ' . $filePath . ': ' . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Collect OCR texts from Page for the week, optionally including revision weeks.
     *
     * @return array<int, array{page_number: int, text: string}>
     */
    protected function collectOcrTextsForWeek(
        string $grade,
        string $period,
        string $week,
        bool $includeRevisionWeeks = true
    ): array {
        $gradeNorm = str_ireplace('N', '', $grade);
        $periodNorm = str_ireplace('P', '', $period);
        $weekNorm = str_ireplace('SEM', '', $week);

        $weeksToScan = [$weekNorm];
        if ($includeRevisionWeeks) {
            if (! in_array('5', $weeksToScan, true)) {
                $weeksToScan[] = '5';
            }
            if (! in_array('6', $weeksToScan, true)) {
                $weeksToScan[] = '6';
            }
        }

        $results = [];

        try {
            $pagesQuery = Page::query()->where(function ($query) use ($gradeNorm, $periodNorm, $weeksToScan) {
                foreach ($weeksToScan as $w) {
                    $key = 'FR_N' . $gradeNorm . '_P' . $periodNorm . '_SEM' . $w;
                    $query->orWhere('n_p_sem', 'like', $key . '%');
                }
            })->where(function ($q) {
                $q->whereNotNull('ocr_olmocr_path')
                  ->orWhereNotNull('ocr_chandra_path')
                  ->orWhereNotNull('ocr_full_text_path');
            });

            $pages = $pagesQuery->get(['n_p_sem', 'page_number', 'ocr_olmocr_path', 'ocr_chandra_path', 'ocr_full_text_path']);

            foreach ($pages as $p) {
                $path = $p->ocr_olmocr_path ?: $p->ocr_chandra_path ?: $p->ocr_full_text_path;
                $fullPath = storage_path('app/public/' . $path);
                if (! file_exists($fullPath)) {
                    $fullPath = storage_path('app/' . $path);
                }

                if (file_exists($fullPath)) {
                    $content = (string) file_get_contents($fullPath);
                    $text = trim(strip_tags($content));
                    if ($text !== '') {
                        $results[] = [
                            'page_number' => (int) $p->page_number,
                            'text' => $text,
                        ];
                    }
                }
            }
        } catch (Throwable $e) {
            Log::warning('Failed collecting OCR texts for week FR_N' . $gradeNorm . '_P' . $periodNorm . '_SEM' . $weekNorm . ': ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Split text block into individual clean sentences.
     *
     * @return string[]
     */
    public function splitIntoSentences(string $text): array
    {
        // 1. Insert space after punctuation when directly followed by capital letter (glued OCR)
        $text = preg_replace('/([.!?])(?=[A-ZÀ-ÖØ-ß])/u', '$1 ', $text);

        // 2. Split by lines
        $lines = preg_split('/\r\n|\r|\n/', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (! is_array($lines)) {
            return [];
        }
        $sentences = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Split columns / large spacing
            $columnItems = preg_split('/\s{2,}|\t/u', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (! is_array($columnItems)) {
                $columnItems = [$line];
            }

            foreach ($columnItems as $item) {
                $item = trim($item);
                if ($item === '') {
                    continue;
                }

                // Split sentence boundaries
                $parts = preg_split('/(?<=[.!?])\s+(?=[A-ZÀ-ÖØ-ß])/u', $item, -1, PREG_SPLIT_NO_EMPTY);
                if (! is_array($parts)) {
                    $parts = [$item];
                }
                foreach ($parts as $part) {
                    $cleaned = trim((string) $part);
                    // Strip meta prefixes
                    $cleaned = (string) preg_replace('/^(?:Voici\s+une\s+phrase\s+correcte\s*:\s*|Une\s+phrase\s+correcte\s*:\s*|(?:La|Une)\s+(?:bonne\s+|correcte\s+)?réponse\s+(?:est|peut\s+être)?\s*:\s*|Exemple\s*:\s*|Par\s+exemple\s*:\s*|Retenez\s*!\s*)/ui', '', $cleaned);
                    $cleaned = (string) preg_replace('/[»"“]\s*([.!?])$/u', '$1', $cleaned);
                    $cleaned = trim((string) preg_replace('/\s+/u', ' ', $cleaned), " \t\n\r\0\x0B\"'«»-–");
                    $cleaned = (string) preg_replace('/\s+([.])$/u', '$1', $cleaned);
                    $cleaned = (string) preg_replace('/\s*\?$/u', ' ?', $cleaned);
                    if ($cleaned !== '') {
                        $sentences[] = $cleaned;
                    }
                }
            }
        }

        return array_values(array_unique($sentences));
    }

    /**
     * Score a vocabulary sentence candidate based on pedagogical quality and curriculum hierarchy.
     *
     * @return array{score: int, reasons: string[]}
     */
    public function scoreSentence(
        string $sentence,
        string $word,
        ?string $sourceSession,
        string $sourceType,
        ?string $translation = null
    ): array {
        $score = 0;
        $reasons = [];

        // 1. Source Weight (Max 35)
        if ($sourceSession && preg_match('/^SEM[56]/i', $sourceSession)) {
            $score += 35;
            $reasons[] = 'Revision Week (SEM5/SEM6) [+35]';
        } elseif ($sourceType === 'slide') {
            $score += 25;
            $reasons[] = 'Presentation Slide [+25]';
        } elseif ($sourceType === 'generated') {
            $score += 22;
            $reasons[] = 'Curated Model Sentence [+22]';
        } else {
            $score += 15;
            $reasons[] = 'Textbook OCR [+15]';
        }

        // 2. Length (Max 15)
        $words = preg_split('/\s+/u', trim($sentence), -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = is_array($words) ? count($words) : 0;
        if ($wordCount >= 4 && $wordCount <= 8) {
            $score += 15;
            $reasons[] = 'Optimal primary length (' . $wordCount . ' words) [+15]';
        } elseif ($wordCount >= 3 && $wordCount <= 11) {
            $score += 10;
            $reasons[] = 'Acceptable length (' . $wordCount . ' words) [+10]';
        } else {
            $score += 5;
            $reasons[] = 'Long/complex length (' . $wordCount . ' words) [+5]';
        }

        // 3. Communicative & Persona Directness (Max 25)
        if (preg_match('/^(?:Je\s+m’appelle|Je\s+m\'appelle|J’ai|J\'ai|Mon\s+prénom|Mon\s+nom|Je\s+suis|Je\s+fais|Je\s+calcule|Je\s+résous|J’écris|J\'écris|Je\s+lis|J’apprends|J\'apprends)\b/ui', $sentence)) {
            $score += 25;
            $reasons[] = 'Direct 1st-person self-expression [+25]';
        } elseif (preg_match('/^(?:Tu\s+as\s+quel\s+âge|Tu\s+t’appelles|Tu\s+t\'appelles|Tu\s+as|Tu\s+fais|Tu\s+peux)\b/ui', $sentence)) {
            $score += 22;
            $reasons[] = 'Direct 2nd-person communicative address [+22]';
        } elseif (preg_match('/^[A-ZÀ-ÖØ-ß][a-zà-öø-ÿ]+\s+(?:a|est|s’appelle|s\'appelle|lit|écrit|fait|cherche|calcule|résout|mesure)\b/u', $sentence)) {
            $score += 20;
            $reasons[] = 'Named concrete subject [+20]';
        } elseif (preg_match('/^(?:L[\'’]élève|Le\s+maître|La\s+maîtresse|Le\s+professeur|L[\'’]enfant)\b/ui', $sentence)) {
            $score += 20;
            $reasons[] = 'School persona subject [+20]';
        } elseif (preg_match('/^(?:Il|Elle|Nous|On)\s+/ui', $sentence)) {
            $score += 15;
            $reasons[] = 'Pronoun subject [+15]';
        } else {
            $score += 10;
            $reasons[] = 'Declarative sentence [+10]';
        }

        // 4. Focus & Simplicity (Max 15)
        if (str_contains($sentence, ' et ') || str_contains($sentence, ' ; ') || str_contains($sentence, ' parce que ')) {
            $score += 8;
            $reasons[] = 'Compound sentence [+8]';
        } else {
            $score += 15;
            $reasons[] = 'Single focused clause [+15]';
        }

        // 5. Translation quality if available (Max 10)
        if (! empty($translation) && mb_strlen($translation) >= 3) {
            $score += 10;
            $reasons[] = 'Arabic translation verified [+10]';
        }

        return [
            'score' => min(100, $score),
            'reasons' => $reasons,
        ];
    }

    /**
     * Generate appropriate model sentences for vocabulary words when authentic sources lack enough candidates.
     *
     * @return string[]
     */
    public function generateAppropriateSentencesForVocab(VocabularyItem $vocab): array
    {
        $word = mb_strtolower(trim($vocab->word));
        $base = mb_strtolower(trim($vocab->base_word ?? ''));

        $curated = [
            'calculer' => [
                'Je calcule rapidement la somme des nombres de tête.',
                'L’élève calcule le résultat de l’opération sur son ardoise.',
            ],
            'mesurer' => [
                'L’élève mesure la longueur de la table avec une règle.',
                'En géométrie, je mesure les côtés du rectangle.',
            ],
            'une langue' => [
                'L’arabe et le français sont deux belles langues enseignées à l’école.',
                'J’apprends une nouvelle langue étrangère en classe.',
            ],
            'langue' => [
                'L’arabe et le français sont deux belles langues enseignées à l’école.',
                'J’apprends une nouvelle langue étrangère en classe.',
            ],
            'lire' => [
                'Chaque soir, je lis une histoire passionnante avant de dormir.',
                'Sami lit un livre de contes à haute voix.',
            ],
            'la solution' => [
                'L’élève a trouvé la bonne solution du problème.',
                'Le maître explique la solution de l’exercice au tableau.',
            ],
            'solution' => [
                'L’élève a trouvé la bonne solution du problème.',
                'Le maître explique la solution de l’exercice au tableau.',
            ],
            'une opération' => [
                'Je pose une opération d’addition sur mon cahier.',
                'Cette opération de calcul est très facile à résoudre.',
            ],
            'opération' => [
                'Je pose une opération d’addition sur mon cahier.',
                'Cette opération de calcul est très facile à résoudre.',
            ],
            'un problème' => [
                'Le maître pose un problème de mathématiques sur le tableau.',
                'Nous réfléchissons ensemble pour résoudre ce problème.',
            ],
            'problème' => [
                'Le maître pose un problème de mathématiques sur le tableau.',
                'Nous réfléchissons ensemble pour résoudre ce problème.',
            ],
            'résoudre' => [
                'L’élève résout l’exercice de calcul mental très rapidement.',
                'Nous apprenons à résoudre des problèmes difficiles en classe.',
            ],
            'difficile' => [
                'Cet exercice de géométrie n’est pas difficile.',
                'Le problème paraît difficile mais la solution est simple.',
            ],
            'facile' => [
                'Cette leçon de français est facile et amusante.',
                'L’exercice de lecture est très facile à faire.',
            ],
            'écrire' => [
                'L’élève écrit soigneusement la date et la leçon sur son cahier.',
                'J’écris un texte avec soin pour mon professeur.',
            ],
            'une olive' => [
                'Je mange une bonne olive noire.',
                'Il y a une olive verte dans l’assiette.',
            ],
            'olive' => [
                'Je mange une bonne olive noire.',
                'Il y a une olive verte dans l’assiette.',
            ],
            'une moto' => [
                'Papa conduit une belle moto rouge.',
                'La moto roule vite sur la route.',
            ],
            'moto' => [
                'Papa conduit une belle moto rouge.',
                'La moto roule vite sur la route.',
            ],
        ];

        if (isset($curated[$word])) {
            return $curated[$word];
        }
        if (isset($curated[$base])) {
            return $curated[$base];
        }

        return [];
    }
}
