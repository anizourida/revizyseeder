<?php

namespace App\Services\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Models\Raiida\RevizyCurriculumMapping;
use App\Services\Raiida\External\RevizySystemClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class VocabularyConceptGenerationService
{
    public function __construct(
        private readonly RevizySystemClient $revizy,
        private readonly RevizyCurriculumMappingSyncService $mappingSync
    ) {
    }

    /**
     * @param  array{
     *     limit?:int,
     *     grade?:string,
     *     period?:string,
     *     week?:string,
     *     subject_code?:string,
     *     status?:string,
     *     is_active?:bool,
     *     description_template?:string,
     *     wait_ms?:int
     * }  $options
     * @return array<string,mixed>
     */
    public function generateBatch(array $options = []): array
    {
        $limit = max(1, min((int) ($options['limit'] ?? 200), 500));
        $subjectCodeOverride = strtoupper(trim((string) ($options['subject_code'] ?? '')));
        $status = trim((string) ($options['status'] ?? 'published')) ?: 'published';
        $isActive = array_key_exists('is_active', $options)
            ? (bool) $options['is_active']
            : true;
        $waitMs = max(0, min(
            (int) ($options['wait_ms'] ?? config('raiida.concept_generator.wait_ms_between_items', 200)),
            5000
        ));
        $debugSearch = (bool) ($options['debug_search'] ?? config('raiida.concept_generator.debug_search', false));

        $descriptionTemplate = trim((string) ($options['description_template'] ?? 'Le mot de vocabulaire :word'));
        if ($descriptionTemplate === '') {
            $descriptionTemplate = 'Le mot de vocabulaire :word';
        }

        $query = VocabularyItem::query()
            ->select([
                'id',
                'word',
                'subject',
                'grade',
                'period',
                'week',
                'concept_id',
                'revizy_skill_id',
                'revizy_unite_id',
            ])
            ->orderBy('id');

        $this->scopeMissingConcept($query);
        $this->applyScopeFilters($query, $options);

        $targets = $query->limit($limit)->get();

        $summary = [
            'targeted' => $targets->count(),
            'linked_existing' => 0,
            'created_total' => 0,
            'failed_total' => 0,
            'mapping_synced_total' => 0,
            'errors' => [],
        ];

        /** @var array<string,array<string,mixed>|null> $mappingCache */
        $mappingCache = [];
        /** @var array<string,bool> $mappingSyncedKeys */
        $mappingSyncedKeys = [];

        foreach ($targets as $item) {
            $word = trim((string) $item->word);
            if ($word === '') {
                $summary['failed_total']++;
                $this->pushError($summary['errors'], "Vocabulary #{$item->id}: word is empty.");
                continue;
            }

            $scope = $this->resolveScope($item, $options, $subjectCodeOverride);
            if (! is_array($scope)) {
                $summary['failed_total']++;
                $this->pushError(
                    $summary['errors'],
                    "Vocabulary #{$item->id} ({$word}): invalid subject/grade/period/week scope."
                );
                continue;
            }

            $mappingKey = $scope['subject_code'] . '|' . $scope['grade_code'] . '|' . $scope['period_code'];
            $mapping = $this->resolveMapping(
                $scope,
                $mappingKey,
                $mappingCache,
                $mappingSyncedKeys,
                $summary
            );
            if (! is_array($mapping)) {
                $summary['failed_total']++;
                $this->pushError(
                    $summary['errors'],
                    "Vocabulary #{$item->id} ({$word}): missing skill/unite mapping for {$scope['subject_code']} {$scope['grade_code']}/{$scope['period_code']}."
                );
                continue;
            }

            $skillId = (int) ($mapping['revizy_vocab_skill_id'] ?? 0);
            $uniteId = (int) ($mapping['revizy_unite_id'] ?? 0);

            if ($skillId <= 0 || $uniteId <= 0) {
                $summary['failed_total']++;
                $this->pushError(
                    $summary['errors'],
                    "Vocabulary #{$item->id} ({$word}): mapping has invalid skill/unite IDs."
                );
                continue;
            }

            try {
                $searchTrace = [];
                $existingConceptId = $this->findExistingConceptId(
                    $scope['subject_code'],
                    $scope['grade_code'],
                    $scope['period_code'],
                    $scope['week_code'],
                    $word,
                    $searchTrace
                );

                if ($debugSearch) {
                    Log::info('raiida.concept_recovery.search_trace', [
                        'vocabulary_id' => $item->id,
                        'word' => $word,
                        'scope' => [
                            'subject_code' => $scope['subject_code'],
                            'grade_code' => $scope['grade_code'],
                            'period_code' => $scope['period_code'],
                            'week_code' => $scope['week_code'],
                        ],
                        'search_trace' => $searchTrace,
                        'found_existing_concept_id' => $existingConceptId,
                    ]);
                }

                if (is_string($existingConceptId) && trim($existingConceptId) !== '') {
                    $item->concept_id = trim($existingConceptId);
                    $item->revizy_skill_id = $skillId;
                    $item->revizy_unite_id = $uniteId;
                    $item->save();
                    $summary['linked_existing']++;
                } else {
                    $payload = [
                        'skill_id' => $skillId,
                        'unite_id' => $uniteId,
                        'name' => $word,
                        'description' => $this->buildDescription($descriptionTemplate, $item, $scope['week_number']),
                        'status' => $status,
                        'is_active' => $isActive,
                    ];

                    if (is_int($scope['week_number'])) {
                        $payload['week'] = $scope['week_number'];
                    }

                    $response = $this->revizy->post('/concepts', $payload);
                    $conceptId = $this->revizy->extractResourceId($response);

                    if (! is_string($conceptId) || trim($conceptId) === '') {
                        $summary['failed_total']++;
                        $this->pushError(
                            $summary['errors'],
                            "Vocabulary #{$item->id} ({$word}): concept created but no concept ID returned."
                        );
                    } else {
                        $item->concept_id = trim($conceptId);
                        $item->revizy_skill_id = $skillId;
                        $item->revizy_unite_id = $uniteId;
                        $item->save();
                        $summary['created_total']++;
                    }
                }
            } catch (Throwable $exception) {
                $summary['failed_total']++;
                $this->pushError($summary['errors'], "Vocabulary #{$item->id} ({$word}): {$exception->getMessage()}");
            }

            if ($waitMs > 0) {
                usleep($waitMs * 1000);
            }
        }

        $remainingQuery = VocabularyItem::query();
        $this->applyScopeFilters($remainingQuery, $options);
        $this->scopeMissingConcept($remainingQuery);
        $summary['remaining_missing_in_scope'] = (int) $remainingQuery->count();

        return $summary;
    }

    /**
     * @param  array{subject_code:string,grade_code:string,period_code:string} $scope
     * @param  array<string,array<string,mixed>|null> $mappingCache
     * @param  array<string,bool> $mappingSyncedKeys
     * @param  array<string,mixed> $summary
     * @return array<string,mixed>|null
     */
    private function resolveMapping(
        array $scope,
        string $mappingKey,
        array &$mappingCache,
        array &$mappingSyncedKeys,
        array &$summary
    ): ?array {
        if (array_key_exists($mappingKey, $mappingCache)) {
            return $mappingCache[$mappingKey];
        }

        $mapping = RevizyCurriculumMapping::query()
            ->where('subject_code', $scope['subject_code'])
            ->where('grade_code', $scope['grade_code'])
            ->where('period_code', $scope['period_code'])
            ->first([
                'subject_code',
                'grade_code',
                'period_code',
                'revizy_unite_id',
                'revizy_vocab_skill_id',
            ]);

        if ($mapping instanceof RevizyCurriculumMapping) {
            $payload = $mapping->toArray();
            if ((int) ($payload['revizy_unite_id'] ?? 0) > 0 && (int) ($payload['revizy_vocab_skill_id'] ?? 0) > 0) {
                $mappingCache[$mappingKey] = $payload;

                return $payload;
            }
        }

        try {
            $sync = $this->mappingSync->syncScope(
                $scope['subject_code'],
                $scope['grade_code'],
                $scope['period_code']
            );

            if (($sync['synced'] ?? false) === true && ! isset($mappingSyncedKeys[$mappingKey])) {
                $mappingSyncedKeys[$mappingKey] = true;
                $summary['mapping_synced_total'] = (int) ($summary['mapping_synced_total'] ?? 0) + 1;
            }
        } catch (Throwable $exception) {
            $mappingCache[$mappingKey] = null;
            $this->pushError(
                $summary['errors'],
                "Mapping auto-sync failed for {$scope['subject_code']} {$scope['grade_code']}/{$scope['period_code']}: {$exception->getMessage()}"
            );

            return null;
        }

        $mapping = RevizyCurriculumMapping::query()
            ->where('subject_code', $scope['subject_code'])
            ->where('grade_code', $scope['grade_code'])
            ->where('period_code', $scope['period_code'])
            ->first([
                'subject_code',
                'grade_code',
                'period_code',
                'revizy_unite_id',
                'revizy_vocab_skill_id',
            ]);

        if (! $mapping instanceof RevizyCurriculumMapping) {
            $mappingCache[$mappingKey] = null;

            return null;
        }

        $payload = $mapping->toArray();
        $mappingCache[$mappingKey] = $payload;

        return $payload;
    }

    /**
     * @param  Builder<VocabularyItem>  $query
     * @param  array<string,mixed>  $options
     */
    private function applyScopeFilters(Builder $query, array $options): void
    {
        if (! empty($options['subject_code'])) {
            $query->where('subject', strtoupper(trim((string) $options['subject_code'])));
        }
        if (! empty($options['grade'])) {
            $query->where('grade', strtoupper(trim((string) $options['grade'])));
        }
        if (! empty($options['period'])) {
            $query->where('period', strtoupper(trim((string) $options['period'])));
        }
        if (! empty($options['week'])) {
            $query->where('week', strtoupper(trim((string) $options['week'])));
        }
    }

    /**
     * @param  Builder<VocabularyItem>  $query
     */
    private function scopeMissingConcept(Builder $query): void
    {
        $query->where(static function (Builder $inner): void {
            $inner
                ->whereNull('concept_id')
                ->orWhere('concept_id', '');
        });
    }

    /**
     * @param  array<string,mixed>  $options
     * @return array{
     *   subject_code:string,
     *   grade_code:string,
     *   period_code:string,
     *   week_code:string,
     *   week_number:int
     * }|null
     */
    private function resolveScope(VocabularyItem $item, array $options, string $subjectCodeOverride): ?array
    {
        $subjectCodeRaw = $subjectCodeOverride !== '' ? $subjectCodeOverride : (string) ($item->subject ?? 'FR');
        $subjectCode = $this->normalizeSubjectCode($subjectCodeRaw);

        $gradeCode = $this->normalizeGradeCode((string) ($options['grade'] ?? $item->grade));
        $periodCode = $this->normalizePeriodCode((string) ($options['period'] ?? $item->period));
        $weekCode = $this->normalizeWeekCode((string) ($options['week'] ?? $item->week));

        if (! is_string($gradeCode) || ! is_string($periodCode) || ! is_string($weekCode)) {
            return null;
        }

        $weekNumber = $this->parseWeekNumber($weekCode);
        if (! is_int($weekNumber)) {
            return null;
        }

        return [
            'subject_code' => $subjectCode,
            'grade_code' => $gradeCode,
            'period_code' => $periodCode,
            'week_code' => $weekCode,
            'week_number' => $weekNumber,
        ];
    }

    private function normalizeSubjectCode(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return 'FR';
        }

        $upper = strtoupper($raw);
        $upperAscii = strtoupper(Str::ascii($raw));

        if (preg_match('/^([A-Z0-9]+)_N[1-6]$/', $upper, $m) === 1) {
            return (string) $m[1];
        }
        if (preg_match('/^([A-Z0-9]+)_N[1-6]$/', $upperAscii, $m) === 1) {
            return (string) $m[1];
        }

        $map = [
            'FR' => 'FR',
            'FRANCAIS' => 'FR',
            'FRANCAISE' => 'FR',
            'FRENCH' => 'FR',
            'AR' => 'AR',
            'ARABE' => 'AR',
            'ARABIC' => 'AR',
            'MATH' => 'MATH',
            'MATHS' => 'MATH',
            'MATHEMATIQUE' => 'MATH',
            'MATHEMATIQUES' => 'MATH',
            'EN' => 'EN',
            'ANGLAIS' => 'EN',
            'ENGLISH' => 'EN',
        ];

        if (isset($map[$upperAscii])) {
            return $map[$upperAscii];
        }

        $token = preg_replace('/[^A-Z0-9]/', '', $upperAscii) ?? '';
        if ($token === '') {
            return 'FR';
        }

        return $map[$token] ?? $token;
    }

    private function findExistingConceptId(
        string $subjectCode,
        string $gradeCode,
        string $periodCode,
        string $weekCode,
        string $name,
        ?array &$trace = null
    ): ?string {
        $strictPrefix = $subjectCode . '_' . $gradeCode . '_' . $periodCode . '_' . $weekCode;
        $nameQueries = $this->buildSearchNameQueries($name);

        // 1) Strict N/P/SEM only, then local exact matching by normalized names.
        // This protects against punctuation variants (e.g., straight vs typographic apostrophes).
        $strictNoNameRows = $this->searchConcepts($strictPrefix, null, 200);
        if (is_array($trace)) {
            $trace[] = [
                'step' => 'strict_prefix_without_name',
                'code_prefix' => $strictPrefix,
                'hits' => count($strictNoNameRows),
            ];
        }
        $strictNoName = $this->pickBestConcept($strictNoNameRows, $name, false);
        if (is_array($strictNoName)) {
            $id = $this->extractConceptId($strictNoName);
            if ($id !== '') {
                if (is_array($trace)) {
                    $trace[] = [
                        'step' => 'strict_prefix_without_name_selected',
                        'selected_id' => $id,
                    ];
                }
                return $id;
            }
        }

        // 2) Strict N/P/SEM + name query variants.
        foreach ($nameQueries as $queryName) {
            $strictRows = $this->searchConcepts($strictPrefix, $queryName, 50);
            if (is_array($trace)) {
                $trace[] = [
                    'step' => 'strict_prefix_with_name',
                    'code_prefix' => $strictPrefix,
                    'name' => $queryName,
                    'hits' => count($strictRows),
                ];
            }

            $strict = $this->pickBestConcept($strictRows, $name, true);
            if (is_array($strict)) {
                $id = $this->extractConceptId($strict);
                if ($id !== '') {
                    if (is_array($trace)) {
                        $trace[] = [
                            'step' => 'strict_prefix_with_name_selected',
                            'selected_id' => $id,
                            'matched_query_name' => $queryName,
                        ];
                    }
                    return $id;
                }
            }
        }

        // 3) N/P + name fallback to protect against legacy week mismatches.
        $periodPrefix = $subjectCode . '_' . $gradeCode . '_' . $periodCode;
        foreach ($nameQueries as $queryName) {
            $periodRows = $this->searchConcepts($periodPrefix, $queryName, 200);
            if (is_array($trace)) {
                $trace[] = [
                    'step' => 'period_prefix_with_name',
                    'code_prefix' => $periodPrefix,
                    'name' => $queryName,
                    'hits' => count($periodRows),
                ];
            }

            $period = $this->pickBestConcept($periodRows, $name, true);
            if (is_array($period)) {
                $id = $this->extractConceptId($period);
                if ($id !== '') {
                    if (is_array($trace)) {
                        $trace[] = [
                            'step' => 'period_prefix_with_name_selected',
                            'selected_id' => $id,
                            'matched_query_name' => $queryName,
                        ];
                    }
                    return $id;
                }
            }
        }

        if (is_array($trace)) {
            $trace[] = [
                'step' => 'not_found',
            ];
        }

        return null;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function searchConcepts(string $codePrefix, ?string $name, int $limit): array
    {
        $params = [
            'code_prefix' => $codePrefix,
            'limit' => max(1, min($limit, 200)),
        ];
        if (is_string($name) && trim($name) !== '') {
            $params['name'] = trim($name);
        }

        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $response = $this->revizy->get('/concepts/search?' . $query);

        return $this->extractConceptRows($response);
    }

    /**
     * @param  array<int,array<string,mixed>>  $rows
     * @return array<string,mixed>|null
     */
    private function pickBestConcept(array $rows, string $targetName, bool $allowLooseFallback): ?array
    {
        if ($rows === []) {
            return null;
        }

        $target = $this->normalizeComparableName($targetName);
        $targetNoArticle = $this->stripLeadingArticle($target);

        $exact = [];
        foreach ($rows as $row) {
            $candidate = $this->normalizeComparableName($this->extractConceptName($row));
            if ($candidate === '') {
                continue;
            }

            if ($candidate === $target) {
                $exact[] = $row;
                continue;
            }

            if ($this->stripLeadingArticle($candidate) === $targetNoArticle) {
                $exact[] = $row;
            }
        }

        if ($exact !== []) {
            return $exact[0];
        }

        return $allowLooseFallback ? $rows[0] : null;
    }

    /**
     * @param  array<string,mixed>  $response
     * @return array<int,array<string,mixed>>
     */
    private function extractConceptRows(array $response): array
    {
        $candidates = [];

        if (isset($response['data']) && is_array($response['data'])) {
            $candidates[] = $response['data'];
        }
        if (isset($response['items']) && is_array($response['items'])) {
            $candidates[] = $response['items'];
        }
        if (isset($response['results']) && is_array($response['results'])) {
            $candidates[] = $response['results'];
        }

        // Some endpoints return the list directly at top-level.
        $candidates[] = $response;

        foreach ($candidates as $candidate) {
            $rows = $this->flattenConceptRows($candidate);
            if ($rows !== []) {
                return $rows;
            }
        }

        return [];
    }

    /**
     * @param  array<mixed>  $payload
     * @return array<int,array<string,mixed>>
     */
    private function flattenConceptRows(array $payload): array
    {
        if (array_is_list($payload)) {
            $rows = array_values(array_filter(
                $payload,
                fn ($row): bool => is_array($row) && $this->looksLikeConceptRow($row)
            ));
            /** @var array<int,array<string,mixed>> $rows */
            return $rows;
        }

        foreach (['data', 'items', 'results'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                $rows = $this->flattenConceptRows($payload[$key]);
                if ($rows !== []) {
                    return $rows;
                }
            }
        }

        return $this->looksLikeConceptRow($payload) ? [$payload] : [];
    }

    /**
     * @param  array<string,mixed>  $row
     */
    private function looksLikeConceptRow(array $row): bool
    {
        return array_key_exists('id', $row)
            || array_key_exists('concept_id', $row)
            || array_key_exists('name', $row)
            || array_key_exists('title', $row)
            || array_key_exists('label', $row);
    }

    /**
     * @param  array<string,mixed>  $row
     */
    private function extractConceptName(array $row): string
    {
        foreach (['name', 'title', 'label'] as $key) {
            if (isset($row[$key])) {
                $name = trim((string) $row[$key]);
                if ($name !== '') {
                    return $name;
                }
            }
        }

        return '';
    }

    /**
     * @param  array<string,mixed>  $row
     */
    private function extractConceptId(array $row): string
    {
        foreach (['id', 'concept_id'] as $key) {
            if (isset($row[$key])) {
                $id = trim((string) $row[$key]);
                if ($id !== '') {
                    return $id;
                }
            }
        }

        return '';
    }

    private function normalizeComparableName(string $text): string
    {
        $normalized = trim($text);
        $normalized = str_replace(['’', '`', '´'], "'", $normalized);
        $normalized = Str::ascii($normalized);
        $normalized = mb_strtolower($normalized, 'UTF-8');
        $normalized = preg_replace('/[^a-z0-9\'\s]+/u', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/u', ' ', trim($normalized)) ?? trim($normalized);

        return $normalized;
    }

    private function stripLeadingArticle(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        return preg_replace("/^(l'|d'|le |la |les |un |une |des |de la |de l')/u", '', $trimmed) ?? $trimmed;
    }

    /**
     * @return array<int,string>
     */
    private function buildSearchNameQueries(string $name): array
    {
        $raw = trim($name);
        if ($raw === '') {
            return [];
        }

        $normalized = $this->normalizeComparableName($raw);
        $noArticle = $this->stripLeadingArticle($normalized);

        $candidates = [
            $raw,
            str_replace('’', "'", $raw),
            str_replace("'", '’', $raw),
            $normalized,
            $noArticle,
            str_replace("'", ' ', $noArticle),
            str_replace("'", '', $noArticle),
        ];

        $queries = [];
        $seen = [];
        foreach ($candidates as $candidate) {
            $value = preg_replace('/\s+/u', ' ', trim((string) $candidate)) ?? trim((string) $candidate);
            if ($value === '') {
                continue;
            }

            $key = mb_strtolower($value, 'UTF-8');
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $queries[] = $value;
        }

        return $queries;
    }

    private function normalizeGradeCode(string $value): ?string
    {
        $normalized = strtoupper(trim($value));
        if (preg_match('/^N[1-6]$/', $normalized) === 1) {
            return $normalized;
        }
        if (preg_match('/^[1-6]$/', $normalized) === 1) {
            return 'N' . $normalized;
        }

        return null;
    }

    private function normalizePeriodCode(string $value): ?string
    {
        $normalized = strtoupper(trim($value));
        if (preg_match('/^P[1-9][0-9]*$/', $normalized) === 1) {
            return $normalized;
        }
        if (preg_match('/^[1-9][0-9]*$/', $normalized) === 1) {
            return 'P' . $normalized;
        }

        return null;
    }

    private function normalizeWeekCode(string $value): ?string
    {
        $normalized = strtoupper(trim($value));
        if (preg_match('/^SEM[0-6]$/', $normalized) === 1) {
            return $normalized;
        }
        if (preg_match('/^[0-6]$/', $normalized) === 1) {
            return 'SEM' . $normalized;
        }
        if (preg_match('/^WEEK[0-6]$/', $normalized) === 1) {
            return 'SEM' . substr($normalized, 4);
        }

        return null;
    }

    private function parseWeekNumber(?string $value): ?int
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        if (preg_match('/([0-6])/', strtoupper($value), $matches) === 1) {
            $week = (int) ($matches[1] ?? -1);
            if ($week >= 0 && $week <= 6) {
                return $week;
            }
        }

        return null;
    }

    private function buildDescription(string $template, VocabularyItem $item, int $weekNumber): string
    {
        $word = trim((string) $item->word);
        $description = strtr($template, [
            ':word' => $word,
            ':grade' => (string) ($item->grade ?? ''),
            ':period' => (string) ($item->period ?? ''),
            ':week' => (string) $weekNumber,
        ]);

        $description = preg_replace('/\s+/u', ' ', trim($description)) ?? trim($description);

        if ($description !== '') {
            return $description;
        }

        return $word !== '' ? "Le mot de vocabulaire {$word}" : 'Le mot de vocabulaire';
    }

    /**
     * @param  array<int,string>  $errors
     */
    private function pushError(array &$errors, string $message): void
    {
        if (count($errors) >= 20) {
            if (($errors[19] ?? null) !== 'More errors omitted...') {
                $errors[19] = 'More errors omitted...';
            }
            return;
        }

        $errors[] = $message;
    }
}
