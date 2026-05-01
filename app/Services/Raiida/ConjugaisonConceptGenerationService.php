<?php

namespace App\Services\Raiida;

use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\RevizyCurriculumMapping;
use App\Services\Raiida\External\RevizySystemClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ConjugaisonConceptGenerationService
{
    public function __construct(
        private readonly RevizySystemClient $revizy,
        private readonly RevizyCurriculumMappingSyncService $mappingSync
    ) {
    }

    /**
     * @param array{
     *     limit?:int,
     *     grade?:string,
     *     period?:string,
     *     week?:string,
     *     subject_code?:string,
     *     status?:string,
     *     is_active?:bool,
     *     wait_ms?:int
     * } $options
     */
    public function generateBatch(array $options = []): array
    {
        $limit = max(1, min((int) ($options['limit'] ?? 100), 500));
        $subjectCodeOverride = strtoupper(trim((string) ($options['subject_code'] ?? '')));
        $status = trim((string) ($options['status'] ?? 'published')) ?: 'published';
        $isActive = array_key_exists('is_active', $options) ? (bool) $options['is_active'] : true;
        $waitMs = max(0, min((int) ($options['wait_ms'] ?? 200), 5000));

        $query = Conjugaison::query()
            ->whereNotNull('verbe')
            ->where('verbe', '!=', '')
            ->where(function (Builder $q) {
                $q->whereNull('concept_id')->orWhere('concept_id', '');
            })
            ->orderBy('id');

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

        $mappingCache = [];
        $mappingSyncedKeys = [];

        foreach ($targets as $item) {
            $verb = trim((string) $item->verbe);
            $tense = trim((string) $item->tense);
            $conceptName = $tense !== '' ? "{$verb} au {$tense}" : $verb;

            $scope = $this->resolveScope($item);
            if (!$scope) {
                $summary['failed_total']++;
                $summary['errors'][] = "Conjugaison #{$item->id}: invalid scope.";
                continue;
            }

            $mappingKey = $scope['subject_code'] . '|' . $scope['grade_code'] . '|' . $scope['period_code'];
            $mapping = $this->resolveMapping($scope, $mappingKey, $mappingCache, $mappingSyncedKeys, $summary);

            if (!$mapping) {
                $summary['failed_total']++;
                $summary['errors'][] = "Conjugaison #{$item->id} ({$conceptName}): missing mapping.";
                continue;
            }

            $skillId = (int) ($mapping['revizy_conjugaison_skill_id'] ?? 0);
            $uniteId = (int) ($mapping['revizy_unite_id'] ?? 0);

            if ($skillId <= 0 || $uniteId <= 0) {
                $summary['failed_total']++;
                $summary['errors'][] = "Conjugaison #{$item->id} ({$conceptName}): invalid mapping IDs.";
                continue;
            }

            try {
                $payload = [
                    'skill_id' => $skillId,
                    'unite_id' => $uniteId,
                    'name' => $conceptName,
                    'description' => "Conjugaison du verbe {$verb}" . ($tense !== '' ? " au {$tense}" : "") . " ({$scope['grade_code']}, {$scope['period_code']}, {$scope['week_code']})",
                    'status' => $status,
                    'is_active' => $isActive,
                    'week' => $scope['week_number'],
                ];

                $response = $this->revizy->post('/concepts', $payload);
                $conceptId = $this->revizy->extractResourceId($response);

                if (!$conceptId) {
                    $summary['failed_total']++;
                    $summary['errors'][] = "Conjugaison #{$item->id} ({$conceptName}): API did not return concept ID.";
                } else {
                    $item->concept_id = $conceptId;
                    $item->revizy_skill_id = $skillId;
                    $item->revizy_unite_id = $uniteId;
                    $item->save();
                    $summary['created_total']++;
                }
            } catch (Throwable $e) {
                $summary['failed_total']++;
                $summary['errors'][] = "Conjugaison #{$item->id} ({$conceptName}): " . $e->getMessage();
            }

            if ($waitMs > 0) {
                usleep($waitMs * 1000);
            }
        }

        return $summary;
    }

    private function resolveMapping($scope, $mappingKey, &$mappingCache, &$mappingSyncedKeys, &$summary): ?array
    {
        if (array_key_exists($mappingKey, $mappingCache)) {
            return $mappingCache[$mappingKey];
        }

        $mapping = RevizyCurriculumMapping::query()
            ->where('subject_code', $scope['subject_code'])
            ->where('grade_code', $scope['grade_code'])
            ->where('period_code', $scope['period_code'])
            ->first();

        if ($mapping && $mapping->revizy_unite_id > 0 && $mapping->revizy_conjugaison_skill_id > 0) {
            return $mapping->toArray();
        }

        try {
            $sync = $this->mappingSync->syncScope($scope['subject_code'], $scope['grade_code'], $scope['period_code']);
            if ($sync['synced'] ?? false) {
                $summary['mapping_synced_total']++;
                return $sync['mapping']->toArray();
            }
        } catch (Throwable $e) {
            Log::error("Mapping sync failed: " . $e->getMessage());
        }

        return null;
    }

    private function applyScopeFilters(Builder $query, array $options): void
    {
        if (!empty($options['grade'])) $query->where('n', $options['grade']);
        if (!empty($options['period'])) $query->where('p', $options['period']);
        if (!empty($options['week'])) $query->where('sem', $options['week']);
    }

    private function resolveScope(Conjugaison $item): ?array
    {
        $n = (string) $item->n;
        $p = (string) $item->p;
        $sem = (string) $item->sem;

        if (!$n || !$p || !$sem) return null;

        $weekNumber = 0;
        if (preg_match('/([0-6])/', $sem, $m)) {
            $weekNumber = (int) $m[1];
        }

        return [
            'subject_code' => 'FR', // Defaulting to FR for conjugaison
            'grade_code' => $n,
            'period_code' => $p,
            'week_code' => $sem,
            'week_number' => $weekNumber,
        ];
    }
}
