<?php

namespace App\Services\Raiida;

use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\ConjugaisonGrade;
use App\Models\Raiida\ConjugaisonPeriod;
use App\Models\Raiida\ConjugaisonWeek;
use Illuminate\Support\Facades\File;

class ConjugaisonRawDataExtractor
{
    /**
     * French subject pronouns used to detect conjugated verb forms.
     */
    private const SUBJECT_PRONOUNS = [
        "j'", "je ", "tu ", "il ", "elle ", "on ",
        "nous ", "vous ", "ils ", "elles ",
    ];

    /**
     * Patterns that indicate teacher-only / navigation content to be EXCLUDED.
     */
    private const EXCLUDED_PATTERNS = [
        'réservé à l\'enseignant',
        'reserve a l\'enseignant',
        'pictogrammes',
        'plan de la séance',
        'plan de la seance',
        'contenu de la semaine',
        'organisation de la semaine',
        'l\'enseignant parle',
        'diffusion d\'un média',
        'diffusion d\'un media',
        'expliquer, donner une consigne',
        'média en cours',
        'media en cours',
        'désigner un élève',
        'designer un eleve',
        'fin du média',
        'fin du media',
        'passer au slide suivant',
        'il convient de lancer le mode diaporama',
        'lecture de la vidéo',
        'lecture de la video',
        'lecture de l\'audio',
        'lecture en cours',
        'dictée en cours',
        'dictee en cours',
        'séance 1', 'séance 2', 'séance 3', 'séance 4', 'séance 5', 'séance 6',
        'seance 1', 'seance 2', 'seance 3', 'seance 4', 'seance 5', 'seance 6',
        'partie 1', 'partie 2', 'partie 3', 'partie 4', 'partie 5',
        'etape 1', 'etape 2', 'etape 3',
        'remarque importante',
        'cette partie de la leçon',
        'cette partie de la lecon',
        'au terme de cette séance',
        'au terme de cette seance',
        'si ce taux n\'est pas atteint',
        'خاص بالأستاذ',
    ];

    /**
     * Words / phrases that strongly indicate conjugaison content.
     */
    private const CONJUGAISON_KEYWORDS = [
        'conjugaison',
        'conjuguer',
        'conjugue',
        'le verbe',
        'les verbes',
        'verbe «',
        'verbe "',
        'au présent',
        'au present',
        'au passé',
        'au passe',
        'au futur',
        'à l\'imparfait',
        'a l\'imparfait',
        'au conditionnel',
        'au subjonctif',
        'à l\'impératif',
        'a l\'imperatif',
    ];

    /**
     * Run extraction for a single scope (n, p, sem).
     *
     * @return array{items: list<array<string,mixed>>, summary: array<string,int>}
     */
    public function extract(string $n, string $p, string $sem): array
    {
        $sessions = $this->findSessionDirectories($n, $p, $sem);

        $summary = [
            'sessions_found' => count($sessions),
            'slides_scanned' => 0,
            'texts_scanned' => 0,
            'texts_matched' => 0,
        ];

        /** @var list<array<string,mixed>> $items */
        $items = [];

        foreach ($sessions as $sessionDir) {
            $sessionName = basename($sessionDir);
            $jsonPath = $sessionDir . DIRECTORY_SEPARATOR . 'data.json';

            if (! is_file($jsonPath)) {
                continue;
            }

            $payload = json_decode((string) file_get_contents($jsonPath), true);
            if (! is_array($payload)) {
                continue;
            }

            $slides = is_array($payload['slides'] ?? null) ? $payload['slides'] : [];

            foreach ($slides as $slideIndex => $slide) {
                if (! is_array($slide)) {
                    continue;
                }

                $summary['slides_scanned']++;
                $slideId = (int) ($slide['id'] ?? ($slideIndex + 1));
                $elements = is_array($slide['elements'] ?? null) ? $slide['elements'] : [];

                // Collect all text elements of this slide
                $slideTexts = [];
                foreach ($elements as $element) {
                    if (! is_array($element)) {
                        continue;
                    }
                    if (strtolower((string) ($element['type'] ?? '')) !== 'text') {
                        continue;
                    }
                    $content = $this->normalizeWhitespace((string) ($element['content'] ?? ''));
                    if ($content !== '') {
                        $slideTexts[] = $content;
                    }
                }

                if ($slideTexts === []) {
                    continue;
                }

                // Combine all slide text to check slide-level conjugaison context
                $combinedSlideText = implode(' ', $slideTexts);
                $normalizedCombined = $this->normalizeForMatching($combinedSlideText);

                // Check if this slide has any conjugaison relevance at all
                $slideHasConjugaisonContext = $this->hasConjugaisonContext($normalizedCombined);

                foreach ($slideTexts as $text) {
                    $summary['texts_scanned']++;
                    $normalized = $this->normalizeForMatching($text);

                    // Skip excluded content (teacher notes, navigation)
                    if ($this->isExcludedContent($normalized)) {
                        continue;
                    }

                    // Skip empty text
                    $wordCount = $this->wordCount($text);
                    if ($wordCount < 1) {
                        continue;
                    }

                    // Decide if this text is conjugaison-related
                    if ($this->isConjugaisonRelated($text, $normalized, $slideHasConjugaisonContext)) {
                        $summary['texts_matched']++;
                        $items[] = [
                            'session' => $sessionName,
                            'slide_id' => $slideId,
                            'text' => $text,
                            'type' => $this->classifyText($text, $normalized),
                        ];
                    }
                }
            }
        }

        return [
            'items' => $items,
            'summary' => $summary,
        ];
    }

