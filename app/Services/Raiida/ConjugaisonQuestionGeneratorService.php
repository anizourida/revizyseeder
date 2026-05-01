<?php

namespace App\Services\Raiida;

use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Page;
use Illuminate\Support\Str;

class ConjugaisonQuestionGeneratorService
{
    private const MAX_SOURCE_LINES = 80;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function generateForConjugaison(Conjugaison $conjugaison, int $min = 4, int $max = 8): array
    {
        [$min, $max] = $this->normalizeBounds($min, $max);

        $sourceLines = $this->collectSourceLines($conjugaison);

        $questions = [];

        $questions = array_merge($questions, $this->buildSentenceSelectionQuestions($conjugaison, $sourceLines, $max));

        $verbQuestion = $this->buildVerbQuestion($conjugaison, $sourceLines);
        if ($verbQuestion !== null) {
            $questions[] = $verbQuestion;
        }

        $tenseQuestion = $this->buildTenseQuestion($conjugaison);
        if ($tenseQuestion !== null) {
            $questions[] = $tenseQuestion;
        }

        $clozeQuestion = $this->buildVerbClozeQuestion($conjugaison, $sourceLines);
        if ($clozeQuestion !== null) {
            $questions[] = $clozeQuestion;
        }

        $questions = $this->dedupeQuestions($questions);

        if (count($questions) < $min) {
            $questions = array_merge($questions, $this->buildFallbackQuestions($conjugaison, $sourceLines, $min - count($questions)));
            $questions = $this->dedupeQuestions($questions);
        }

        if (count($questions) > $max) {
            $questions = array_slice($questions, 0, $max);
        }

        while (count($questions) < $min) {
            $questions[] = $this->buildMinimalFallbackQuestion($conjugaison, count($questions) + 1);
        }

        return array_values($questions);
    }

    /**
     * @return array{0:int, 1:int}
     */
    private function normalizeBounds(int $min, int $max): array
    {
        $min = max(1, min(8, $min));
        $max = max(1, min(8, $max));

        if ($min > $max) {
            $max = $min;
        }

        return [$min, $max];
    }

    /**
     * @return array<int, array{text:string, source:string, score:int}>
     */
    private function collectSourceLines(Conjugaison $conjugaison): array
    {
        $lines = [];

        $this->pushLines($lines, $this->extractInlineConjugaisonLines($conjugaison), 'conjugaison_record', 95);
        $this->pushLines($lines, $this->extractRelatedRawDataLines($conjugaison), 'related_raw_data', 85);
        $this->pushLines($lines, $this->extractPowerPointLines($conjugaison), 'teacher_lesson_ppt', 75);
        $this->pushLines($lines, $this->extractLivretLines($conjugaison), 'livret_page', 65);

        usort($lines, static fn (array $a, array $b): int => ($b['score'] <=> $a['score']) ?: (mb_strlen($b['text'], 'UTF-8') <=> mb_strlen($a['text'], 'UTF-8')));

        $deduped = [];
        $seen = [];

        foreach ($lines as $line) {
            $text = (string) ($line['text'] ?? '');
            $signature = $this->lineSignature($text);
            if ($signature === '' || isset($seen[$signature])) {
                continue;
            }

            $seen[$signature] = true;
            $deduped[] = [
                'text' => $text,
                'source' => (string) ($line['source'] ?? ''),
                'score' => (int) ($line['score'] ?? 0),
            ];

            if (count($deduped) >= self::MAX_SOURCE_LINES) {
                break;
            }
        }

        return $deduped;
    }

    /**
     * @param  array<int, array{text:string, source:string, score:int}>  $bucket
     * @param  array<int, string>  $lines
     */
    private function pushLines(array &$bucket, array $lines, string $source, int $baseScore): void
    {
        foreach ($lines as $index => $line) {
            $clean = $this->sanitizeLine($line);
            if ($clean === '') {
                continue;
            }

            $score = $baseScore - min(30, $index);
            if ($this->isConjugaisonFocusedLine($clean)) {
                $score += 12;
            }

            $bucket[] = [
                'text' => $clean,
                'source' => $source,
                'score' => $score,
            ];
        }
    }

