<?php

namespace App\Console\Commands;

use App\Services\Raiida\ConjugaisonConceptGenerationService;
use Illuminate\Console\Command;

class RevizySeederGenerateConjugaisonConceptsCommand extends Command
{
    protected $signature = 'revizyseeder:generate-conjugaison-concepts
        {--limit=100 : Max items to process}
        {--grade= : Filter by grade (N1-N6)}
        {--period= : Filter by period (P1-P5)}
        {--week= : Filter by week (SEM1-SEM6)}
        {--status=published : Status for new concepts}
        {--wait=200 : Wait ms between API calls}';

    protected $description = 'Generate Revizy Concepts for extracted conjugaison data.';

    public function handle(ConjugaisonConceptGenerationService $service): int
    {
        $this->info('🚀 Starting Conjugaison Concept Generation...');

        $options = [
            'limit' => (int) $this->option('limit'),
            'grade' => $this->option('grade'),
            'period' => $this->option('period'),
            'week' => $this->option('week'),
            'status' => $this->option('status'),
            'wait_ms' => (int) $this->option('wait'),
        ];

        $summary = $service->generateBatch($options);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Targeted', $summary['targeted']],
                ['Linked Existing', $summary['linked_existing']],
                ['Created New', $summary['created_total']],
                ['Mappings Synced', $summary['mapping_synced_total']],
                ['Failed', $summary['failed_total']],
            ]
        );

        if (!empty($summary['errors'])) {
            $this->error('Errors encountered:');
            foreach (array_slice($summary['errors'], 0, 10) as $error) {
                $this->line(" - $error");
            }
            if (count($summary['errors']) > 10) {
                $this->line(' ... and ' . (count($summary['errors']) - 10) . ' more.');
            }
        }

        $this->info('✅ Process completed.');

        return self::SUCCESS;
    }
}
