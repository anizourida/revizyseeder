<?php

namespace App\Console\Commands;

use App\Jobs\Raiida\BackfillOrderWordsQuestionsJob;
use App\Services\Raiida\QuestionStudioService;
use Illuminate\Console\Command;

class RaiidaBackfillOrderWordsQuestionsCommand extends Command
{
    protected $signature = 'raiida:backfill-order-words
        {--period= : Period code (e.g. P4)}
        {--week= : Week code (e.g. SEM2)}
        {--grade= : Grade code (e.g. N1)}
        {--limit=5000 : Max items to process}
        {--dry-run : Do not call Revizy publish}
        {--include-payload : Include generated question payload in output}
        {--sync : Run immediately instead of queueing}';

    protected $description = 'Backfill missing order_words vocabulary questions for concepts that do not have them yet.';

    public function handle(QuestionStudioService $service): int
    {
        $options = [
            'period' => $this->option('period'),
            'week' => $this->option('week'),
            'grade' => $this->option('grade'),
            'limit' => (int) $this->option('limit'),
            'dry_run' => (bool) $this->option('dry-run'),
            'include_payload' => (bool) $this->option('include-payload'),
        ];

        if ((bool) $this->option('sync')) {
            $result = $service->batchGenerateAndPublishMissingOrderWords($options);

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total items', (string) ($result['total'] ?? 0)],
                    ['Generated', (string) ($result['generated'] ?? 0)],
                    ['Published', (string) ($result['published'] ?? 0)],
                    ['Failed', (string) ($result['failed'] ?? 0)],
                    ['Skipped', (string) ($result['skipped'] ?? 0)],
                    ['Dry run', (string) ((bool) ($result['dry_run'] ?? false) ? '1' : '0')],
                ]
            );

            $this->info((string) ($result['message'] ?? 'Completed.'));

            return self::SUCCESS;
        }

        BackfillOrderWordsQuestionsJob::dispatch($options);

        $this->info('Queued missing order_words backfill job.');
        $this->line('Queue: ' . (string) config('raiida.workflow_queue', 'revizyseeder-workflows'));

        return self::SUCCESS;
    }
}
