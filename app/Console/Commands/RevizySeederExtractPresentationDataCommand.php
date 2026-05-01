<?php

namespace App\Console\Commands;

use App\Jobs\Raiida\ExtractPresentationDataJob;
use App\Models\Raiida\FileAsset;
use App\Services\Raiida\PresentationDataExtractionService;
use Illuminate\Console\Command;
use Throwable;

class RevizySeederExtractPresentationDataCommand extends Command
{
    protected $signature = 'revizyseeder:extract-presentation-data
        {--id=* : Specific file_asset ID(s)}
        {--force : Re-extract even when data already exists}
        {--queue : Dispatch extraction jobs instead of running inline}
        {--chunk=100 : Chunk size for inline mode}
        {--limit=0 : Limit number of assets (0 = all)}';

    protected $description = 'Extract presentation JSON + media assets from downloaded PPT files.';

    public function handle(PresentationDataExtractionService $service): int
    {
        $ids = collect((array) $this->option('id'))
            ->map(static fn (mixed $value): int => (int) $value)
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $force = (bool) $this->option('force');
        $queueMode = (bool) $this->option('queue');
        $chunkSize = max(25, (int) $this->option('chunk'));
        $limit = max(0, (int) $this->option('limit'));

        $query = FileAsset::query()
            ->select(['id', 'filename', 'is_downloaded', 'local_path', 'is_presentation_data_extracted', 'presentation_json_path'])
            ->where('is_downloaded', true)
            ->whereNotNull('local_path')
            ->where(function ($extensionQuery): void {
                $extensionQuery
                    ->whereRaw('LOWER(filename) LIKE ?', ['%.pptx'])
                    ->orWhereRaw('LOWER(filename) LIKE ?', ['%.ppsx']);
            })
            ->orderBy('id');

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        } elseif (! $force) {
            $query->where(function ($stateQuery): void {
                $stateQuery
                    ->where('is_presentation_data_extracted', false)
                    ->orWhereNull('presentation_json_path');
            });
        }

        $assetIds = $query->pluck('id')->all();
        if ($limit > 0) {
            $assetIds = array_slice($assetIds, 0, $limit);
        }

        if ($assetIds === []) {
            $this->warn('No downloaded file assets matched your selection.');

            return self::SUCCESS;
        }

        if ($queueMode) {
            foreach ($assetIds as $assetId) {
                ExtractPresentationDataJob::dispatch((int) $assetId, $force);
            }

            $this->info('Queued presentation extraction jobs: ' . count($assetIds));
            $this->line('Queue: ' . (string) config('raiida.presentation_data.queue', 'revizyseeder-workflows'));

            return self::SUCCESS;
        }

        $stats = [
            'processed' => 0,
            'extracted' => 0,
            'from_cache' => 0,
            'failed' => 0,
        ];

        $progress = $this->output->createProgressBar(count($assetIds));
        $progress->start();

        try {
            foreach (array_chunk($assetIds, $chunkSize) as $chunk) {
                $assets = FileAsset::query()->whereIn('id', $chunk)->orderBy('id')->get();

                foreach ($assets as $asset) {
                    $stats['processed']++;

                    try {
                        $summary = $service->extractFromFileAsset($asset, $force);

                        if ((bool) ($summary['from_cache'] ?? false)) {
                            $stats['from_cache']++;
                        } else {
                            $stats['extracted']++;
                        }
                    } catch (Throwable $exception) {
                        $stats['failed']++;
                        $this->newLine();
                        $this->error('ID ' . $asset->id . ' (' . $asset->filename . '): ' . $exception->getMessage());
                    }

                    $progress->advance();
                }
            }
        } finally {
            $progress->finish();
            $this->newLine(2);
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Processed', (string) $stats['processed']],
                ['Extracted', (string) $stats['extracted']],
                ['Cached', (string) $stats['from_cache']],
                ['Failed', (string) $stats['failed']],
            ]
        );

        if ($stats['failed'] > 0) {
            $this->warn('Presentation extraction completed with failures.');

            return self::FAILURE;
        }

        $this->info('Presentation extraction completed.');

        return self::SUCCESS;
    }
}