    /**
     * @return array<int, string>
     */
    private function extractInlineConjugaisonLines(Conjugaison $conjugaison): array
    {
        $lines = [];

        if (is_string($conjugaison->question) && trim($conjugaison->question) !== '') {
            $lines = array_merge($lines, $this->splitTextLines($conjugaison->question));
        }

        if (is_string($conjugaison->raw_data) && trim($conjugaison->raw_data) !== '') {
            $lines = array_merge($lines, $this->splitTextLines($conjugaison->raw_data));
        }

        if (is_string($conjugaison->name) && trim($conjugaison->name) !== '') {
            $lines = array_merge($lines, $this->splitTextLines($conjugaison->name));
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function extractRelatedRawDataLines(Conjugaison $conjugaison): array
    {
        if (! is_string($conjugaison->related_raw_data) || trim($conjugaison->related_raw_data) === '') {
            return [];
        }

        $decoded = json_decode($conjugaison->related_raw_data, true);
        if (! is_array($decoded)) {
            return $this->splitTextLines($conjugaison->related_raw_data);
        }

        $lines = [];

        foreach ((array) ($decoded['top_questions'] ?? []) as $row) {
            if (! is_array($row)) {
                continue;
            }

            $lines = array_merge($lines, $this->splitTextLines((string) ($row['question'] ?? '')));
        }

        foreach ((array) ($decoded['top_examples'] ?? []) as $row) {
            if (! is_array($row)) {
                continue;
            }

            $lines = array_merge($lines, $this->splitTextLines((string) ($row['sentence'] ?? '')));
        }

        foreach ((array) ($decoded['top_candidates'] ?? []) as $row) {
            if (! is_array($row)) {
                continue;
            }

            $lines = array_merge($lines, $this->splitTextLines((string) ($row['raw_data'] ?? '')));
            $lines = array_merge($lines, $this->splitTextLines((string) ($row['name'] ?? '')));
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    private function extractPowerPointLines(Conjugaison $conjugaison): array
    {
        $assets = $this->resolveRelevantLessonAssets($conjugaison);
        if ($assets === []) {
            return [];
        }

        $lines = [];
        $targetSlide = (int) ($conjugaison->source_slide_id ?? 0);

        foreach ($assets as $asset) {
            $jsonPath = $this->resolveExistingPath((string) ($asset->presentation_json_path ?? ''));
            if ($jsonPath === null || ! is_file($jsonPath)) {
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

                $slideId = (int) ($slide['id'] ?? ($slideIndex + 1));
                $slideBoost = $this->slideDistanceBoost($targetSlide, $slideId);

                foreach ((array) ($slide['elements'] ?? []) as $element) {
                    if (! is_array($element) || strtolower((string) ($element['type'] ?? '')) !== 'text') {
                        continue;
                    }

                    $content = (string) ($element['content'] ?? '');
                    foreach ($this->splitTextLines($content) as $line) {
                        $line = $this->sanitizeLine($line);
                        if ($line === '') {
                            continue;
                        }

                        if (! $this->isConjugaisonFocusedLine($line) && ! $this->looksLikeClassroomPrompt($line)) {
                            continue;
                        }

                        $prioritized[] = [
                            'text' => $line,
                            'boost' => $slideBoost,
                        ];
                    }
                }
            }
        }

        usort($prioritized, static fn (array $a, array $b): int => ((int) ($b['boost'] ?? 0) <=> (int) ($a['boost'] ?? 0)));

        return array_values(array_map(static fn (array $row): string => (string) ($row['text'] ?? ''), $prioritized));
    }

    /**
     * @return array<int, string>
     */
    private function extractLivretLines(Conjugaison $conjugaison): array
    {
        $scopePrefix = 'FR_' . trim((string) $conjugaison->n) . '_' . trim((string) $conjugaison->p) . '_' . trim((string) $conjugaison->sem);

        $pages = Page::query()
            ->where('n_p_sem', 'like', $scopePrefix . '%')
            ->where(function ($query): void {
                $query->whereNotNull('ocr_chandra_path')
                    ->orWhereNotNull('ocr_olmocr_path')
                    ->orWhereNotNull('ocr_full_text_path');
            })
            ->orderBy('page_number')
            ->limit(18)
            ->get(['ocr_chandra_path', 'ocr_olmocr_path', 'ocr_full_text_path']);

        $lines = [];

        foreach ($pages as $page) {
            $candidatePaths = [
                (string) ($page->ocr_chandra_path ?? ''),
                (string) ($page->ocr_olmocr_path ?? ''),
                (string) ($page->ocr_full_text_path ?? ''),
            ];

            foreach ($candidatePaths as $candidatePath) {
                $resolved = $this->resolveExistingPath($candidatePath);
                if ($resolved === null || ! is_file($resolved)) {
                    continue;
                }

                $content = (string) file_get_contents($resolved);
                foreach ($this->splitTextLines($content) as $line) {
                    $line = $this->sanitizeLine($line);
                    if ($line === '') {
                        continue;
                    }

                    if (! $this->isConjugaisonFocusedLine($line) && ! $this->looksLikeClassroomPrompt($line)) {
                        continue;
                    }

                    $lines[] = $line;
                }

                break;
            }
        }

        return $lines;
    }

    /**
     * @return array<int, FileAsset>
     */
    private function resolveRelevantLessonAssets(Conjugaison $conjugaison): array
    {
        $assets = [];
        $seenIds = [];

        $pushAsset = static function (?FileAsset $asset) use (&$assets, &$seenIds): void {
            if (! $asset instanceof FileAsset) {
                return;
            }

            if (isset($seenIds[$asset->id])) {
                return;
            }

            $seenIds[$asset->id] = true;
            $assets[] = $asset;
        };

        if (! empty($conjugaison->source_file_asset_id)) {
            $pushAsset(FileAsset::query()->find((int) $conjugaison->source_file_asset_id));
        }

        $sourceLessonId = trim((string) ($conjugaison->source_lesson_id ?? ''));
        if ($sourceLessonId !== '') {
            $exactMatches = FileAsset::query()
                ->where('filename', 'like', $sourceLessonId . '%')
                ->limit(4)
                ->get();

            foreach ($exactMatches as $asset) {
                $pushAsset($asset);
            }
        }

        $scopeMatches = FileAsset::query()
            ->where('filename', 'like', 'FR_%_' . $conjugaison->p . '_' . $conjugaison->sem . '_S%')
            ->orderBy('id')
            ->limit(18)
            ->get();

        $gradeNumber = (int) Str::of((string) $conjugaison->n)->replace('N', '')->toString();

        foreach ($scopeMatches as $asset) {
            $lessonId = pathinfo((string) $asset->filename, PATHINFO_FILENAME);
            if (! $this->lessonIdMatchesGrade($lessonId, $gradeNumber)) {
                continue;
            }

            $pushAsset($asset);
        }

        return $assets;
    }

    private function lessonIdMatchesGrade(string $lessonId, int $gradeNumber): bool
    {
        if ($gradeNumber < 1) {
            return true;
        }

        if (preg_match('/_N([1-6](?:&[1-6])?)_P[1-5]_SEM[1-6]_S[1-6]$/i', $lessonId, $matches) !== 1) {
            return false;
        }

        $token = (string) ($matches[1] ?? '');
        $grades = array_filter(array_map(static fn (string $part): int => (int) trim($part), explode('&', $token)), static fn (int $value): bool => $value >= 1 && $value <= 6);

        return in_array($gradeNumber, $grades, true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildSentenceSelectionQuestions(Conjugaison $conjugaison, array $sourceLines, int $max): array
    {
        if ($sourceLines === []) {
            return [];
        }

        $questions = [];
        $anchors = array_slice($sourceLines, 0, min(6, count($sourceLines)));

        foreach ($anchors as $index => $anchor) {
            if (count($questions) >= $max) {
                break;
            }

            $correct = (string) ($anchor['text'] ?? '');
            $distractors = [];

            foreach ($sourceLines as $candidate) {
                $candidateText = (string) ($candidate['text'] ?? '');
                if ($candidateText === '' || $candidateText === $correct) {
                    continue;
                }

                if (similar_text($this->lineSignature($candidateText), $this->lineSignature($correct)) > 70) {
                    continue;
                }

                $distractors[] = $candidateText;
                if (count($distractors) >= 2) {
                    break;
                }
            }

            if (count($distractors) < 2) {
                continue;
            }

            $answers = [
                $this->textAnswer($correct, true),
                $this->textAnswer($distractors[0], false),
                $this->textAnswer($distractors[1], false),
            ];

            $questions[] = $this->buildUniversalQuestion(
                $conjugaison,
                'Conjugaison - Phrase vue en classe ' . ($index + 1),
                $this->buildLessonContextBody($conjugaison),
                $answers,
                'أختار الجملة الصحيحة.'
            );
        }

        return $questions;
    }

    /**
     * @param  array<int, array{text:string, source:string, score:int}>  $sourceLines
     * @return array<string, mixed>|null
     */
    private function buildVerbQuestion(Conjugaison $conjugaison, array $sourceLines): ?array
    {
        $verb = trim((string) ($conjugaison->verbe ?? ''));
        if ($verb === '') {
            return null;
        }

        $distractors = Conjugaison::query()
            ->where('n', (string) $conjugaison->n)
            ->whereNotNull('verbe')
            ->where('verbe', '!=', '')
            ->where('verbe', '!=', $verb)
            ->orderBy('id')
            ->limit(6)
            ->pluck('verbe')
            ->map(static fn ($value): string => trim((string) $value))
            ->filter(static fn (string $value): bool => $value !== '')
            ->unique()
            ->values()
            ->all();

        if (count($distractors) < 2) {
            $distractors = array_values(array_unique(array_filter(array_merge($distractors, [
                'etre',
                'avoir',
                'aller',
                'faire',
                'prendre',
            ]), static fn (string $value): bool => mb_strtolower($value, 'UTF-8') !== mb_strtolower($verb, 'UTF-8'))));
        }

        if (count($distractors) < 2) {
            return null;
        }

        $contextLine = $this->pickContextLineForVerb($sourceLines, $verb);

        $answers = [
            $this->textAnswer($verb, true),
            $this->textAnswer((string) $distractors[0], false),
            $this->textAnswer((string) $distractors[1], false),
        ];

        return $this->buildUniversalQuestion(
            $conjugaison,
            'Conjugaison - Verbe de la lecon',
            ($contextLine !== '' ? $contextLine . "\n\n" : '') . 'Quel verbe est travaille dans cette lecon ?',
            $answers,
            'أختار الكلمة المناسبة.'
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildTenseQuestion(Conjugaison $conjugaison): ?array
    {
        $tense = trim((string) ($conjugaison->tense ?? ''));
        if ($tense === '') {
            return null;
        }

        $pool = [
            'present',
            'passe compose',
            'imparfait',
            'futur',
            'futur simple',
            'conditionnel present',
            'imperatif',
        ];

        $fromDb = Conjugaison::query()
            ->whereNotNull('tense')
            ->where('tense', '!=', '')
            ->where('tense', '!=', $tense)
            ->orderBy('id')
            ->limit(10)
            ->pluck('tense')
            ->map(static fn ($value): string => trim((string) $value))
            ->filter(static fn (string $value): bool => $value !== '')
            ->values()
            ->all();

        $distractors = [];
        foreach (array_merge($fromDb, $pool) as $candidate) {
            if (mb_strtolower($candidate, 'UTF-8') === mb_strtolower($tense, 'UTF-8')) {
                continue;
            }
            if (in_array($candidate, $distractors, true)) {
                continue;
            }
            $distractors[] = $candidate;
            if (count($distractors) >= 2) {
                break;
            }
        }

        if (count($distractors) < 2) {
            return null;
        }

        return $this->buildUniversalQuestion(
            $conjugaison,
            'Conjugaison - Temps de la lecon',
            'A quel temps travaille-t-on le verbe « ' . trim((string) ($conjugaison->verbe ?? '')) . ' » ?',
            [
                $this->textAnswer($tense, true),
                $this->textAnswer($distractors[0], false),
                $this->textAnswer($distractors[1], false),
            ],
            'أختار الكلمة المناسبة.'
        );
    }

    /**
     * @param  array<int, array{text:string, source:string, score:int}>  $sourceLines
     * @return array<string, mixed>|null
     */
    private function buildVerbClozeQuestion(Conjugaison $conjugaison, array $sourceLines): ?array
    {
        $verb = trim((string) ($conjugaison->verbe ?? ''));
        if ($verb === '') {
            return null;
        }

        $targetLine = '';
        foreach ($sourceLines as $row) {
            $line = (string) ($row['text'] ?? '');
            if ($line === '') {
                continue;
            }

            if (preg_match('/\b' . preg_quote($verb, '/') . '\b/ui', $line) === 1) {
                $targetLine = $line;
                break;
            }

            if (preg_match('/\bverbe\b/ui', $line) === 1 && preg_match('/\bconjug/ui', $line) === 1) {
                $targetLine = $line;
                break;
            }
        }

        if ($targetLine === '') {
            return null;
        }

        $masked = preg_replace('/\b' . preg_quote($verb, '/') . '\b/ui', '_____', $targetLine, 1);
        if (! is_string($masked) || $masked === $targetLine) {
            $masked = $targetLine . ' (_____ )';
        }

        $verbDistractors = Conjugaison::query()
            ->where('n', (string) $conjugaison->n)
            ->whereNotNull('verbe')
            ->where('verbe', '!=', '')
            ->where('verbe', '!=', $verb)
            ->orderBy('id')
            ->limit(8)
            ->pluck('verbe')
            ->map(static fn ($value): string => trim((string) $value))
            ->filter(static fn (string $value): bool => $value !== '')
            ->unique()
            ->values()
            ->all();

        if (count($verbDistractors) < 2) {
            return null;
        }

        return $this->buildUniversalQuestion(
            $conjugaison,
            'Conjugaison - Complete avec le verbe correct',
            $masked,
            [
                $this->textAnswer($verb, true),
                $this->textAnswer($verbDistractors[0], false),
                $this->textAnswer($verbDistractors[1], false),
            ],
            'أختار الكلمة المناسبة.'
        );
    }

    /**
     * @param  array<int, array{text:string, source:string, score:int}>  $sourceLines
     * @return array<int, array<string, mixed>>
     */
    private function buildFallbackQuestions(Conjugaison $conjugaison, array $sourceLines, int $missing): array
    {
        $questions = [];

        for ($i = 0; $i < $missing; $i++) {
            $line = (string) ($sourceLines[$i]['text'] ?? $this->buildLessonContextBody($conjugaison));

            $answers = [
                $this->textAnswer($line, true),
                $this->textAnswer('Je complete la conjugaison au tableau.', false),
                $this->textAnswer('Je relis la lecon avec mon enseignant.', false),
            ];

            $questions[] = $this->buildUniversalQuestion(
                $conjugaison,
                'Conjugaison - Souvenir de classe ' . ($i + 1),
                'Choisis l\'enonce travaille en classe.',
                $answers,
                'أختار الجملة الصحيحة.'
            );
        }

        return $questions;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMinimalFallbackQuestion(Conjugaison $conjugaison, int $index): array
    {
        $verb = trim((string) ($conjugaison->verbe ?? ''));
        $tense = trim((string) ($conjugaison->tense ?? ''));

        $body = 'Lecon: ' . trim((string) $conjugaison->n) . ' / ' . trim((string) $conjugaison->p) . ' / ' . trim((string) $conjugaison->sem);
        if ($verb !== '') {
            $body .= ' - verbe ' . $verb;
        }
        if ($tense !== '') {
            $body .= ' au ' . $tense;
        }

        return $this->buildUniversalQuestion(
            $conjugaison,
            'Conjugaison - Question auto ' . $index,
            $body,
            [
                $this->textAnswer('Je conjugue correctement le verbe de la lecon.', true),
                $this->textAnswer('Je choisis un verbe au hasard.', false),
                $this->textAnswer('Je n\'utilise pas le temps demande.', false),
            ],
            'أختار الجملة الصحيحة.'
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @return array<int, array<string, mixed>>
     */
    private function dedupeQuestions(array $questions): array
    {
        $unique = [];
        $seen = [];

        foreach ($questions as $question) {
            $data = is_array($question['data'] ?? null) ? $question['data'] : [];
            $body = (string) ($data['body'] ?? '');
            $answers = [];

            foreach ((array) ($data['answers'] ?? []) as $answer) {
                if (! is_array($answer)) {
                    continue;
                }

                $answers[] = strtolower(trim((string) ($answer['body'] ?? ''))) . '|' . (((bool) ($answer['is_correct'] ?? false)) ? '1' : '0');
            }

            sort($answers);
            $signature = md5(strtolower(trim((string) ($question['name'] ?? ''))) . '|' . strtolower(trim($body)) . '|' . implode('::', $answers));

            if (isset($seen[$signature])) {
                continue;
            }

            $seen[$signature] = true;
            $unique[] = $question;
        }

        return $unique;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildUniversalQuestion(
        Conjugaison $conjugaison,
        string $name,
        string $body,
        array $answers,
        string $instruction
    ): array {
        return [
            'concept_id' => $conjugaison->concept_id !== null && trim((string) $conjugaison->concept_id) !== ''
                ? (string) $conjugaison->concept_id
                : null,
            'name' => $name,
            'type' => 'universal',
            'data' => [
                'instruction' => $instruction,
                'body' => trim($body),
                'media' => ['image' => null, 'audio' => null],
                'answers' => array_values($answers),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function textAnswer(string $text, bool $correct): array
    {
        return [
            'body' => trim($text),
            'is_correct' => $correct,
            'media' => ['image' => null, 'audio' => null],
        ];
    }

    private function buildLessonContextBody(Conjugaison $conjugaison): string
    {
        $parts = [
            trim((string) $conjugaison->n),
            trim((string) $conjugaison->p),
            trim((string) $conjugaison->sem),
        ];

        $parts = array_values(array_filter($parts, static fn (string $value): bool => $value !== ''));

        $body = 'Choisis la phrase travaillee en classe';
        if ($parts !== []) {
            $body .= ' (' . implode(' / ', $parts) . ')';
        }

        $verb = trim((string) ($conjugaison->verbe ?? ''));
        $tense = trim((string) ($conjugaison->tense ?? ''));

        if ($verb !== '' || $tense !== '') {
            $body .= ' - ';
            if ($verb !== '') {
                $body .= 'verbe ' . $verb;
            }
            if ($tense !== '') {
                $body .= ($verb !== '' ? ' au ' : '') . $tense;
            }
        }

        return $body;
    }

    private function pickContextLineForVerb(array $sourceLines, string $verb): string
    {
        foreach ($sourceLines as $row) {
            $line = (string) ($row['text'] ?? '');
            if ($line === '') {
                continue;
            }

            if (preg_match('/\b' . preg_quote($verb, '/') . '\b/ui', $line) === 1 || preg_match('/\bverbe\b/ui', $line) === 1) {
                return $line;
            }
        }

        return '';
    }

    private function lineSignature(string $line): string
    {
        $line = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $line = preg_replace('/\s+/u', ' ', trim($line)) ?? '';

        return mb_strtolower($line, 'UTF-8');
    }

    private function sanitizeLine(string $line): string
    {
        $line = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $line = str_replace(["\r", "\t"], [' ', ' '], $line);
        $line = preg_replace('/\s+/u', ' ', $line) ?? '';
        $line = trim($line, " \n\r\t\v\0-–•:;");

        if ($line === '') {
            return '';
        }

        if (mb_strlen($line, 'UTF-8') < 6 || mb_strlen($line, 'UTF-8') > 240) {
            return '';
        }

        if (preg_match('/^[\d\W_]+$/u', $line) === 1) {
            return '';
        }

        return $line;
    }

    /**
     * @return array<int, string>
     */
    private function splitTextLines(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $parts = preg_split('/\n+/u', $text) ?: [];

        $lines = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part !== '') {
                $lines[] = $part;
            }
        }

        return $lines;
    }

    private function isConjugaisonFocusedLine(string $line): bool
    {
        $normalized = mb_strtolower($line, 'UTF-8');

        $keywords = [
            'conjug',
            'verbe',
            'temps',
            'present',
            'futur',
            'conditionnel',
            'imparfait',
            'passe',
            'je ',
            "j'",
            'tu ',
            'il ',
            'elle ',
            'nous ',
            'vous ',
            'ils ',
            'elles ',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function looksLikeClassroomPrompt(string $line): bool
    {
        $normalized = mb_strtolower($line, 'UTF-8');

        $markers = [
            'qui veut',
            'complete',
            'compl',
            'levez la main',
            'choisis',
            'choisir',
            'ecris',
            'ecoute',
            'lis',
            'travail',
            'activite',
        ];

        foreach ($markers as $marker) {
            if (str_contains($normalized, $marker)) {
                return true;
            }
        }

        return false;
    }

    private function slideDistanceBoost(int $targetSlide, int $currentSlide): int
    {
        if ($targetSlide <= 0 || $currentSlide <= 0) {
            return 0;
        }

        $distance = abs($targetSlide - $currentSlide);

        return match (true) {
            $distance === 0 => 10,
            $distance <= 1 => 8,
            $distance <= 2 => 6,
            $distance <= 4 => 4,
            default => 0,
        };
    }

    private function resolveExistingPath(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        $candidates = [];

        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            $candidates[] = $path;
        } else {
            $candidates[] = base_path($path);
            $candidates[] = storage_path('app/' . ltrim($path, '/'));

            if (str_starts_with($path, 'storage/app/')) {
                $candidates[] = base_path($path);
            }
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
