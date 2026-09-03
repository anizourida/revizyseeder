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
        'description de la scène',
        'graphème',
        'phonème',
        'lecture du graphème',
        'écriture du graphème',
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

        foreach ($grouped as $groupKey => $groupVocabs) {
            [$grade, $period, $week] = explode('|', $groupKey);
            $presentationTexts = $this->collectPresentationTextsForWeek($grade, $period, $week);
            $ocrTexts = $this->collectOcrTextsForWeek($grade, $period, $week);

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

        if (empty($candidates)) {
            // Save placeholder record so vocabulary is copied and accounted for
            VocabularySentence::create([
                'vocabulary_item_id' => $vocab->id,
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

        $created = 0;
        foreach ($candidates as $cand) {
            VocabularySentence::create([
                'vocabulary_item_id' => $vocab->id,
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
     * Search candidate texts for full sentences containing the vocabulary word or base word.
     *
     * @return array<int, array{sentence: string, session?: string, slide?: int, type: string}>
     */
    public function findSentencesForWord(
        VocabularyItem $vocab,
        array $presentationTexts,
        array $ocrTexts
    ): array {
        $word = trim($vocab->word);
        $baseWord = trim($vocab->base_word ?? '');
        $searchTerms = array_values(array_unique(array_filter([$word, $baseWord])));

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
        string $sentence,
        array $searchTerms,
        string $fullWord,
        string $baseWord
    ): bool {
        $sentence = trim($sentence);
        if ($sentence === '') {
            return false;
        }

        $lowerSentence = mb_strtolower($sentence);

        // Filter blacklisted phrases / teacher instructions
        foreach ($this->blacklistedPhrases as $blacklisted) {
            if (str_contains($lowerSentence, $blacklisted)) {
                return false;
            }
        }

        // Filter out fill-in-the-blank questions like "Le garçon donne un _____ à son ______"
        if (str_contains($sentence, '___') || str_contains($sentence, '...')) {
            return false;
        }

        // Filter out syllable breakdowns or arrows (e.g., "t eau > teau > bateau")
        if (str_contains($sentence, '>') || str_contains($sentence, '->') || str_contains($sentence, '<')) {
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

        // Must contain at least 3 words
        $words = preg_split('/\s+/u', $sentence, -1, PREG_SPLIT_NO_EMPTY);
        if (! is_array($words) || count($words) < 3) {
            return false;
        }

        // Check if exactly equals the vocab word itself
        if (mb_strtolower(trim($sentence, " .!?:;\"'")) === mb_strtolower(trim($fullWord))) {
            return false;
        }

        // Check if any search term matches as a word boundary
        $matchesTerm = false;
        foreach ($searchTerms as $term) {
            if (mb_strlen($term) < 2) {
                continue;
            }

            // Word boundary regex that accounts for French letters and apostrophes
            $pattern = '/(?:\b|[\'\’])' . preg_quote($term, '/') . '(?:\b|s\b|es\b)/iu';
            if (preg_match($pattern, $sentence)) {
                $matchesTerm = true;
                break;
            }
        }

        return $matchesTerm;
    }

    /**
     * Collect all slide texts for a given grade, period, and week across all sessions.
     *
     * @return array<int, array{session: string, slide_id: int, text: string}>
     */
    protected function collectPresentationTextsForWeek(string $grade, string $period, string $week): array
    {
        $gradeNorm = str_ireplace('N', '', $grade);
        $periodNorm = str_ireplace('P', '', $period);
        $weekNorm = str_ireplace('SEM', '', $week);

        // Pattern matching directories like FR_N2_P1_SEM1_*
        $dirPatterns = [
            storage_path("app/presentation_data/FR_N{$gradeNorm}_P{$periodNorm}_SEM{$weekNorm}_*/data.json"),
            storage_path("app/presentation_data/FR_N{$gradeNorm}_P{$periodNorm}_S{$weekNorm}_*/data.json"),
            storage_path("app/presentation_data/FR_N{$gradeNorm}_P{$periodNorm}_semaine_{$weekNorm}_*/data.json"),
        ];

        $files = [];
        foreach ($dirPatterns as $pattern) {
            $matched = glob($pattern);
            if ($matched) {
                $files = array_merge($files, $matched);
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
     * Collect OCR texts from Page and BookPage for the week.
     *
     * @return array<int, array{page_number: int, text: string}>
     */
    protected function collectOcrTextsForWeek(string $grade, string $period, string $week): array
    {
        $gradeNorm = str_ireplace('N', '', $grade);
        $periodNorm = str_ireplace('P', '', $period);
        $weekNorm = str_ireplace('SEM', '', $week);
        $key = "FR_N{$gradeNorm}_P{$periodNorm}_SEM{$weekNorm}";

        $results = [];

        try {
            $pages = Page::where('n_p_sem', 'like', "{$key}%")
                ->where(function ($q) {
                    $q->whereNotNull('ocr_text')->orWhereNotNull('raw_text');
                })
                ->get(['page_number', 'ocr_text', 'raw_text']);

            foreach ($pages as $p) {
                $text = trim((string) ($p->ocr_text ?: $p->raw_text));
                if ($text !== '') {
                    $results[] = [
                        'page_number' => (int) $p->page_number,
                        'text' => $text,
                    ];
                }
            }
        } catch (Throwable $e) {
            // Ignore if columns missing or query fails
        }

        return $results;
    }

    /**
     * Split text block into individual sentences.
     *
     * @return string[]
     */
    protected function splitIntoSentences(string $text): array
    {
        // Split by lines or sentence end markers (. ! ?)
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

            // If line contains multiple punctuation-terminated sentences
            $parts = preg_split('/(?<=[.!?])\s+(?=[A-ZÀ-ÖØ-ß])/u', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (! is_array($parts)) {
                $parts = [$line];
            }
            foreach ($parts as $part) {
                $cleaned = trim($part, " \t\n\r\0\x0B\"'«»");
                if ($cleaned !== '') {
                    $sentences[] = $cleaned;
                }
            }
        }

        return array_values(array_unique($sentences));
    }
}
