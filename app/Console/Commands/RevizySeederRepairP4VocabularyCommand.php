<?php

namespace App\Console\Commands;

use App\Services\Raiida\VocabularyP4RepairService;
use Illuminate\Console\Command;

class RevizySeederRepairP4VocabularyCommand extends Command
{
    protected $signature = 'revizyseeder:vocab:repair-p4
        {--dry-run : Build correction mapping and report only}
        {--apply : Apply correction map in-place}
        {--export= : Export base path (without extension) or explicit .json path}
        {--skip-audio-sync : Skip forced audio regeneration and secret replacement}
        {--skip-translation-queue : Skip clearing/queueing translations}';

    protected $description = 'Repair FR P4 vocabulary truncation while preserving integration IDs (concept/flashcard/audio secrets).';

    public function handle(VocabularyP4RepairService $service): int
    {
        $runApply = (bool) $this->option('apply');
        $runDry = (bool) $this->option('dry-run') || ! $runApply;

        $this->line('Building P4 correction map for FR N1..N6 (SEM1..SEM4)...');
        $map = $service->buildCorrectionMap();

        $summary = (array) ($map['summary'] ?? []);
        $this->table(
            ['Metric', 'Value'],
            [
                ['Scanned rows', (string) ($summary['scanned_rows'] ?? 0)],
                ['Changed rows', (string) ($summary['changed_rows'] ?? 0)],
                ['Ambiguous rows', (string) ($summary['ambiguous_rows'] ?? 0)],
                ['Ready to apply', (string) ($summary['ready_to_apply_rows'] ?? 0)],
                ['Lessons total', (string) ($summary['lessons_total'] ?? 0)],
            ]
        );

        $applySummary = null;
        if ($runApply) {
            $this->line('Applying map with ID-preserving updates...');
            $applySummary = $service->applyCorrectionMap(
                (array) ($map['rows'] ?? []),
                [
                    'sync_audio' => ! (bool) $this->option('skip-audio-sync'),
                    'queue_translations' => ! (bool) $this->option('skip-translation-queue'),
                ]
            );

            $this->table(
                ['Apply Metric', 'Value'],
                [
                    ['Applied rows', (string) ($applySummary['applied_rows'] ?? 0)],
                    ['Skipped rows', (string) ($applySummary['skipped_rows'] ?? 0)],
                    ['Collision merges', (string) ($applySummary['collision_merges'] ?? 0)],
                    ['Deleted duplicate rows', (string) ($applySummary['deleted_duplicate_rows'] ?? 0)],
                    ['Failed rows', (string) ($applySummary['failed_rows'] ?? 0)],
                    ['Audio regenerated', (string) ($applySummary['audio_regenerated'] ?? 0)],
                    ['Audio secret replaced', (string) ($applySummary['audio_secret_replaced'] ?? 0)],
                    ['Translation rows queued', (string) ($applySummary['translation_rows_queued'] ?? 0)],
                ]
            );
        }

        $report = [
            'generated_at' => now()->toIso8601String(),
            'command' => 'revizyseeder:vocab:repair-p4',
            'mode' => $runApply ? ($runDry ? 'dry-run+apply' : 'apply') : 'dry-run',
            'map' => $map,
            'apply' => $applySummary,
        ];

        $exportBasePath = trim((string) $this->option('export'));
        $paths = $service->exportReport($report, $exportBasePath);

        $this->info('Report exported:');
        $this->line('  JSON: ' . (string) ($paths['json'] ?? ''));
        $this->line('  CSV : ' . (string) ($paths['csv'] ?? ''));

        if (! empty($applySummary['errors']) && is_array($applySummary['errors'])) {
            $this->warn('Sample errors:');
            foreach (array_slice($applySummary['errors'], 0, 5) as $error) {
                $this->line('  - ' . (string) $error);
            }
        }

        return self::SUCCESS;
    }
}
