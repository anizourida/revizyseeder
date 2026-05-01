<?php

namespace App\Console\Commands;

use App\Models\Raiida\FileAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class RevizySeederHydrateFilesCommand extends Command
{
    protected $signature = 'revizyseeder:hydrate-files
        {--source=/Users/macbook/Rida/fichiers-raiida/files : Source files root}
        {--mode=copy : Transfer mode: copy|hardlink|symlink}
        {--chunk=200 : Chunk size for DB iteration}
        {--skip-transfer : Do not copy/link, only sync DB from target files}
        {--skip-db : Do not update DB states}
        {--dry-run : Preview actions without changing files or DB}
        {--limit=0 : Limit number of file assets processed (0 = all)}';

    protected $description = 'Hydrate local files from existing Raiida files folder and align file_assets download state.';

    public function handle(): int
    {
        $sourceRoot = rtrim((string) $this->option('source'), DIRECTORY_SEPARATOR);
        $targetRoot = rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);
        $mode = strtolower(trim((string) $this->option('mode')));
        $chunkSize = max(50, (int) $this->option('chunk'));
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');
        $skipTransfer = (bool) $this->option('skip-transfer');
        $skipDb = (bool) $this->option('skip-db');

        if (! in_array($mode, ['copy', 'hardlink', 'symlink'], true)) {
            $this->error("Invalid --mode value: {$mode}. Allowed: copy|hardlink|symlink");

            return self::FAILURE;
        }

        if (! $skipTransfer && ! is_dir($sourceRoot)) {
            $this->error("Source directory not found: {$sourceRoot}");

            return self::FAILURE;
        }

        if ($targetRoot === '') {
            $this->error('Target files root is empty. Check RAIIDA_FILES_ROOT.');

            return self::FAILURE;
        }

        if (! $dryRun && ! is_dir($targetRoot)) {
            File::ensureDirectoryExists($targetRoot);
        }

        $query = FileAsset::query()
            ->select(['id', 'local_path', 'is_downloaded', 'download_state', 'size_bytes', 'downloaded_at'])
            ->whereNotNull('local_path')
            ->orderBy('id');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->warn('No file assets found with local_path.');

            return self::SUCCESS;
        }

        $this->line("Source: {$sourceRoot}");
        $this->line("Target: {$targetRoot}");
        $this->line("Mode: {$mode}" . ($dryRun ? ' (dry-run)' : ''));

        $stats = [
            'processed' => 0,
            'invalid_path' => 0,
            'target_exists' => 0,
            'source_missing' => 0,
            'transferred' => 0,
            'transfer_failed' => 0,
            'db_marked_downloaded' => 0,
            'db_reset_pending' => 0,
            'db_unchanged' => 0,
        ];

        $progress = $this->output->createProgressBar($total);
        $progress->start();

        try {
            $query->chunkById($chunkSize, function ($assets) use (
                $sourceRoot,
                $targetRoot,
                $mode,
                $skipTransfer,
                $skipDb,
                $dryRun,
                &$stats,
                $progress
            ): void {
                foreach ($assets as $asset) {
                    $stats['processed']++;
                    $relativePath = $this->normalizeRelativePath((string) $asset->local_path);

                    if ($relativePath === null) {
                        $stats['invalid_path']++;
                        $progress->advance();

                        continue;
                    }

                    $sourcePath = $sourceRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
                    $targetPath = $targetRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

                    $targetExists = is_file($targetPath);
                    $sourceExists = is_file($sourcePath);

                    if ($targetExists) {
                        $stats['target_exists']++;
                    } elseif (! $sourceExists) {
                        $stats['source_missing']++;
                    }

                    if (! $targetExists && ! $skipTransfer && $sourceExists) {
                        $transferred = $dryRun ? true : $this->transferFile($sourcePath, $targetPath, $mode);

                        if ($transferred) {
                            $stats['transferred']++;
                        } else {
                            $stats['transfer_failed']++;
                        }

                        clearstatcache(true, $targetPath);
                        $targetExists = is_file($targetPath) || ($dryRun && $transferred);
                    }

                    if (! $skipDb) {
                        $updated = $this->syncRecordState(
                            assetId: (int) $asset->id,
                            targetPath: $targetPath,
                            targetExists: $targetExists,
                            isDownloaded: (bool) $asset->is_downloaded,
                            currentState: (string) $asset->download_state,
                            currentSize: (int) $asset->size_bytes,
                            currentDownloadedAt: $asset->downloaded_at,
                            dryRun: $dryRun,
                        );

                        if ($updated === 'downloaded') {
                            $stats['db_marked_downloaded']++;
                        } elseif ($updated === 'pending') {
                            $stats['db_reset_pending']++;
                        } else {
                            $stats['db_unchanged']++;
                        }
                    }

                    $progress->advance();
                }
            });
        } finally {
            $progress->finish();
            $this->newLine(2);
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Processed', (string) $stats['processed']],
                ['Target already exists', (string) $stats['target_exists']],
                ['Transferred', (string) $stats['transferred']],
                ['Source missing', (string) $stats['source_missing']],
                ['Transfer failed', (string) $stats['transfer_failed']],
                ['Invalid local_path', (string) $stats['invalid_path']],
                ['DB marked downloaded', (string) $stats['db_marked_downloaded']],
                ['DB reset to pending', (string) $stats['db_reset_pending']],
                ['DB unchanged', (string) $stats['db_unchanged']],
            ]
        );

        if ($dryRun) {
            $this->comment('Dry-run complete. No file or DB changes were applied.');
        } else {
            $this->info('Hydration complete.');
        }

        return self::SUCCESS;
    }

    private function normalizeRelativePath(string $relativePath): ?string
    {
        $normalized = str_replace('\\', '/', trim($relativePath));
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return null;
        }

        $parts = explode('/', $normalized);
        foreach ($parts as $part) {
            if ($part === '' || $part === '.' || $part === '..') {
                return null;
            }
        }

        return $normalized;
    }

    private function transferFile(string $sourcePath, string $targetPath, string $mode): bool
    {
        try {
            File::ensureDirectoryExists(dirname($targetPath));

            return match ($mode) {
                'hardlink' => link($sourcePath, $targetPath),
                'symlink' => symlink($sourcePath, $targetPath),
                default => copy($sourcePath, $targetPath),
            };
        } catch (Throwable) {
            return false;
        }
    }

    private function syncRecordState(
        int $assetId,
        string $targetPath,
        bool $targetExists,
        bool $isDownloaded,
        string $currentState,
        int $currentSize,
        mixed $currentDownloadedAt,
        bool $dryRun
    ): string {
        if ($targetExists) {
            $sizeBytes = $dryRun
                ? max($currentSize, 1)
                : (is_file($targetPath) ? ((int) filesize($targetPath)) : 0);

            $mustUpdate = (! $isDownloaded)
                || ($currentState !== FileAsset::DOWNLOAD_STATE_DOWNLOADED)
                || ($sizeBytes > 0 && $sizeBytes !== $currentSize);

            if (! $mustUpdate) {
                return 'unchanged';
            }

            if (! $dryRun) {
                $now = now();
                DB::table('file_assets')
                    ->where('id', $assetId)
                    ->update([
                        'is_downloaded' => true,
                        'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
                        'download_progress' => 100,
                        'download_error' => null,
                        'download_started_at' => null,
                        'download_finished_at' => $now->toDateTimeString(),
                        'downloaded_at' => $currentDownloadedAt ?: $now->toDateTimeString(),
                        'size_bytes' => max($sizeBytes, $currentSize),
                        'updated_at' => $now->toDateTimeString(),
                    ]);
            }

            return 'downloaded';
        }

        $shouldResetToPending = $isDownloaded
            || ($currentState === FileAsset::DOWNLOAD_STATE_DOWNLOADING)
            || ($currentState === FileAsset::DOWNLOAD_STATE_DOWNLOADED);

        if (! $shouldResetToPending) {
            return 'unchanged';
        }

        if (! $dryRun) {
            DB::table('file_assets')
                ->where('id', $assetId)
                ->update([
                    'is_downloaded' => false,
                    'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
                    'download_progress' => 0,
                    'download_started_at' => null,
                    'download_finished_at' => null,
                    'updated_at' => now()->toDateTimeString(),
                ]);
        }

        return 'pending';
    }
}
