<?php

namespace App\Jobs\Raiida;

use App\Services\Raiida\QuestionStudioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BatchGeneratePublishForPeriodJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 7200;

    public int $tries = 1;

    public function __construct(
        public readonly string $period = 'P4'
    ) {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(QuestionStudioService $service): void
    {
        $periodCode = strtoupper(trim($this->period));
        if (! preg_match('/^P[1-5]$/', $periodCode)) {
            throw new InvalidArgumentException("Invalid period [{$this->period}]. Expected P1..P5.");
        }

        Log::info('raiida.batch_generate_publish_period.started', [
            'period' => $periodCode,
            'queue' => $this->queue,
        ]);

        $result = $service->batchGenerateAndPublish($periodCode);

        Log::info('raiida.batch_generate_publish_period.completed', [
            'period' => $periodCode,
            'total' => $result['total'] ?? 0,
            'generated' => $result['generated'] ?? 0,
            'published' => $result['published'] ?? 0,
            'failed' => $result['failed'] ?? 0,
            'skipped' => $result['skipped'] ?? 0,
        ]);
    }
}

