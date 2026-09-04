<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Raiida\Page;
use App\Jobs\RevizySeederLMStudioOCRJob;

class RevizySeederExtractTextOlmocrCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revizyseeder:extract-text-olmocr 
                            {grade? : Optional Grade ID to process. If omitted, processes all grades.}
                            {--book : Only extract pages that are linked to Book Pages (textbook booklet)}
                            {--delay=0 : Seconds between each job (default 0)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedules text extraction jobs using olmOCR without delay.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gradeId = $this->argument('grade');
        $forBook = (bool) $this->option('book');
        $delayIncrement = (int) $this->option('delay');

        if ($forBook) {
            $bookQuery = \App\Models\Raiida\BookPage::query()
                ->whereNotNull('page_id')
                ->whereNull('ocr_olmocr_path');

            if ($gradeId) {
                $bookQuery->whereHas('book', fn ($b) => $b->where('n', $gradeId)->orWhere('id', $gradeId));
                $this->info("Fetching missing Book Pages for Grade: {$gradeId}...");
            } else {
                $this->info("Fetching missing Book Pages across ALL grades...");
            }

            $pageIds = $bookQuery->pluck('page_id')->unique()->filter()->values();
            $pages = Page::whereIn('id', $pageIds)->whereNull('ocr_olmocr_path')->get()->unique('md5_checksum');
        } else {
            $query = Page::whereNotNull('md5_checksum')
                ->whereNull('ocr_olmocr_path')
                ->where('n_p_sem', 'NOT LIKE', '%&%');

            if ($gradeId) {
                $this->info("Fetching pages needing text extraction for Grade ID: {$gradeId}...");
                $query->where('grade_id', $gradeId);
            } else {
                $this->info("Fetching pages needing text extraction across ALL grades...");
            }

            // Only dispatch one job per unique image checksum to avoid redundant LM Studio calls
            $pages = $query->get()->unique('md5_checksum');
        }

        if ($pages->isEmpty()) {
            $this->warn("No pages found that require text extraction.");
            return;
        }

        $count = $pages->count();
        $this->info("Found {$count} unique images needing text extraction.");
        if ($delayIncrement > 0) {
            $this->info("Dispatching jobs with a {$delayIncrement}-second delay between each...");
        } else {
            $this->info("Dispatching jobs immediately without delay...");
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($pages->values() as $index => $page) {
            $seconds = $index * $delayIncrement;
            
            // Dispatch with mode = 'text_only' because page numbers are already handled
            $job = RevizySeederLMStudioOCRJob::dispatch($page->id, 'allenai/olmocr-2-7b', 'text_only');
            if ($seconds > 0) {
                $job->delay(now()->addSeconds($seconds));
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully scheduled {$count} jobs.");
        if ($delayIncrement > 0) {
            $this->info("Total queue time required: " . gmdate("H:i:s", $count * $delayIncrement) . " (Hours:Minutes:Seconds).");
        }
    }
}
