<?php

namespace App\Jobs\Raiida;

use App\Services\Raiida\SyncFilesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public readonly ?string $workflowContextId = null,
        public readonly ?int $initiatedByUserId = null,
        public readonly ?string $initiatedByEmail = null,
        public readonly ?string $initiatedByRole = null,
        public readonly bool $retryFailed = false
    )
    {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(SyncFilesService $service): void
    {
        $audit = [
            'workflow_context_id' => $this->workflowContextId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_email' => $this->initiatedByEmail,
            'initiated_by_role' => $this->initiatedByRole,
        ];

        Log::info('raiida.admin_mutation.job.sync.started', $audit);

        try {
            $summary = $service->runMetadataOnly();

            if (! ($summary['locked'] ?? false)) {
                $downloadCandidateIds = $service->collectDownloadCandidateIds($this->retryFailed);
                $batch = $service->dispatchDownloadBatch(
                    $downloadCandidateIds,
                    $this->workflowContextId,
                    $this->initiatedByUserId,
                    $this->initiatedByEmail,
                    $this->initiatedByRole
                );

                $summary['queued_download_jobs'] = count($downloadCandidateIds);
                $summary['download_batch_id'] = $batch?->id;
            }
        } catch (Throwable $exception) {
            Log::error('raiida.admin_mutation.job.sync.failed', $audit + [
                'exception' => $exception::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        Log::info('raiida.admin_mutation.job.sync.completed', $audit + $summary);
    }
}
