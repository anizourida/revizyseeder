<?php

namespace App\Services\Raiida;

use App\Models\Raiida\VocabularyItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VocabularyMetadataClassificationService
{
    public function __construct(
        private readonly GeminiClassificationService $gemini
    ) {
    }

    /**
     * @param  array{limit?:int,grade?:string,period?:string,week?:string,dry_run?:bool,force?:bool}  $options
     * @return array<string,mixed>
     */
    public function classify(array $options = []): array
    {
        $limit = max(1, min((int) ($options['limit'] ?? 120), 500));
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $force = (bool) ($options['force'] ?? false);

        $query = VocabularyItem::query()
            ->select([
                'id',
                'word',
                'lexical_type',
                'gender',
                'distractor_group',
                'distractor_subgroup',
                'grade',
                'period',
                'week',
            ])
            ->orderBy('id');

        if (! $force) {
            $this->scopeMissingMetadata($query);
        }

        if (! empty($options['grade'])) {
            $query->where('grade', (string) $options['grade']);
        }
        if (! empty($options['period'])) {
            $query->where('period', (string) $options['period']);
        }
        if (! empty($options['week'])) {
            $query->where('week', (string) $options['week']);
        }

        /** @var Collection<int, VocabularyItem> $targets */
        $targets = $query->limit($limit)->get();

        $summary = [
            'dry_run' => $dryRun,
            'force' => $force,
            'targeted' => $targets->count(),
            'updated_total' => 0,
            'updated_from_cache' => 0,
            'updated_from_ai' => 0,
            'skipped_no_change' => 0,
            'ai_batches' => 0,
            'ai_failed_batches' => 0,
            'errors' => [],
        ];

        if ($targets->isEmpty()) {
            return $summary + ['remaining_missing_in_scope' => 0];
        }

        $knownMap = $this->buildKnownMap();
        $pendingByKey = [];

        foreach ($targets as $item) {
            $wordKey = $this->wordKey((string) $item->word);
            if ($wordKey === '') {
                $summary['skipped_no_change']++;
                continue;
            }

            $known = $knownMap[$wordKey] ?? null;
            if (is_array($known)) {
                $changed = $this->applyMetadata($item, $known, $dryRun, $force);
                if ($changed) {
                    $summary['updated_total']++;
                    $summary['updated_from_cache']++;
                } else {
                    $summary['skipped_no_change']++;
                }
                continue;
            }

            if (! isset($pendingByKey[$wordKey])) {
                $pendingByKey[$wordKey] = [];
            }
            $pendingByKey[$wordKey][] = $item;
        }

        $configuredBatchSize = max(1, min((int) config('raiida.gemini.classification_batch_size', 40), 80));
        $maxOutputTokens = max(300, (int) config('raiida.gemini.max_output_tokens', 1400));
        $estimatedTokensPerItem = 90;
        $tokenSafeBatchSize = max(1, (int) floor(max(1, $maxOutputTokens - 240) / $estimatedTokensPerItem));
        $batchSize = max(1, min($configuredBatchSize, $tokenSafeBatchSize, 80));
        $pendingKeys = array_keys($pendingByKey);
        $pendingChunks = array_chunk($pendingKeys, $batchSize);

        foreach ($pendingChunks as $chunkKeys) {
            $summary['ai_batches']++;

            $payload = [];
            foreach ($chunkKeys as $key) {
                $first = $pendingByKey[$key][0] ?? null;
                if (! $first instanceof VocabularyItem) {
                    continue;
                }

                $payload[] = [
                    'id' => (int) $first->id,
                    'word' => (string) $first->word,
                ];
            }

            try {
                $classified = $this->gemini->classify($payload);
            } catch (\Throwable $exception) {
                $summary['ai_failed_batches']++;
                $summary['errors'][] = $exception->getMessage();
                continue;
            }

            $keyedByRepresentative = [];
            foreach ($payload as $row) {
                $id = (int) ($row['id'] ?? 0);
                if ($id <= 0) {
                    continue;
                }

                $word = (string) ($row['word'] ?? '');
                $k = $this->wordKey($word);
                if ($k === '') {
                    continue;
                }

                if (isset($classified[$id]) && is_array($classified[$id])) {
                    $keyedByRepresentative[$k] = $classified[$id];
                }
            }

            foreach ($chunkKeys as $key) {
                $prediction = $keyedByRepresentative[$key] ?? null;
                if (! is_array($prediction)) {
                    foreach ($pendingByKey[$key] as $item) {
                        $summary['skipped_no_change']++;
                    }
                    continue;
                }

                foreach ($pendingByKey[$key] as $item) {
                    $changed = $this->applyMetadata($item, $prediction, $dryRun, $force);
                    if ($changed) {
                        $summary['updated_total']++;
                        $summary['updated_from_ai']++;
                    } else {
                        $summary['skipped_no_change']++;
                    }
                }
            }
        }

        $remainingQuery = VocabularyItem::query();
        if (! empty($options['grade'])) {
            $remainingQuery->where('grade', (string) $options['grade']);
        }
        if (! empty($options['period'])) {
            $remainingQuery->where('period', (string) $options['period']);
        }
        if (! empty($options['week'])) {
            $remainingQuery->where('week', (string) $options['week']);
        }
        $this->scopeMissingMetadata($remainingQuery);

        $summary['remaining_missing_in_scope'] = (int) $remainingQuery->count();

        return $summary;
    }

    /**
     * @return array<string,array{lexical_type:?string,gender:?string,distractor_group:?string,distractor_subgroup:?string}>
     */
    private function buildKnownMap(): array
    {
        $rows = VocabularyItem::query()
            ->select(['word', 'lexical_type', 'gender', 'distractor_group', 'distractor_subgroup'])
            ->whereNotNull('lexical_type')
            ->where('lexical_type', '!=', '')
            ->whereNotNull('distractor_group')
            ->where('distractor_group', '!=', '')
            ->get();

        $bucket = [];
        foreach ($rows as $row) {
            $key = $this->wordKey((string) $row->word);
            if ($key === '') {
                continue;
            }

            $signature = implode('|', [
                (string) ($row->lexical_type ?? ''),
                (string) ($row->gender ?? ''),
                (string) ($row->distractor_group ?? ''),
                (string) ($row->distractor_subgroup ?? ''),
            ]);

            if (! isset($bucket[$key])) {
                $bucket[$key] = [];
            }
            if (! isset($bucket[$key][$signature])) {
                $bucket[$key][$signature] = 0;
            }
            $bucket[$key][$signature]++;
        }

        $best = [];
        foreach ($bucket as $key => $signatures) {
            arsort($signatures);
            $signature = array_key_first($signatures);
            if (! is_string($signature)) {
                continue;
            }

            [$lexicalType, $gender, $group, $subgroup] = array_pad(explode('|', $signature, 4), 4, '');

            $best[$key] = [
                'lexical_type' => $lexicalType !== '' ? $lexicalType : null,
                'gender' => $gender !== '' ? $gender : null,
                'distractor_group' => $group !== '' ? $group : null,
                'distractor_subgroup' => $subgroup !== '' ? $subgroup : null,
            ];
        }

        return $best;
    }

    /**
     * @param  array{lexical_type:?string,gender:?string,distractor_group:?string,distractor_subgroup:?string}  $metadata
     */
    private function applyMetadata(VocabularyItem $item, array $metadata, bool $dryRun, bool $force): bool
    {
        $updates = [];

        if (($force || $this->isBlank($item->lexical_type)) && ! $this->isBlank($metadata['lexical_type'] ?? null)) {
            $updates['lexical_type'] = $metadata['lexical_type'];
        }

        $targetType = $updates['lexical_type'] ?? $item->lexical_type;

        if ($targetType === 'nom') {
            if (($force || $this->isBlank($item->gender)) && ! $this->isBlank($metadata['gender'] ?? null)) {
                $updates['gender'] = $metadata['gender'];
            }
        }

        if (($force || $this->isBlank($item->distractor_group)) && ! $this->isBlank($metadata['distractor_group'] ?? null)) {
            $updates['distractor_group'] = $metadata['distractor_group'];
        }

        if (($force || $this->isBlank($item->distractor_subgroup)) && ! $this->isBlank($metadata['distractor_subgroup'] ?? null)) {
            $updates['distractor_subgroup'] = $metadata['distractor_subgroup'];
        }

        if ($updates === []) {
            return false;
        }

        if (! $dryRun) {
            $item->fill($updates);
            $item->save();
        }

        return true;
    }

    private function scopeMissingMetadata(Builder $query): void
    {
        $query->where(function (Builder $missing): void {
            $missing
                ->whereNull('lexical_type')
                ->orWhere('lexical_type', '')
                ->orWhereNull('distractor_group')
                ->orWhere('distractor_group', '')
                ->orWhere(function (Builder $nounMissingGender): void {
                    $nounMissingGender
                        ->where('lexical_type', 'nom')
                        ->where(function (Builder $gender): void {
                            $gender->whereNull('gender')->orWhere('gender', '');
                        });
                });
        });
    }

    private function wordKey(string $word): string
    {
        $normalized = mb_strtolower(trim($word), 'UTF-8');
        $normalized = str_replace(["\u{2019}", "'"], '', $normalized);
        $normalized = preg_replace('/\s+/u', ' ', $normalized);

        return trim((string) $normalized);
    }

    private function isBlank(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        return trim((string) $value) === '';
    }
}
