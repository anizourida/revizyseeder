<?php

namespace App\Jobs\Raiida;

use App\Services\Raiida\FileInspectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class InspectFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public readonly ?string $workflowContextId = null,
        public readonly ?int $initiatedByUserId = null,
        public readonly ?string $initiatedByEmail = null,
        public readonly ?string $initiatedByRole = null
    )
    {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(FileInspectionService $service): void
    {
        $audit = [
            'workflow_context_id' => $this->workflowContextId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_email' => $this->initiatedByEmail,
            'initiated_by_role' => $this->initiatedByRole,
        ];

        Log::info('raiida.admin_mutation.job.inspect.started', $audit);

        try {
            $summary = $service->run();
        } catch (Throwable $exception) {
            Log::error('raiida.admin_mutation.job.inspect.failed', $audit + [
                'exception' => $exception::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        Log::info('raiida.admin_mutation.job.inspect.completed', $audit + $summary);
    }
}
