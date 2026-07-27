<?php

namespace App\Console\Commands;

use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\External\RevizySystemClient;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class RevizySeederSyncVocabularyToRevizyCommand extends Command
{
    protected $signature = 'revizyseeder:vocabulary:sync-to-revizy
        {--grade= : Filter by grade code, e.g. N4}
        {--period= : Filter by period code, e.g. P2}
        {--week= : Filter by week code, e.g. SEM3}
        {--limit=0 : Limit number of rows to process (0 = all)}
        {--dry-run : Print rows that would sync without calling Revizy}
        {--only-missing : Create missing Revizy vocabulary rows without overwriting existing ones}';

    protected $description = 'Sync local Seeder vocabulary_items into Revizy vocabularies via the protected system API.';

    public function handle(RevizySystemClient $revizy): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $onlyMissing = (bool) $this->option('only-missing');
        $limit = max(0, (int) $this->option('limit'));

        $query = VocabularyItem::query()
            ->orderBy('id');
        $this->applyFilters($query);

        if ($limit > 0) {
            $query->limit($limit);
        }

        $items = $query->get();
        if ($items->isEmpty()) {
            $this->warn('No vocabulary rows matched the selected filters.');

            return self::SUCCESS;
        }

        $stats = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'warnings' => 0,
        ];

        $this->line('Vocabulary rows: ' . $items->count() . ($dryRun ? ' (dry-run)' : ''));

        foreach ($items as $item) {
            $stats['processed']++;
            $payload = $this->payload($item, $onlyMissing);

            if ($dryRun) {
                $this->line(sprintf(
                    '[DRY RUN] #%d %s %s/%s/%s concept=%s image=%s audio=%s',
                    $item->id,
                    $item->word,
                    $item->grade,
                    $item->period,
                    $item->week,
                    $item->concept_id ?: '-',
                    $item->revizy_image_file_id ?: '-',
                    $item->revizy_audio_file_id ?: '-'
                ));
                continue;
            }

            try {
                $response = $revizy->post('/vocabulary', $payload);
                $warnings = is_array($response['warnings'] ?? null) ? $response['warnings'] : [];
                $stats['warnings'] += count($warnings);

                if ((bool) ($response['skipped'] ?? false)) {
                    $stats['skipped']++;
                } elseif ((bool) ($response['created'] ?? false)) {
                    $stats['created']++;
                } else {
                    $stats['updated']++;
                }

                if ($warnings !== []) {
                    $this->warn("Vocabulary #{$item->id} synced with warnings: " . implode(' | ', array_slice($warnings, 0, 3)));
                }
            } catch (RaiidaApiException $exception) {
                $stats['failed']++;
                $this->error("Vocabulary #{$item->id} failed: {$exception->getMessage()}");
            } catch (Throwable $exception) {
                $stats['failed']++;
                $this->error("Vocabulary #{$item->id} failed: {$exception->getMessage()}");
            }
        }

        $this->newLine();
        $this->table(
            ['Processed', 'Created', 'Updated', 'Skipped', 'Failed', 'Warnings'],
            [[
                $stats['processed'],
                $stats['created'],
                $stats['updated'],
                $stats['skipped'],
                $stats['failed'],
                $stats['warnings'],
            ]]
        );

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function applyFilters(Builder $query): void
    {
        if ($this->option('grade')) {
            $query->where('grade', strtoupper(trim((string) $this->option('grade'))));
        }
        if ($this->option('period')) {
            $query->where('period', strtoupper(trim((string) $this->option('period'))));
        }
        if ($this->option('week')) {
            $query->where('week', strtoupper(trim((string) $this->option('week'))));
        }
    }

    /**
     * @return array<string,mixed>
     */
    private function payload(VocabularyItem $item, bool $onlyMissing): array
    {
        return array_filter([
            'source' => 'seeder',
            'source_vocabulary_item_id' => (int) $item->id,
            'word' => (string) $item->word,
            'base_word' => $this->nullableString($item->base_word ?? null),
            'ar_translation' => $this->nullableString($item->ar_translation ?? null),
            'grade_code' => strtoupper((string) $item->grade),
            'subject_code' => strtoupper((string) ($item->subject ?: 'FR')),
            'period_code' => strtoupper((string) $item->period),
            'week_code' => strtoupper((string) $item->week),
            'lesson_id' => $this->nullableString($item->lesson_id ?? null),
            'lexical_type' => $this->nullableString($item->lexical_type ?? null),
            'gender' => $this->nullableString($item->gender ?? null),
            'distractor_group' => $this->nullableString($item->distractor_group ?? null),
            'distractor_subgroup' => $this->nullableString($item->distractor_subgroup ?? null),
            'concept_id' => $this->nullableString($item->concept_id ?? null),
            'revizy_skill_id' => $this->positiveInt($item->revizy_skill_id ?? null),
            'revizy_unite_id' => $this->positiveInt($item->revizy_unite_id ?? null),
            'flashcard_id' => $this->positiveInt($item->flashcard_id ?? null),
            'revizy_image_file_id' => $this->nullableString($item->revizy_image_file_id ?? null),
            'revizy_audio_file_id' => $this->nullableString($item->revizy_audio_file_id ?? null),
            'status' => 'published',
            'only_missing' => $onlyMissing,
        ], static fn (mixed $value): bool => $value !== null);
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }

    private function positiveInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? $int : null;
    }
}
