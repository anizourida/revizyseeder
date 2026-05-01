<?php

namespace App\Console\Commands;

use App\Jobs\Raiida\BatchGeneratePublishForPeriodJob;
use App\Services\Raiida\QuestionStudioService;
use Illuminate\Console\Command;

class RevizySeederPublishQuestionsForPeriodCommand extends Command
{
    protected $signature = 'revizyseeder:questions:publish-period
        {period=P4 : Period code (P1..P5)}
        {--sync : Run immediately instead of queueing}';

    protected $description = 'Generate and publish vocabulary questions for a specific period (defaults to P4).';

    public function handle(QuestionStudioService $service): int
    {
        $period = strtoupper(trim((string) $this->argument('period')));
        if (! preg_match('/^P[1-5]$/', $period)) {
            $this->error("Invalid period [{$period}]. Expected P1..P5.");

            return self::FAILURE;
        }

        if ((bool) $this->option('sync')) {
            $result = $service->batchGenerateAndPublish($period);

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Period', $period],
                    ['Total items', (string) ($result['total'] ?? 0)],
                    ['Generated', (string) ($result['generated'] ?? 0)],
                    ['Published', (string) ($result['published'] ?? 0)],
                    ['Failed', (string) ($result['failed'] ?? 0)],
                    ['Skipped', (string) ($result['skipped'] ?? 0)],
                ]
            );

            $this->info((string) ($result['message'] ?? 'Completed.'));

            return self::SUCCESS;
        }

        BatchGeneratePublishForPeriodJob::dispatch($period);

        $this->info("Queued period question publish job for {$period}.");
        $this->line('Queue: ' . (string) config('raiida.workflow_queue', 'revizyseeder-workflows'));

        return self::SUCCESS;
    }
}

