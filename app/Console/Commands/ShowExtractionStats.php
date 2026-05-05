<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowExtractionStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-extraction-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows the percentage of pages that have been extracted (OCR)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $total = \App\Models\Raiida\Page::count();
        
        if ($total === 0) {
            $this->warn('No pages found in the database.');
            return;
        }

        $olmocr = \App\Models\Raiida\Page::whereNotNull('ocr_olmocr_path')->count();
        $chandra = \App\Models\Raiida\Page::whereNotNull('ocr_chandra_path')->count();
        $fullText = \App\Models\Raiida\Page::whereNotNull('ocr_full_text_path')->count();
        
        // At least one extraction
        $any = \App\Models\Raiida\Page::where(function($query) {
            $query->whereNotNull('ocr_olmocr_path')
                  ->orWhereNotNull('ocr_chandra_path')
                  ->orWhereNotNull('ocr_full_text_path');
        })->count();

        $percentage = round(($any / $total) * 100, 2);

        $this->info("Extraction Statistics:");
        $this->line("----------------------");
        $this->line("Total Pages:        $total");
        $this->line("OlmOCR Extracted:   $olmocr (" . round(($olmocr / $total) * 100, 2) . "%)");
        $this->line("Chandra Extracted:  $chandra (" . round(($chandra / $total) * 100, 2) . "%)");
        $this->line("Full Text Extracted: $fullText (" . round(($fullText / $total) * 100, 2) . "%)");
        $this->line("----------------------");
        
        if ($percentage >= 100) {
            $this->info("Overall Progress:   $percentage% (COMPLETE)");
        } elseif ($percentage >= 50) {
            $this->info("Overall Progress:   $percentage%");
        } else {
            $this->warn("Overall Progress:   $percentage%");
        }

        $this->line("Remaining:          " . ($total - $any));
    }
}
