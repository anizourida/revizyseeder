<?php

namespace App\Jobs\Raiida;

use App\Models\Raiida\FileAsset;
use App\Services\Raiida\PresentationDataExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExtractPresentationDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public int $tries = 2;

    public function __construct(
        public readonly int $fileAssetId,
        public readonly bool $force = false,
        public readonly ?string $workflowContextId = null,
        public readonly ?int $initiatedByUserId = null,
        public readonly ?string $initiatedByEmail = null,
        public readonly ?string $initiatedByRole = null
    ) {
        $this->queue = (string) config('raiida.presentation_data.queue', config('raiida.workflow_queue', 'revizyseeder-workflows'));
    }

    public function handle(PresentationDataExtractionService $service): void
    {
        $audit = [
            'file_asset_id' => $this->fileAssetId,
            'force' => $this->force,
            'workflow_context_id' => $this->workflowContextId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_email' => $this->initiatedByEmail,
            'initiated_by_role' => $this->initiatedByRole,
            'attempt' => $this->attempts(),
        ];

        $asset = FileAsset::query()->find($this->fileAssetId);
        if (! $asset) {
            Log::warning('raiida.admin_mutation.job.presentation_extract.skipped_missing_asset', $audit);

            return;
        }

        if (! $asset->is_downloaded) {
            Log::info('raiida.admin_mutation.job.presentation_extract.skipped_not_downloaded', $audit);

            return;
        }

        $lockSeconds = max(300, (int) config('raiida.presentation_data.file_lock_seconds', 1800));
        $lock = Cache::lock('revizyseeder-presentation-extract-' . $this->fileAssetId, $lockSeconds);
        if (! $lock->get()) {
            Log::info('raiida.admin_mutation.job.presentation_extract.skipped_locked', $audit);

            return;
        }

        try {
            $summary = $service->extractFromFileAsset($asset, $this->force);

            Log::info('raiida.admin_mutation.job.presentation_extract.completed', $audit + [
                'slide_count' => (int) ($summary['slide_count'] ?? 0),
                'images' => (int) ($summary['images'] ?? 0),
                'videos' => (int) ($summary['videos'] ?? 0),
                'from_cache' => (bool) ($summary['from_cache'] ?? false),
            ]);
        } catch (Throwable $exception) {
            if ($this->isPermanentExtractionError($exception)) {
                Log::warning('raiida.admin_mutation.job.presentation_extract.permanent_failure', $audit + [
                    'exception' => $exception::class,
                    'error' => $exception->getMessage(),
                ]);

                return;
            }

            Log::warning('raiida.admin_mutation.job.presentation_extract.failed', $audit + [
                'exception' => $exception::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        } finally {
            $lock->release();
        }
    }

    private function isPermanentExtractionError(Throwable $exception): bool
    {
        $message = strtolower(trim($exception->getMessage()));
        if ($message === '') {
            return false;
        }

        $patterns = [
            'package not found at',
            'bad crc-32',
            'unrecognized shape type',
            'unsupported presentation extension',
            'failed to convert ppsx to pptx',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
