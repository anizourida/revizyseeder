<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Raiida\Page;
use App\Jobs\ExtractPageNumberJob;

class RevizySeederExtractPageNumbersCommand extends Command
{
    protected $signature = 'revizyseeder:extract-page-numbers
                            {--force : Re-extract even if already extracted}
                            {--grade= : Only extract for a specific grade ID}
                            {--sync : Run synchronously instead of dispatching to queue}
                            {--delay=30 : Seconds between each dispatched job}';

    protected $description = 'Dispatch jobs to extract page numbers for Pages using Python OCR script';

    public function handle()
    {
        $force = $this->option('force');
        $gradeId = $this->option('grade');
        $sync = $this->option('sync');
        $delayIncrement = (int) $this->option('delay');

        $query = Page::query();

        // Filter by grade if specified
        if ($gradeId) {
            $query->where('grade_id', $gradeId);
            $this->info("Filtering by Grade ID: {$gradeId}");
        }

        if (!$force) {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('page_number_extraction_method')
                        ->orWhere('page_number_extraction_method', '')
                        ->orWhere('page_number_extraction_method', 'ocr_failed');
                })
                ->where(function ($sub) {
                    $sub->whereNull('page_number')
                        ->orWhere('page_number', '<', 1);
                });
            });
        }

        $pages = $query->get();

        if ($pages->isEmpty()) {
            $this->info('No pages found needing page number extraction.');
            return self::SUCCESS;
        }

        $count = $pages->count();
        $this->info("Found {$count} pages to process.");

        if ($sync) {
            $this->info('Running synchronously...');
            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($pages as $page) {
                ExtractPageNumberJob::dispatchSync($page->id);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Page number extraction completed!');
        } else {
            $this->info("Dispatching {$count} jobs to queue with {$delayIncrement}s staggered delay...");
            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($pages->values() as $index => $page) {
                $seconds = $index * $delayIncrement;

                ExtractPageNumberJob::dispatch($page->id)
                    ->delay(now()->addSeconds($seconds));

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully dispatched {$count} jobs. Estimated queue time: " . gmdate('H:i:s', $count * $delayIncrement));
        }

        return self::SUCCESS;
    }
}
