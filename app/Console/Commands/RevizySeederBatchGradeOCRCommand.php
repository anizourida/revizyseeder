<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Raiida\Page;
use App\Jobs\RevizySeederLMStudioOCRJob;

class RevizySeederBatchGradeOCRCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revizyseeder:batch-grade-ocr 
                            {grade : The Grade ID to process}
                            {--mode=page_only : page_only or text_only}
                            {--delay=0 : Seconds between each job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled staggered OCR jobs for a specific grade booklet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gradeId = $this->argument('grade');
        $mode = $this->option('mode');
        $delayIncrement = (int) $this->option('delay');

        $this->info("Fetching pages for Grade ID: {$gradeId}...");

        // We only dispatch ONE per unique checksum because our SYNC logic (Page::updated)
        // will automatically propagate the results to all duplicates.
        $pages = Page::where('grade_id', $gradeId)
            ->whereNotNull('md5_checksum')
            ->where(function ($query) {
                $query->whereNull('page_number_extraction_method')
                      ->orWhere('page_number_extraction_method', 'NOT LIKE', '%admin%')
                      ->where('page_number_extraction_method', 'NOT LIKE', '%llm-allenai/olmocr%');
            })
            ->get()
            ->unique('md5_checksum');

        if ($pages->isEmpty()) {
            $this->warn("No unique pages found for Grade {$gradeId}.");
            return;
        }

        $count = $pages->count();
        if ($delayIncrement > 0) {
            $this->info("Found {$count} unique images. Dispatching jobs with {$delayIncrement}s staggered delay...");
        } else {
            $this->info("Found {$count} unique images. Dispatching jobs immediately...");
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($pages->values() as $index => $page) {
            $seconds = $index * $delayIncrement;
            
            $job = RevizySeederLMStudioOCRJob::dispatch($page->id, 'allenai/olmocr-2-7b', $mode);
            if ($seconds > 0) {
                $job->delay(now()->addSeconds($seconds));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully scheduled {$count} jobs." . ($delayIncrement > 0 ? " Total queue time: " . ($count * $delayIncrement) . " seconds." : ""));
    }
}
