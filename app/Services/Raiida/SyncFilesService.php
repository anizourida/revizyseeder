<?php

namespace App\Services\Raiida;

use App\Jobs\Raiida\DownloadFileAssetJob;
use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Period;
use App\Models\Raiida\Subject;
use App\Models\Raiida\Week;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncFilesService
{
    public function run(): array
    {
        return $this->runInternal(true);
    }

    public function runMetadataOnly(): array
    {
        return $this->runInternal(false);
    }

    public function collectDownloadCandidateIds(bool $retryPermanentErrors = false): array
    {
        $ids = [];

        FileAsset::query()
            ->select(['id', 'download_state', 'download_error'])
            ->where('is_downloaded', false)
            ->where(function ($query): void {
                $query
                    ->whereNull('download_state')
                    ->orWhereIn('download_state', [
                        FileAsset::DOWNLOAD_STATE_PENDING,
                        FileAsset::DOWNLOAD_STATE_FAILED,
                    ]);
            })
            ->lazyById(500)
            ->each(function (FileAsset $asset) use (&$ids, $retryPermanentErrors): void {
                if (
                    ! $retryPermanentErrors
                    && $asset->download_state === FileAsset::DOWNLOAD_STATE_FAILED
                    && $this->isPermanentDownloadError($asset->download_error)
                ) {
                    return;
                }

                $ids[] = (int) $asset->id;
            });

        return $ids;
    }

    public function isPermanentDownloadError(?string $error): bool
    {
        $error = trim((string) $error);
        if ($error === '') {
            return false;
        }

        if (! preg_match('/HTTP\s+(\d{3})/i', $error, $matches)) {
            return false;
        }

        $status = (int) ($matches[1] ?? 0);
        if ($status < 400 || $status >= 500) {
            return false;
        }

        return ! in_array($status, [408, 429], true);
    }

    public function dispatchDownloadBatch(
        array $assetIds,
        ?string $workflowContextId = null,
        ?int $initiatedByUserId = null,
        ?string $initiatedByEmail = null,
        ?string $initiatedByRole = null
    ): ?Batch
    {
        if ($assetIds === []) {
            return null;
        }

        $queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
        $batchNamePrefix = trim((string) config('raiida.sync.download_batch_name', 'revizyseeder-fetch-downloads'));
        $batchName = ($batchNamePrefix !== '' ? $batchNamePrefix : 'revizyseeder-fetch-downloads')
            . '-' . now()->format('YmdHis');
        $batchChunkSize = max(50, (int) config('raiida.sync.download_batch_chunk_size', 300));

        $batch = Bus::batch([])
            ->name($batchName)
            ->onQueue($queue)
            ->allowFailures()
            ->dispatch();

        foreach (array_chunk($assetIds, $batchChunkSize) as $assetIdChunk) {
            $jobs = array_map(
                static fn (int $assetId): DownloadFileAssetJob => new DownloadFileAssetJob(
                    $assetId,
                    $workflowContextId,
                    $initiatedByUserId,
                    $initiatedByEmail,
                    $initiatedByRole
                ),
                $assetIdChunk
            );

            $batch->add($jobs);
            unset($jobs);
        }

        return $batch;
    }

    private function runInternal(bool $downloadInline): array
    {
        $this->recoverStaleDownloads();

        $metadataItems = $this->fetchMetadata();
        [$items, $metadataSummary] = $this->normalizeMetadataItems($metadataItems);

        $summary = [
            'raw_total' => $metadataSummary['raw_total'],
            'total' => count($items),
            'duplicates' => $metadataSummary['duplicates'],
            'processed' => 0,
            'downloaded' => 0,
            'skipped' => 0,
            'pending' => 0,
            'failed' => 0,
            'locked' => false,
        ];

        $lock = $this->acquireSyncLock();
        if ($lock === null) {
            $summary['locked'] = true;
            Log::info('Raiida sync skipped: another sync is already running.', [
                'lock_key' => (string) config('raiida.sync.lock_key', 'revizyseeder-sync-files'),
            ]);

            return $summary;
        }

        try {
            foreach ($items as $item) {
                $summary['processed']++;

                try {
                    $status = $this->processItem((array) $item, $downloadInline);
                    if (array_key_exists($status, $summary)) {
                        $summary[$status]++;
                    }
                } catch (Throwable $e) {
                    $summary['failed']++;
                    Log::warning('Raiida sync failed for one item', [
                        'item' => $item,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } finally {
            $lock->release();
        }

        return $summary;
    }

    public function downloadExistingAsset(FileAsset $asset): string
    {
        $asset->loadMissing('week.period.subject.grade');

        $relativePath = trim((string) $asset->local_path);
        if ($relativePath === '') {
            $relativePath = $this->buildFallbackRelativePath($asset);
            $asset->local_path = $relativePath;
        }

        $absolutePath = $this->filesRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        $originalUrl = trim((string) $asset->original_url);
        if ($originalUrl === '') {
            $originalUrl = $this->buildOriginalUrl($asset->filename);
            $asset->original_url = $originalUrl;
        }

        if ($this->hasCompleteLocalFile($asset, $absolutePath)) {
            $this->markDownloaded($asset, $absolutePath);

            return 'skipped';
        }

        $this->prepareTargetForFreshDownload($asset, $absolutePath);

        $this->markDownloading($asset);

        $downloadError = null;
        $downloaded = $this->downloadFile(
            $originalUrl,
            $absolutePath,
            fn (int $percent) => $this->persistProgress($asset->id, $percent),
            $downloadError
        );
        if (! $downloaded) {
            $this->markFailed($asset, $downloadError);

            return 'failed';
        }

        $this->markDownloaded($asset, $absolutePath);

        return 'downloaded';
    }

    private function fetchMetadata(): array
    {
        $url = (string) config('raiida.sync.metadata_url');
        if ($url === '') {
            return [];
        }

        try {
            $response = Http::timeout(30)->acceptJson()->get($url);
            if (! $response->successful()) {
                Log::warning('Raiida sync metadata request failed', [
                    'status' => $response->status(),
                    'url' => $url,
                ]);

                return [];
            }

            $payload = $response->json();

            return is_array($payload) ? $payload : [];
        } catch (Throwable $e) {
            Log::warning('Raiida sync metadata request exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: array{raw_total: int, duplicates: int}}
     */
    private function normalizeMetadataItems(array $items): array
    {
        $normalizedByKey = [];
        $duplicates = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized = $this->normalizeMetadataItem($item);
            $key = (string) ($normalized['_dedupe_key'] ?? '');

            if ($key === '') {
                continue;
            }

            if (! array_key_exists($key, $normalizedByKey)) {
                $normalizedByKey[$key] = $normalized;
                continue;
            }

            $duplicates++;
            $normalizedByKey[$key] = $this->preferNewerMetadataItem(
                $normalizedByKey[$key],
                $normalized
            );
        }

        return [
            array_values($normalizedByKey),
            [
                'raw_total' => count($items),
                'duplicates' => $duplicates,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function normalizeMetadataItem(array $item): array
    {
        $subject = trim((string) ($item['matiere'] ?? 'Unknown'));
        $grade = trim((string) ($item['niveau'] ?? 'Unknown'));
        $period = trim((string) ($item['periode'] ?? 'Unknown'));
        $week = trim((string) ($item['semaine'] ?? 'Unknown'));
        $rawPath = (string) ($item['file'] ?? $item['path'] ?? '');
        $fallbackId = (string) ($item['_id'] ?? 'None');
        $filename = $this->resolveFilename($rawPath, $fallbackId);

        $item['matiere'] = $subject;
        $item['niveau'] = $grade;
        $item['periode'] = $period;
        $item['semaine'] = $week;
        $item['_raw_path'] = $rawPath;
        $item['_normalized_filename'] = $filename;
        $item['_dedupe_key'] = implode('|', [$subject, $grade, $period, $week, $filename]);
        $item['_updated_score'] = $this->metadataTimestampScore($item);

        return $item;
    }

    /**
     * @param array<string, mixed> $existing
     * @param array<string, mixed> $candidate
     * @return array<string, mixed>
     */
    private function preferNewerMetadataItem(array $existing, array $candidate): array
    {
        $existingScore = (int) ($existing['_updated_score'] ?? 0);
        $candidateScore = (int) ($candidate['_updated_score'] ?? 0);

        return $candidateScore >= $existingScore
            ? $candidate
            : $existing;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function metadataTimestampScore(array $item): int
    {
        $updatedAt = (string) ($item['updatedAt'] ?? '');
        $createdAt = (string) ($item['createdAt'] ?? '');

        $updatedTimestamp = strtotime($updatedAt) ?: 0;
        $createdTimestamp = strtotime($createdAt) ?: 0;

        return max($updatedTimestamp, $createdTimestamp);
    }

    private function processItem(array $item, bool $downloadInline): string
    {
        $week = $this->getOrCreateHierarchy($item);

        $rawPath = (string) ($item['_raw_path'] ?? $item['file'] ?? $item['path'] ?? '');
        $filename = (string) ($item['_normalized_filename'] ?? $this->resolveFilename(
            $rawPath,
            (string) ($item['_id'] ?? 'None')
        ));

        $relativePath = $this->buildRelativePath($item, $filename);
        $absolutePath = $this->filesRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $originalUrl = $this->buildOriginalUrl($filename);

        preg_match('/_(S[1-6])(?=\b|_|\.)/', $filename, $matches);
        $sessionId = $matches[1] ?? null;

        $asset = FileAsset::query()->firstOrNew([
            'week_id' => $week->id,
            'filename' => $filename,
        ]);

        $asset->local_path = $relativePath;
        $asset->original_url = $originalUrl;
        $asset->session_id = $sessionId;
        if (! $asset->is_downloaded) {
            $asset->download_state = FileAsset::DOWNLOAD_STATE_PENDING;
            $asset->download_progress = 0;
            $asset->download_error = null;
            $asset->download_started_at = null;
            $asset->download_finished_at = null;
        }
        $asset->save();

        if ($this->hasCompleteLocalFile($asset, $absolutePath)) {
            $this->markDownloaded($asset, $absolutePath);

            return 'skipped';
        }

        if (! $downloadInline) {
            $asset->download_state = FileAsset::DOWNLOAD_STATE_PENDING;
            $asset->download_progress = 0;
            $asset->download_error = null;
            $asset->download_started_at = null;
            $asset->download_finished_at = null;
            $asset->save();

            return 'pending';
        }

        $this->prepareTargetForFreshDownload($asset, $absolutePath);

        $this->markDownloading($asset);

        $downloadError = null;
        $downloaded = $this->downloadFile(
            $originalUrl,
            $absolutePath,
            fn (int $percent) => $this->persistProgress($asset->id, $percent),
            $downloadError
        );
        if (! $downloaded) {
            $this->markFailed($asset, $downloadError);

            return 'failed';
        }

        $this->markDownloaded($asset, $absolutePath);

        return 'downloaded';
    }

    private function getOrCreateHierarchy(array $item): Week
    {
        $gradeName = trim((string) ($item['niveau'] ?? 'Unknown'));
        $subjectName = trim((string) ($item['matiere'] ?? 'Unknown'));
        $periodName = trim((string) ($item['periode'] ?? 'Unknown'));
        $weekName = trim((string) ($item['semaine'] ?? 'Unknown'));

        $grade = Grade::query()->firstOrCreate(
            ['name' => $gradeName],
            ['code' => $this->normalizeGradeCode($gradeName)]
        );

        if ($grade->code === null || $grade->code === '') {
            $grade->code = $this->normalizeGradeCode($gradeName);
            $grade->save();
        }

        $subject = Subject::query()->firstOrCreate(
            ['name' => $subjectName, 'grade_id' => $grade->id],
            ['code' => $subjectName]
        );

        $period = Period::query()->firstOrCreate(
            ['name' => $periodName, 'subject_id' => $subject->id],
            ['code' => $periodName]
        );

        return Week::query()->firstOrCreate(
            ['name' => $weekName, 'period_id' => $period->id],
            ['code' => $weekName]
        );
    }

    private function buildRelativePath(array $item, string $filename): string
    {
        $subject = trim((string) ($item['matiere'] ?? 'Unknown'));
        $grade = trim((string) ($item['niveau'] ?? 'Unknown'));
        $period = trim((string) ($item['periode'] ?? 'Unknown'));
        $week = trim((string) ($item['semaine'] ?? 'Unknown'));

        return implode('/', [
            $subject,
            'niveau_' . $grade,
            'periode_' . $period,
            'semaine_' . $week,
            $filename,
        ]);
    }

    private function downloadFile(string $url, string $targetPath, ?callable $onProgress = null, ?string &$error = null): bool
    {
        $headers = [
            'User-Agent' => (string) config('raiida.sync.user_agent'),
        ];

        $appKey = (string) config('raiida.sync.app_key');
        if ($appKey !== '') {
            $headers['x-app-key'] = $appKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->connectTimeout(15)
                ->timeout(600) // Increase total timeout to 10 minutes for very large files
                ->retry(2, 1000, null, false)
                ->withOptions([
                    'sink' => $targetPath,
                    // Abort if download speed drops below 1 byte/s for 15 seconds
                    'curl' => [
                        CURLOPT_LOW_SPEED_LIMIT => 1,
                        CURLOPT_LOW_SPEED_TIME => 15,
                    ],
                    'progress' => function (
                        int | float $downloadTotal,
                        int | float $downloadedBytes,
                        int | float $uploadTotal,
                        int | float $uploadedBytes
                    ) use ($onProgress): void {
                        if (! $onProgress) {
                            return;
                        }

                        if ($downloadTotal > 0) {
                            $percent = (int) floor(($downloadedBytes / $downloadTotal) * 100);
                            $onProgress(max(0, min(99, $percent)));

                            return;
                        }

                        if ($downloadedBytes > 0) {
                            $onProgress(10);
                        }
                    },
                ])
                ->get($url);

            if (! $response->successful() || ! is_file($targetPath)) {
                $error = 'HTTP ' . $response->status();

                if (is_file($targetPath)) {
                    @unlink($targetPath);
                }

                return false;
            }

            if ($onProgress) {
                $onProgress(100);
            }

            return true;
        } catch (Throwable $e) {
            Log::warning('Raiida file download failed', [
                'url' => $url,
                'target' => $targetPath,
                'error' => $e->getMessage(),
            ]);

            $error = $e->getMessage();

            if (is_file($targetPath)) {
                @unlink($targetPath);
            }

            return false;
        }
    }

    private function hasValidPptExtension(string $filename): bool
    {
        return (bool) preg_match('/\.(pptx|ppsx|ppt)$/i', $filename);
    }

    private function normalizeGradeCode(string $name): string
    {
        $trimmed = trim($name);
        $upper = strtoupper($trimmed);

        if (str_starts_with($upper, 'N')) {
            return $upper;
        }

        if (is_numeric($trimmed)) {
            return 'N' . $trimmed;
        }

        return $upper;
    }

    private function buildFallbackRelativePath(FileAsset $asset): string
    {
        $subject = trim((string) ($asset->week?->period?->subject?->name ?? 'Unknown'));
        $grade = trim((string) ($asset->week?->period?->subject?->grade?->name ?? 'Unknown'));
        $period = trim((string) ($asset->week?->period?->name ?? 'Unknown'));
        $week = trim((string) ($asset->week?->name ?? 'Unknown'));

        return implode('/', [
            $subject,
            'niveau_' . $grade,
            'periode_' . $period,
            'semaine_' . $week,
            $asset->filename,
        ]);
    }

    private function filesRoot(): string
    {
        return rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);
    }

    private function buildOriginalUrl(string $filename): string
    {
        $baseUrl = rtrim((string) config('raiida.sync.file_base_url'), '/');

        return $baseUrl . '/' . ltrim($filename, '/');
    }

    private function resolveFilename(string $rawPath, string $fallbackId): string
    {
        $filename = basename(trim($rawPath));

        if ($filename === '' || ! $this->hasValidPptExtension($filename)) {
            return 'file_' . $fallbackId . '.pptx';
        }

        return $filename;
    }

    private function acquireSyncLock(): ?Lock
    {
        $lockKey = (string) config('raiida.sync.lock_key', 'revizyseeder-sync-files');
        $lockSeconds = max(60, (int) config('raiida.sync.lock_seconds', 7200));
        $lock = Cache::lock($lockKey, $lockSeconds);

        if (! $lock->get()) {
            return null;
        }

        return $lock;
    }

    private function recoverStaleDownloads(): void
    {
        $assets = FileAsset::query()
            ->where('download_state', FileAsset::DOWNLOAD_STATE_DOWNLOADING)
            ->get();

        foreach ($assets as $asset) {
            $relativePath = trim((string) $asset->local_path);
            if ($relativePath === '') {
                $relativePath = $this->buildFallbackRelativePath($asset);
                $asset->local_path = $relativePath;
            }

            $absolutePath = $this->filesRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

            if ($this->hasCompleteLocalFile($asset, $absolutePath)) {
                $this->markDownloaded($asset, $absolutePath);

                continue;
            }

            $asset->download_state = FileAsset::DOWNLOAD_STATE_PENDING;
            $asset->is_downloaded = false;
            $asset->size_bytes = 0;
            $asset->downloaded_at = null;
            $asset->download_progress = min(99, max(0, (int) $asset->download_progress));
            if (! is_file($absolutePath)) {
                $asset->download_progress = 0;
            }
            $asset->download_error = 'Recovered interrupted download. Will continue on next fetch.';
            $asset->download_started_at = null;
            $asset->download_finished_at = now();
            $asset->save();
        }
    }

    private function hasCompleteLocalFile(FileAsset $asset, string $absolutePath): bool
    {
        if (! is_file($absolutePath)) {
            return false;
        }

        if (! $asset->is_downloaded) {
            // Keep first-sync parity only for newly discovered metadata rows that
            // have never started a download attempt.
            $isFreshMetadataAsset = $asset->wasRecentlyCreated
                && $asset->download_state === FileAsset::DOWNLOAD_STATE_PENDING
                && (int) $asset->download_progress === 0;

            if (! $isFreshMetadataAsset) {
                return false;
            }
        }

        $onDiskSize = filesize($absolutePath);
        if (! is_int($onDiskSize) || $onDiskSize <= 0) {
            return false;
        }

        $recordedSize = (int) $asset->size_bytes;
        if ($recordedSize > 0 && $recordedSize !== $onDiskSize) {
            return false;
        }

        return true;
    }

    private function prepareTargetForFreshDownload(FileAsset $asset, string $absolutePath): void
    {
        if (is_file($absolutePath) && ! $this->hasCompleteLocalFile($asset, $absolutePath)) {
            @unlink($absolutePath);
        }

        $directory = dirname($absolutePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    private function markDownloading(FileAsset $asset): void
    {
        $asset->is_downloaded = false;
        $asset->size_bytes = 0;
        $asset->downloaded_at = null;
        $asset->download_state = FileAsset::DOWNLOAD_STATE_DOWNLOADING;
        $asset->download_progress = 0;
        $asset->download_error = null;
        $asset->download_started_at = now();
        $asset->download_finished_at = null;
        $asset->save();
    }

    private function markDownloaded(FileAsset $asset, string $absolutePath): void
    {
        $asset->is_downloaded = true;
        $asset->size_bytes = (int) filesize($absolutePath);
        $asset->downloaded_at = now();
        $asset->download_state = FileAsset::DOWNLOAD_STATE_DOWNLOADED;
        $asset->download_progress = 100;
        $asset->download_error = null;
        $asset->download_finished_at = now();
        $asset->save();
    }

    private function markFailed(FileAsset $asset, ?string $error): void
    {
        $asset->is_downloaded = false;
        $asset->size_bytes = 0;
        $asset->downloaded_at = null;
        $asset->download_state = FileAsset::DOWNLOAD_STATE_FAILED;
        $asset->download_error = $error;
        $asset->download_finished_at = now();
        $asset->save();
    }

    private function persistProgress(int $assetId, int $percent): void
    {
        static $lastPersist = [];
        $percent = max(0, min(100, $percent));
        $now = microtime(true);
        $previous = $lastPersist[$assetId]['percent'] ?? -1;
        $previousAt = $lastPersist[$assetId]['time'] ?? 0.0;

        if ($percent <= $previous) {
            return;
        }

        if ($percent < 100 && ($percent - $previous) < 2 && ($now - $previousAt) < 0.75) {
            return;
        }

        FileAsset::query()
            ->whereKey($assetId)
            ->update([
                'download_progress' => $percent,
                'updated_at' => now(),
            ]);

        $lastPersist[$assetId] = [
            'percent' => $percent,
            'time' => $now,
        ];
    }
}
