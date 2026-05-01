<?php

namespace App\Services\Raiida;

use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\ConjugaisonGrade;
use App\Models\Raiida\ConjugaisonPeriod;
use App\Models\Raiida\ConjugaisonWeek;
use App\Models\Raiida\FileAsset;
use Illuminate\Support\Facades\DB;

class ConjugaisonExtractionService
{
    private const EMPTY_VALUE = '';

    public function __construct(
        private readonly ConjugaisonTextAnalyzer $analyzer
    ) {
    }

    /**
     * @return array<string, int>
     */
    public function run(bool $force = false): array
    {
        $this->ensureReferenceData();

        $coverageScopes = $this->buildCoverageScopes();
        $coverageKeys = collect($coverageScopes)->map(static fn (array $scope): string => $scope['n'] . '|' . $scope['p'] . '|' . $scope['sem'])->all();

        $summary = [
            'assets_scanned' => 0,
            'assets_qualified' => 0,
            'assets_with_json' => 0,
            'candidates_found' => 0,
            'question_candidates_found' => 0,
            'example_candidates_found' => 0,
            'keys_detected' => 0,
            'coverage_total' => count($coverageScopes),
            'persisted' => 0,
            'placeholders_created' => 0,
            'questions_attached' => 0,
            'examples_attached' => 0,
            'skipped_missing_json' => 0,
            'skipped_invalid_json' => 0,
        ];

        $subjectPrefix = strtoupper(trim((string) config('raiida.conjugaison_extraction.target_subject_prefix', 'FR_')));

        /**
         * @var array<string, array<string, mixed>> $bestByKey
         */
        $bestByKey = [];

        /**
         * @var array<string, array<int, array<string, mixed>>> $textRowsByKey
         */
        $textRowsByKey = [];

        $query = FileAsset::query()
            ->select(['id', 'filename', 'presentation_json_path', 'is_presentation_data_extracted'])
            ->where('is_presentation_data_extracted', true)
            ->whereNotNull('presentation_json_path')
            ->orderBy('id');

        foreach ($query->lazyById(200) as $asset) {
            $summary['assets_scanned']++;

            $lessonId = pathinfo((string) $asset->filename, PATHINFO_FILENAME);
            if ($lessonId === '') {
                continue;
            }

            if ($subjectPrefix !== '' && ! str_starts_with(strtoupper($lessonId), $subjectPrefix)) {
                continue;
            }

            $scopes = $this->parseScopesFromLessonId($lessonId, $coverageKeys);
            if ($scopes === []) {
                continue;
            }

            $summary['assets_qualified']++;

            $jsonPath = $this->resolvePresentationJsonPath((string) $asset->presentation_json_path);
            if ($jsonPath === null || ! is_file($jsonPath)) {
                $summary['skipped_missing_json']++;

                continue;
            }

            $summary['assets_with_json']++;

            $payload = json_decode((string) file_get_contents($jsonPath), true);
            if (! is_array($payload)) {
                $summary['skipped_invalid_json']++;

                continue;
            }

            $slides = is_array($payload['slides'] ?? null) ? $payload['slides'] : [];
            if ($slides === []) {
                continue;
            }

            foreach ($scopes as $scope) {
                $key = $scope['n'] . '|' . $scope['p'] . '|' . $scope['sem'];

                foreach ($slides as $slideIndex => $slide) {
                    if (! is_array($slide)) {
                        continue;
                    }

                    $slideId = (int) ($slide['id'] ?? ($slideIndex + 1));
                    $elements = is_array($slide['elements'] ?? null) ? $slide['elements'] : [];

                    foreach ($elements as $element) {
                        if (! is_array($element)) {
                            continue;
                        }

                        if (strtolower((string) ($element['type'] ?? '')) !== 'text') {
                            continue;
                        }

                        $content = (string) ($element['content'] ?? '');
                        if (trim($content) === '') {
                            continue;
                        }

                        $textRowsByKey[$key][] = [
                            'content' => $content,
                            'source_lesson_id' => $lessonId,
                            'source_file_asset_id' => (int) $asset->id,
                            'source_slide_id' => $slideId,
                        ];

                        $candidate = $this->analyzer->analyze($content);
                        if ($candidate === null) {
                            continue;
                        }

                        $summary['candidates_found']++;

                        $baseScore = (int) $candidate['score'];
                        $slideBonus = $slideId > 0 && $slideId <= 8 ? 4 : ($slideId <= 16 ? 2 : 0);
                        $sessionBonus = (int) $scope['session'] === 2 ? 3 : ((int) $scope['session'] === 1 ? 1 : 0);
                        $score = $baseScore + $slideBonus + $sessionBonus;

                        $candidateSnapshot = [
                            'name' => (string) $candidate['name'],
                            'raw_data' => (string) $candidate['raw_data'],
                            'verbe' => $candidate['verbe'] !== null ? (string) $candidate['verbe'] : null,
                            'tense' => $candidate['tense'] !== null ? (string) $candidate['tense'] : null,
                            'score' => $score,
                            'score_breakdown' => [
                                'base' => $baseScore,
                                'slide_bonus' => $slideBonus,
                                'session_bonus' => $sessionBonus,
                            ],
                            'source_lesson_id' => $lessonId,
                            'source_file_asset_id' => (int) $asset->id,
                            'source_slide_id' => $slideId,
                            'n' => $scope['n'],
                            'p' => $scope['p'],
                            'sem' => $scope['sem'],
                            'grade_number' => (int) $scope['grade_number'],
                            'period_number' => (int) $scope['period_number'],
                            'week_number' => (int) $scope['week_number'],
                        ];
                        $candidateSnapshot = array_merge(
                            $candidateSnapshot,
                            $this->buildPreviewUrls((int) $asset->id, $slideId)
                        );

                        if (! isset($bestByKey[$key])) {
                            $bestByKey[$key] = [
                                'best' => $candidateSnapshot,
                                'candidate_count' => 1,
                                'top_candidates' => [$candidateSnapshot],
                                'best_question' => null,
                                'question_count' => 0,
                                'top_questions' => [],
                                'best_example' => null,
                                'example_count' => 0,
                                'top_examples' => [],
                            ];

                            continue;
                        }

                        $bestByKey[$key]['candidate_count']++;
                        $bestByKey[$key]['top_candidates'][] = $candidateSnapshot;
                        usort(
                            $bestByKey[$key]['top_candidates'],
                            static fn (array $a, array $b): int => ($b['score'] <=> $a['score'])
                                ?: ($a['source_slide_id'] <=> $b['source_slide_id'])
                        );
                        $bestByKey[$key]['top_candidates'] = array_slice($bestByKey[$key]['top_candidates'], 0, 5);

                        $currentBest = $bestByKey[$key]['best'];
                        if (
                            $score > (int) $currentBest['score']
                            || (
                                $score === (int) $currentBest['score']
                                && ((int) $slideId < (int) $currentBest['source_slide_id'])
                            )
                        ) {
                            $bestByKey[$key]['best'] = $candidateSnapshot;
                        }
                    }
                }
            }
        }

        foreach ($bestByKey as $key => &$row) {
            $best = $row['best'] ?? null;
            if (! is_array($best)) {
                continue;
            }

            $expectedVerb = isset($best['verbe']) ? (string) $best['verbe'] : null;
            $expectedTense = isset($best['tense']) ? (string) $best['tense'] : null;
            $textRows = $textRowsByKey[$key] ?? [];
            $bestSourceFileAssetId = (int) ($best['source_file_asset_id'] ?? 0);
            if ($bestSourceFileAssetId > 0) {
                $filteredRows = array_values(array_filter(
                    $textRows,
                    static fn (array $row): bool => (int) ($row['source_file_asset_id'] ?? 0) === $bestSourceFileAssetId
                ));
                if ($filteredRows !== []) {
                    $textRows = $filteredRows;
                }
            }

            foreach ($textRows as $textRow) {
                $question = $this->analyzer->analyzeQuestion(
                    (string) ($textRow['content'] ?? ''),
                    $expectedVerb,
                    $expectedTense
                );
                if ($question === null) {
                    continue;
                }

                $summary['question_candidates_found']++;

                $slideId = (int) ($textRow['source_slide_id'] ?? 0);
                $score = (int) ($question['score'] ?? 0);
                if ($slideId > 0 && $slideId <= 8) {
                    $score += 2;
                } elseif ($slideId <= 16) {
                    $score += 1;
                }

                $candidate = [
                    'question' => (string) ($question['question'] ?? ''),
                    'score' => $score,
                    'source_lesson_id' => (string) ($textRow['source_lesson_id'] ?? ''),
                    'source_file_asset_id' => (int) ($textRow['source_file_asset_id'] ?? 0),
                    'source_slide_id' => $slideId,
                ];
                $candidate = array_merge(
                    $candidate,
                    $this->buildPreviewUrls((int) ($textRow['source_file_asset_id'] ?? 0), $slideId)
                );

                $row['question_count'] = (int) ($row['question_count'] ?? 0) + 1;
                $row['top_questions'][] = $candidate;
                usort(
                    $row['top_questions'],
                    static fn (array $a, array $b): int => ($b['score'] <=> $a['score'])
                        ?: ($a['source_slide_id'] <=> $b['source_slide_id'])
                );
                $row['top_questions'] = array_slice($row['top_questions'], 0, 5);

                $currentBestQuestion = $row['best_question'] ?? null;
                if (! is_array($currentBestQuestion)
                    || $candidate['score'] > (int) ($currentBestQuestion['score'] ?? 0)
                    || (
                        $candidate['score'] === (int) ($currentBestQuestion['score'] ?? 0)
                        && $candidate['source_slide_id'] < (int) ($currentBestQuestion['source_slide_id'] ?? PHP_INT_MAX)
                    )
                ) {
                    $row['best_question'] = $candidate;
                }
            }

            foreach ($textRows as $textRow) {
                $example = $this->analyzer->analyzeExampleSentence(
                    (string) ($textRow['content'] ?? ''),
                    $expectedVerb
                );
                if ($example === null) {
                    continue;
                }

                $summary['example_candidates_found']++;

                $slideId = (int) ($textRow['source_slide_id'] ?? 0);
                $score = (int) ($example['score'] ?? 0);
                if ($slideId > 0 && $slideId <= 8) {
                    $score += 2;
                } elseif ($slideId <= 16) {
                    $score += 1;
                }

                $candidate = [
                    'sentence' => (string) ($example['sentence'] ?? ''),
                    'score' => $score,
                    'source_lesson_id' => (string) ($textRow['source_lesson_id'] ?? ''),
                    'source_file_asset_id' => (int) ($textRow['source_file_asset_id'] ?? 0),
                    'source_slide_id' => $slideId,
                ];
                $candidate = array_merge(
                    $candidate,
                    $this->buildPreviewUrls((int) ($textRow['source_file_asset_id'] ?? 0), $slideId)
                );

                $row['example_count'] = (int) ($row['example_count'] ?? 0) + 1;
                $row['top_examples'][] = $candidate;
                usort(
                    $row['top_examples'],
                    static fn (array $a, array $b): int => ($b['score'] <=> $a['score'])
                        ?: ($a['source_slide_id'] <=> $b['source_slide_id'])
                );
                $row['top_examples'] = array_slice($row['top_examples'], 0, 5);

                $currentBestExample = $row['best_example'] ?? null;
                if (! is_array($currentBestExample)
                    || $candidate['score'] > (int) ($currentBestExample['score'] ?? 0)
                    || (
                        $candidate['score'] === (int) ($currentBestExample['score'] ?? 0)
                        && $candidate['source_slide_id'] < (int) ($currentBestExample['source_slide_id'] ?? PHP_INT_MAX)
                    )
                ) {
                    $row['best_example'] = $candidate;
                }
            }
        }
        unset($row);

        $summary['keys_detected'] = count($bestByKey);

        $gradeIds = ConjugaisonGrade::query()->pluck('id', 'code')->all();
        $periodIds = ConjugaisonPeriod::query()->pluck('id', 'code')->all();
        $weekIds = ConjugaisonWeek::query()->pluck('id', 'code')->all();

        DB::transaction(function () use (&$summary, $bestByKey, $textRowsByKey, $coverageScopes, $gradeIds, $periodIds, $weekIds): void {
            foreach ($coverageScopes as $scope) {
                $key = $scope['n'] . '|' . $scope['p'] . '|' . $scope['sem'];
                $best = $bestByKey[$key]['best'] ?? null;
                if ($best === null && isset($textRowsByKey[$key])) {
                    foreach ($textRowsByKey[$key] as $row) {
                        $discovery = $this->analyzer->discoverVerbFromText((string) ($row['content'] ?? ''));
                        if ($discovery !== null) {
                            $best = [
                                'verbe' => $discovery,
                                'tense' => 'présent',
                                'score' => 5,
                                'source_file_asset_id' => (int) ($row['source_file_asset_id'] ?? 0),
                                'source_slide_id' => (int) ($row['source_slide_id'] ?? 0),
                                'raw_data' => (string) ($row['content'] ?? ''),
                            ];
                            break;
                        }
                    }
                }
                $score = is_array($best) ? (int) ($best['score'] ?? 0) : 0;

                $n = (string) ($scope['n'] ?? '');
                $p = (string) ($scope['p'] ?? '');
                $sem = (string) ($scope['sem'] ?? '');

                $basePayload = [
                    'grade_id' => $gradeIds[$n] ?? null,
                    'period_id' => $periodIds[$p] ?? null,
                    'week_id' => $weekIds[$sem] ?? null,
                    'week' => (int) ($scope['week_number'] ?? 0),
                ];

                $entry = Conjugaison::query()->firstOrNew([
                    'n' => $n,
                    'p' => $p,
                    'sem' => $sem,
                ]);

                if (! $entry->exists) {
                    $summary['placeholders_created']++;
                }

                $entry->fill(array_merge($basePayload, [
                    'name' => self::EMPTY_VALUE,
                    'question' => self::EMPTY_VALUE,
                    'verbe' => null,
                    'tense' => null,
                    'raw_data' => self::EMPTY_VALUE,
                    'related_raw_data' => json_encode([
                        'status' => 'empty',
                    ], JSON_UNESCAPED_UNICODE),
                    'source_lesson_id' => null,
                    'source_slide_id' => null,
                    'source_file_asset_id' => null,
                    'confidence_score' => 0,
                    'extraction_meta' => [
                        'method' => 'coverage_prefill_v2',
                    ],
                    'extracted_at' => now(),
                ]));

                if (is_array($best) && $score >= 4) {
                    $bestQuestion = is_array($row['best_question'] ?? null) ? $row['best_question'] : null;
                    $bestExample = is_array($row['best_example'] ?? null) ? $row['best_example'] : null;
                    $questionText = $bestQuestion !== null
                        ? (string) ($bestQuestion['question'] ?? self::EMPTY_VALUE)
                        : self::EMPTY_VALUE;
                    $rawDataText = $this->composeRawDataWithExamples(
                        (string) ($best['raw_data'] ?? self::EMPTY_VALUE),
                        is_array($row['top_examples'] ?? null) ? $row['top_examples'] : []
                    );

                    $entry->fill(array_merge($basePayload, [
                        'name' => (string) ($best['name'] ?? self::EMPTY_VALUE),
                        'question' => $questionText,
                        'verbe' => $best['verbe'] !== null ? (string) $best['verbe'] : null,
                        'tense' => $best['tense'] !== null ? (string) $best['tense'] : null,
                        'raw_data' => $rawDataText,
                        'related_raw_data' => json_encode([
                            'candidate_count' => (int) ($row['candidate_count'] ?? 0),
                            'question_count' => (int) ($row['question_count'] ?? 0),
                            'example_count' => (int) ($row['example_count'] ?? 0),
                            'top_candidates' => $row['top_candidates'] ?? [],
                            'top_questions' => $row['top_questions'] ?? [],
                            'top_examples' => $row['top_examples'] ?? [],
                        ], JSON_UNESCAPED_UNICODE),
                        'source_lesson_id' => (string) ($best['source_lesson_id'] ?? ''),
                        'source_slide_id' => (int) ($best['source_slide_id'] ?? 0),
                        'source_file_asset_id' => (int) ($best['source_file_asset_id'] ?? 0),
                        'confidence_score' => $score,
                        'extraction_meta' => [
                            'method' => 'presentation_data_v2',
                            'candidate_count' => (int) ($row['candidate_count'] ?? 0),
                            'question_count' => (int) ($row['question_count'] ?? 0),
                            'example_count' => (int) ($row['example_count'] ?? 0),
                            'score_breakdown' => $best['score_breakdown'] ?? null,
                            'best_question_score' => $bestQuestion['score'] ?? 0,
                            'best_example_score' => $bestExample['score'] ?? 0,
                            'source_preview_url' => $this->buildPreviewUrls(
                                (int) ($best['source_file_asset_id'] ?? 0),
                                (int) ($best['source_slide_id'] ?? 0)
                            )['source_preview_url'],
                            'source_slide_preview_url' => $this->buildPreviewUrls(
                                (int) ($best['source_file_asset_id'] ?? 0),
                                (int) ($best['source_slide_id'] ?? 0)
                            )['source_slide_preview_url'],
                        ],
                        'extracted_at' => now(),
                    ]));

                    $summary['persisted']++;
                    if ($bestQuestion !== null) {
                        $summary['questions_attached']++;
                    }
                    if ($bestExample !== null) {
                        $summary['examples_attached']++;
                    }
                }

                $entry->save();
            }
        });

        return $summary;
    }