    /**
     * Run extraction and persist results into the conjugaisons table.
     *
     * @return array{items: list<array<string,mixed>>, summary: array<string,int>, persisted: bool}
     */
    public function extractAndPersist(string $n, string $p, string $sem): array
    {
        $result = $this->extract($n, $p, $sem);
        $items = $result['items'];

        if ($items === []) {
            return array_merge($result, ['persisted' => false]);
        }

        // Build a clean raw text block: all extracted conjugaison sentences, deduplicated
        $rawLines = [];
        $seen = [];
        foreach ($items as $item) {
            $key = $this->normalizeForMatching($item['text']);
            if (! isset($seen[$key])) {
                $rawLines[] = $item['text'];
                $seen[$key] = true;
            }
        }

        $rawData = implode("\n", $rawLines);

        $relatedRawData = json_encode([
            'extraction_method' => 'comprehensive_raw_data_v1',
            'extracted_at' => now()->toIso8601String(),
            'sessions_scanned' => $result['summary']['sessions_found'],
            'total_items' => count($items),
            'unique_items' => count($rawLines),
            'items' => $items,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Ensure reference data exists
        $this->ensureReferenceRow($n, $p, $sem);

        // Update the conjugaisons row
        $entry = Conjugaison::query()->where('n', $n)->where('p', $p)->where('sem', $sem)->first();

        if ($entry !== null) {
            $entry->update([
                'raw_data' => $rawData,
                'related_raw_data' => $relatedRawData,
                'extraction_meta' => [
                    'method' => 'comprehensive_raw_data_v1',
                    'sessions_scanned' => $result['summary']['sessions_found'],
                    'total_items' => count($items),
                    'unique_items' => count($rawLines),
                ],
                'extracted_at' => now(),
            ]);
        }

        return array_merge($result, ['persisted' => true]);
    }

    /**
     * Check if text is conjugaison-related.
     */
    private function isConjugaisonRelated(string $text, string $normalized, bool $slideHasContext): bool
    {
        // Strong match: contains explicit conjugaison keywords
        if ($this->containsAny($normalized, self::CONJUGAISON_KEYWORDS)) {
            return true;
        }

        // Fill-in-the-blank pattern (underscores or dots indicating a blank)
        if ($this->hasFillInTheBlank($text)) {
            // Only include fill-in-blank if the slide has conjugaison context
            // or the sentence has pronoun + blank pattern
            if ($slideHasContext || $this->hasPronounBlankPattern($normalized)) {
                return true;
            }
        }

        // Sentence with conjugated verb form: pronoun + verb in a meaningful sentence
        if ($this->hasConjugatedVerbPattern($normalized)) {
            // Only include if the slide context is about conjugaison,
            // or if the sentence itself looks like a conjugaison exercise / answer
            if ($slideHasContext) {
                return true;
            }
        }

        // Exercise instruction mentioning verb completion
        if ($this->isVerbExerciseInstruction($normalized)) {
            return true;
        }

        // Sentence ordering exercise with conjugated verbs
        if ($this->isSentenceOrderingWithVerb($text, $normalized)) {
            if ($slideHasContext) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the text contains a fill-in-the-blank pattern.
     */
    private function hasFillInTheBlank(string $text): bool
    {
        // Multiple underscores
        if (preg_match('/_{3,}/', $text) === 1) {
            return true;
        }

        // Multiple dots used as placeholders
        if (preg_match('/\.{3,}|…{1,}/', $text) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Check if the text has a pronoun + blank pattern (je ___, nous ___, etc.)
     */
    private function hasPronounBlankPattern(string $normalized): bool
    {
        $pronounPatterns = [
            '/\bj\'\\s*_{3,}/',
            '/\bje\\s+_{3,}/',
            '/\btu\\s+_{3,}/',
            '/\bil\\s+_{3,}/',
            '/\belle\\s+_{3,}/',
            '/\bon\\s+_{3,}/',
            '/\bnous\\s+_{3,}/',
            '/\bvous\\s+_{3,}/',
            '/\bils\\s+_{3,}/',
            '/\belles\\s+_{3,}/',
            '/\bj\'\\s*…/',
            '/\bje\\s+…/',
            '/\bje\\s+\\.{3,}/',
        ];

        foreach ($pronounPatterns as $pattern) {
            if (preg_match($pattern, $normalized) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the text has a conjugated verb pattern (pronoun followed by a verb form).
     */
    private function hasConjugatedVerbPattern(string $normalized): bool
    {
        // Common French conjugation patterns: pronoun + verb form
        $patterns = [
            // Verb "apprendre" conjugations (main verb for N4/P1/SEM1)
            '/\bj\'apprends\b/',
            '/\btu apprends\b/',
            '/\bil apprend\b/',
            '/\belle apprend\b/',
            '/\bon apprend\b/',
            '/\bnous apprenons\b/',
            '/\bvous apprenez\b/',
            '/\bils apprennent\b/',
            '/\belles apprennent\b/',
            // Generalized: subject pronoun + common -er, -ir, -re verb endings
            '/\bj\'\\w+[eè]s?\b/',
            '/\bje\\s+\\w{3,}s?\b/',
            '/\btu\\s+\\w{3,}s\b/',
            '/\b(?:il|elle|on)\s+\w{3,}\b/',
            '/\bnous\\s+\\w{3,}ons\b/',
            '/\bvous\\s+\\w{3,}ez\b/',
            '/\bils\\s+\\w{3,}ent\b/',
            '/\belles\\s+\\w{3,}ent\b/',
            '/\b(?:il|elle|on|ils|elles)\s+s\'\w{3,}\b/',
        ];

        $matchCount = 0;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $normalized) === 1) {
                $matchCount++;
            }
        }

        // Need at least one explicit conjugated form
        return $matchCount > 0;
    }

    /**
     * Check if text is a verb exercise instruction.
     */
    private function isVerbExerciseInstruction(string $normalized): bool
    {
        return $this->containsAny($normalized, [
            'compléter la phrase avec le verbe',
            'completer la phrase avec le verbe',
            'la bonne forme du verbe',
            'forme correcte du verbe',
            'conjuguez le verbe',
            'conjugue le verbe',
            'conjuguer le verbe',
            'écrivez sur vos cahiers une phrase correcte',
            'ecrivez sur vos cahiers une phrase correcte',
            'qui veut compléter la phrase avec le verbe',
            'qui veut completer la phrase avec le verbe',
            'la conjugaison du verbe',
        ]);
    }

    /**
     * Check if text is a sentence ordering exercise involving verbs.
     */
    private function isSentenceOrderingWithVerb(string $text, string $normalized): bool
    {
        // Short single words that are conjugated verb forms (used in sentence ordering)
        $verbForms = [
            'apprend', 'apprends', 'apprenons', 'apprenez', 'apprennent',
            'fait', 'fais', 'faisons', 'faites', 'font',
            'aime', 'aimes', 'aimons', 'aimez', 'aiment',
        ];

        $wordCount = $this->wordCount($text);

        // A single or two-word fragment that's part of a reordering exercise
        if ($wordCount <= 3) {
            $lower = mb_strtolower(trim($text));
            foreach ($verbForms as $form) {
                if ($lower === $form) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the combined slide text has any conjugaison context.
     */
    private function hasConjugaisonContext(string $normalizedCombined): bool
    {
        return $this->containsAny($normalizedCombined, [
            'conjugaison',
            'conjuguer',
            'conjugue',
            'le verbe',
            'les verbes',
            'verbe «',
            'verbe "',
            'au présent',
            'au present',
            'au passé',
            'au passe',
            'au futur',
            'imparfait',
            'conditionnel',
            'compléter la phrase avec le verbe',
            'completer la phrase avec le verbe',
            'la bonne forme du verbe',
            'j\'apprends',
            'nous apprenons',
            'ils apprennent',
            'elles apprennent',
            'j\' _______',
            'j\'_______',
            'apprend ',
            'apprenons',
            'apprenez',
            'apprennent',
            'la fille apprend',
            'les enfants apprennent',
            'les élèves apprennent',
            'les eleves apprennent',
            'corrigez',
            's\'appelle',
            's\'appellent',
            'nous _______',
            'ils _______',
            'elles _______',
        ]);
    }

    /**
     * Classify the type of conjugaison content.
     */
    private function classifyText(string $text, string $normalized): string
    {
        if ($this->hasFillInTheBlank($text)) {
            return 'fill_in_blank';
        }

        if ($this->containsAny($normalized, ['conjugaison', 'conjuguer', 'conjugue', 'le verbe', 'les verbes'])) {
            if ($this->containsAny($normalized, ['utiliser', 'on va faire', 'nous allons', 'maintenant'])) {
                return 'lesson_objective';
            }

            return 'conjugaison_instruction';
        }

        if ($this->isVerbExerciseInstruction($normalized)) {
            return 'exercise_instruction';
        }

        if ($this->containsAny($normalized, ['corrigez', 'la bonne réponse', 'la bonne reponse'])) {
            return 'correction';
        }

        if ($this->hasConjugatedVerbPattern($normalized)) {
            return 'conjugated_sentence';
        }

        $wordCount = $this->wordCount($text);
        if ($wordCount <= 3) {
            return 'verb_fragment';
        }

        return 'contextual';
    }

    /**
     * Find all FR session directories for the given scope.
     *
     * @return list<string>
     */
    private function findSessionDirectories(string $n, string $p, string $sem): array
    {
        $gradeNum = (int) substr($n, 1);
        $periodNum = (int) substr($p, 1);
        $weekNum = (int) substr($sem, 3);

        $baseDir = storage_path('app/presentation_data');

        if (! is_dir($baseDir)) {
            return [];
        }

        // Build prefixes to match:
        // 1. Exact grade: FR_N4_P1_SEM1_S
        // 2. Multi-grade combos containing this grade: FR_N3&4_P1_SEM1_S
        $prefixes = [
            sprintf('FR_N%d_P%d_SEM%d_S', $gradeNum, $periodNum, $weekNum),
        ];

        // Add multi-grade prefix patterns where this grade could appear
        for ($other = 1; $other <= 6; $other++) {
            if ($other === $gradeNum) {
                continue;
            }
            $lo = min($gradeNum, $other);
            $hi = max($gradeNum, $other);
            $prefixes[] = sprintf('FR_N%d&%d_P%d_SEM%d_S', $lo, $hi, $periodNum, $weekNum);
        }

        $dirs = [];
        foreach (scandir($baseDir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (! is_dir($baseDir . DIRECTORY_SEPARATOR . $entry)) {
                continue;
            }

            foreach ($prefixes as $prefix) {
                if (str_starts_with($entry, $prefix)) {
                    $dirs[] = $baseDir . DIRECTORY_SEPARATOR . $entry;
                    break;
                }
            }
        }

        sort($dirs);

        return $dirs;
    }


    /**
     * Ensure the reference row exists in the conjugaisons table.
     */
    private function ensureReferenceRow(string $n, string $p, string $sem): void
    {
        $gradeNum = (int) substr($n, 1);
        $periodNum = (int) substr($p, 1);
        $weekNum = (int) substr($sem, 3);

        // Ensure grade/period/week reference rows
        $grade = ConjugaisonGrade::query()->updateOrCreate(
            ['grade_number' => $gradeNum],
            ['code' => $n, 'label' => 'Grade ' . $gradeNum]
        );

        $period = ConjugaisonPeriod::query()->updateOrCreate(
            ['period_number' => $periodNum],
            ['code' => $p, 'label' => 'Period ' . $periodNum]
        );

        $week = ConjugaisonWeek::query()->updateOrCreate(
            ['week_number' => $weekNum],
            ['code' => $sem, 'label' => 'Week ' . $weekNum]
        );

        // Ensure conjugaison row exists
        Conjugaison::query()->firstOrCreate(
            ['n' => $n, 'p' => $p, 'sem' => $sem],
            [
                'grade_id' => $grade->id,
                'period_id' => $period->id,
                'week_id' => $week->id,
                'week' => $weekNum,
                'raw_data' => '',
                'name' => '',
                'question' => '',
            ]
        );
    }

    /**
     * Check if text is excluded content (teacher notes, navigation, etc.)
     */
    private function isExcludedContent(string $normalized): bool
    {
        // Exact match on short markers
        if (in_array($normalized, ['?', '??', '???'], true)) {
            return true;
        }

        return $this->containsAny($normalized, self::EXCLUDED_PATTERNS);
    }

    private function normalizeWhitespace(string $value): string
    {
        $value = str_replace(["\u{00A0}", "\u{202F}"], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim((string) $value);
    }

    private function normalizeForMatching(string $value): string
    {
        $value = str_replace(["\u{2019}", "\u{2018}", '`'], "'", $value);
        $value = str_replace(["\u{201C}", "\u{201D}", '«', '»'], '"', $value);
        $value = mb_strtolower($value);

        return $this->normalizeWhitespace($value);
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function wordCount(string $value): int
    {
        $parts = preg_split('/\s+/u', trim($value));

        return $parts === false ? 0 : count(array_filter($parts, static fn (string $part): bool => $part !== ''));
    }
}
