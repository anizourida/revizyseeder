<?php

namespace App\Jobs\Raiida;

use App\Services\Raiida\QuestionStudioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BatchGeneratePublishJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(QuestionStudioService $service): void
    {
        $result = $service->batchGenerateAndPublish();

        Log::info('raiida.batch_generate_publish.completed', [
            'total' => $result['total'] ?? 0,
            'generated' => $result['generated'] ?? 0,
            'published' => $result['published'] ?? 0,
            'failed' => $result['failed'] ?? 0,
            'skipped' => $result['skipped'] ?? 0,
        ]);
    }
}