    /**
     * @param  array<int, array<string, mixed>>  $topExamples
     */
    private function composeRawDataWithExamples(string $bestRawData, array $topExamples): string
    {
        $lines = [];
        $seen = [];

        $push = static function (string $line) use (&$lines, &$seen): void {
            $clean = trim($line);
            if ($clean === '') {
                return;
            }

            $signature = mb_strtolower($clean);
            if (isset($seen[$signature])) {
                return;
            }

            $seen[$signature] = true;
            $lines[] = $clean;
        };

        $push($bestRawData);

        foreach ($topExamples as $row) {
            if (! is_array($row)) {
                continue;
            }

            $push((string) ($row['sentence'] ?? ''));
            if (count($lines) >= 6) {
                break;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @return array{source_preview_url:string|null, source_slide_preview_url:string|null}
     */
    private function buildPreviewUrls(int $fileAssetId, int $slideId): array
    {
        if ($fileAssetId <= 0) {
            return [
                'source_preview_url' => null,
                'source_slide_preview_url' => null,
            ];
        }

        $previewUrl = route('admin.files.preview', ['fileAsset' => $fileAssetId]);
        $slideUrl = $slideId > 0
            ? route('admin.files.preview', ['fileAsset' => $fileAssetId, 'slide' => $slideId]) . '#slide-' . $slideId
            : null;

        return [
            'source_preview_url' => $previewUrl,
            'source_slide_preview_url' => $slideUrl,
        ];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function buildCoverageScopes(): array
    {
        $scopes = [];
        foreach ((array) config('raiida.conjugaison_extraction.grade_range', [1, 2, 3, 4, 5, 6]) as $gradeNumber) {
            $gradeNumber = (int) $gradeNumber;
            if ($gradeNumber < 1 || $gradeNumber > 6) {
                continue;
            }

            foreach ((array) config('raiida.conjugaison_extraction.period_range', [1, 2, 3, 4, 5]) as $periodNumber) {
                $periodNumber = (int) $periodNumber;
                if ($periodNumber < 1 || $periodNumber > 5) {
                    continue;
                }

                foreach ((array) config('raiida.conjugaison_extraction.week_range', [1, 2, 3, 4, 5, 6]) as $weekNumber) {
                    $weekNumber = (int) $weekNumber;
                    if ($weekNumber < 1 || $weekNumber > 6) {
                        continue;
                    }

                    $scopes[] = [
                        'n' => 'N' . $gradeNumber,
                        'p' => 'P' . $periodNumber,
                        'sem' => 'SEM' . $weekNumber,
                        'grade_number' => $gradeNumber,
                        'period_number' => $periodNumber,
                        'week_number' => $weekNumber,
                        'session' => 0,
                    ];
                }
            }
        }

        return $scopes;
    }

    private function ensureReferenceData(): void
    {
        foreach ((array) config('raiida.conjugaison_extraction.grade_range', [1, 2, 3, 4, 5, 6]) as $gradeNumber) {
            $gradeNumber = (int) $gradeNumber;
            if ($gradeNumber < 1 || $gradeNumber > 9) {
                continue;
            }

            ConjugaisonGrade::query()->updateOrCreate(
                ['grade_number' => $gradeNumber],
                [
                    'code' => 'N' . $gradeNumber,
                    'label' => 'Grade ' . $gradeNumber,
                ]
            );
        }

        foreach ((array) config('raiida.conjugaison_extraction.period_range', [1, 2, 3, 4, 5]) as $periodNumber) {
            $periodNumber = (int) $periodNumber;
            if ($periodNumber < 1 || $periodNumber > 9) {
                continue;
            }

            ConjugaisonPeriod::query()->updateOrCreate(
                ['period_number' => $periodNumber],
                [
                    'code' => 'P' . $periodNumber,
                    'label' => 'Period ' . $periodNumber,
                ]
            );
        }

        foreach ((array) config('raiida.conjugaison_extraction.week_range', [1, 2, 3, 4, 5, 6]) as $weekNumber) {
            $weekNumber = (int) $weekNumber;
            if ($weekNumber < 1 || $weekNumber > 9) {
                continue;
            }

            ConjugaisonWeek::query()->updateOrCreate(
                ['week_number' => $weekNumber],
                [
                    'code' => 'SEM' . $weekNumber,
                    'label' => 'Week ' . $weekNumber,
                ]
            );
        }
    }

    /**
     * @param  array<int, string>  $coverageKeys
     * @return array<int, array<string, int|string>>
     */
    private function parseScopesFromLessonId(string $lessonId, array $coverageKeys): array
    {
        $lessonId = trim($lessonId);
        $pattern = '/_N([1-6](?:&[1-6])?)_P([1-5])_SEM([1-6])_S([1-6])(?:\b|_|$)/i';
        if (preg_match($pattern, $lessonId, $matches) !== 1) {
            return [];
        }

        $gradeToken = (string) ($matches[1] ?? '');
        $periodNumber = (int) ($matches[2] ?? 0);
        $weekNumber = (int) ($matches[3] ?? 0);
        $sessionNumber = (int) ($matches[4] ?? 0);

        if ($gradeToken === '' || $periodNumber < 1 || $weekNumber < 1) {
            return [];
        }

        $grades = array_filter(
            array_unique(array_map(static fn (string $part): int => (int) trim($part), explode('&', $gradeToken))),
            static fn (int $grade): bool => $grade >= 1 && $grade <= 6
        );

        $scopes = [];
        foreach ($grades as $gradeNumber) {
            $scope = [
                'n' => 'N' . $gradeNumber,
                'p' => 'P' . $periodNumber,
                'sem' => 'SEM' . $weekNumber,
                'grade_number' => $gradeNumber,
                'period_number' => $periodNumber,
                'week_number' => $weekNumber,
                'session' => $sessionNumber,
            ];

            $key = $scope['n'] . '|' . $scope['p'] . '|' . $scope['sem'];
            if (! in_array($key, $coverageKeys, true)) {
                continue;
            }

            $scopes[] = $scope;
        }

        return $scopes;
    }

    private function resolvePresentationJsonPath(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }
}
