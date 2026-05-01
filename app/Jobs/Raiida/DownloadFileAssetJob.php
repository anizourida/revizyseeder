<?php

namespace App\Jobs\Raiida;

use App\Models\Raiida\FileAsset;
use App\Services\Raiida\SyncFilesService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class DownloadFileAssetJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;

    public int $tries = 3;

    public function __construct(
        public readonly int $fileAssetId,
        public readonly ?string $workflowContextId = null,
        public readonly ?int $initiatedByUserId = null,
        public readonly ?string $initiatedByEmail = null,
        public readonly ?string $initiatedByRole = null
    )
    {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(SyncFilesService $service): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $audit = [
            'file_asset_id' => $this->fileAssetId,
            'workflow_context_id' => $this->workflowContextId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_email' => $this->initiatedByEmail,
            'initiated_by_role' => $this->initiatedByRole,
            'attempt' => $this->attempts(),
        ];

        $asset = FileAsset::query()->find($this->fileAssetId);
        if (! $asset) {
            Log::warning('raiida.admin_mutation.job.download.skipped_missing_asset', $audit);

            return;
        }

        $lockSeconds = max(300, (int) config('raiida.sync.file_lock_seconds', 1800));
        $lock = Cache::lock('revizyseeder-file-download-' . $this->fileAssetId, $lockSeconds);
        if (! $lock->get()) {
            Log::info('raiida.admin_mutation.job.download.skipped_locked', $audit);

            return;
        }

        try {
            $status = $service->downloadExistingAsset($asset);

            if ($status === 'failed') {
                $asset->refresh();
                $error = (string) ($asset->download_error ?? '');

                if ($service->isPermanentDownloadError($error)) {
                    Log::warning('raiida.admin_mutation.job.download.permanent_failure', $audit + [
                        'status' => $status,
                        'error' => $error,
                    ]);

                    return;
                }

                throw new RuntimeException('File download failed for file_asset_id=' . $this->fileAssetId . ': ' . $error);
            }

            $asset->refresh();
            if ($this->shouldDispatchPresentationExtraction($asset, $status)) {
                try {
                    ExtractPresentationDataJob::dispatch(
                        $asset->id,
                        false,
                        $this->workflowContextId,
                        $this->initiatedByUserId,
                        $this->initiatedByEmail,
                        $this->initiatedByRole
                    );
                } catch (Throwable $exception) {
                    Log::warning('raiida.admin_mutation.job.download.presentation_extract_dispatch_failed', $audit + [
                        'exception' => $exception::class,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            Log::info('raiida.admin_mutation.job.download.completed', $audit + ['status' => $status]);
        } catch (Throwable $exception) {
            Log::warning('raiida.admin_mutation.job.download.failed', $audit + [
                'exception' => $exception::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        } finally {
            $lock->release();
        }
    }

    private function shouldDispatchPresentationExtraction(FileAsset $asset, string $downloadStatus): bool
    {
        if (! (bool) config('raiida.presentation_data.auto_extract_after_download', true)) {
            return false;
        }

        if (! in_array($downloadStatus, ['downloaded', 'skipped'], true)) {
            return false;
        }

        if (! preg_match('/\.(pptx|ppsx)$/i', (string) $asset->filename)) {
            return false;
        }

        if ($downloadStatus === 'skipped' && $asset->is_presentation_data_extracted) {
            return false;
        }

        return true;
    }
}
