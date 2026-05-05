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
                            {--delay=30 : Seconds between each job (default 30)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedules text extraction jobs using olmOCR with a 30-second delay between each job.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gradeId = $this->argument('grade');
        $delayIncrement = (int) $this->option('delay');

        $query = Page::whereNotNull('md5_checksum')
            ->whereNull('ocr_olmocr_path');

        if ($gradeId) {
            $this->info("Fetching pages needing text extraction for Grade ID: {$gradeId}...");
            $query->where('grade_id', $gradeId);
        } else {
            $this->info("Fetching pages needing text extraction across ALL grades...");
        }

        // Only dispatch one job per unique image checksum to avoid redundant LM Studio calls
        $pages = $query->get()->unique('md5_checksum');

        if ($pages->isEmpty()) {
            $this->warn("No pages found that require text extraction.");
            return;
        }

        $count = $pages->count();
        $this->info("Found {$count} unique images needing text extraction.");
        $this->info("Dispatching jobs with a {$delayIncrement}-second delay between each...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($pages->values() as $index => $page) {
            $seconds = $index * $delayIncrement;
            
            // Dispatch with mode = 'text_only' because page numbers are already handled
            RevizySeederLMStudioOCRJob::dispatch($page->id, 'allenai/olmocr-2-7b', 'text_only')
                ->delay(now()->addSeconds($seconds));

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully scheduled {$count} jobs.");
        $this->info("Total queue time required: " . gmdate("H:i:s", $count * $delayIncrement) . " (Hours:Minutes:Seconds).");
    }
}
