<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Raiida\Page;
use Illuminate\Support\Facades\Storage;

class RevizySeederCheckOcrErrorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revizyseeder:check-ocr-errors 
                            {--grade= : Filter by Grade ID}
                            {--limit= : Limit the number of pages to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks extracted OCR texts for potential errors like repetitive characters or error logs.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gradeId = $this->option('grade');
        $limit = $this->option('limit');

        $query = Page::whereNotNull('ocr_olmocr_path');

        if ($gradeId) {
            $query->where('grade_id', $gradeId);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $pages = $query->get();

        if ($pages->isEmpty()) {
            $this->warn("No pages found with OCR extraction path.");
            return;
        }

        $this->info("Checking " . $pages->count() . " pages for OCR errors...");

        $errorsCount = 0;
        $results = [];

        $bar = $this->output->createProgressBar($pages->count());
        $bar->start();

        $ocrColumns = ['ocr_full_text_path', 'ocr_olmocr_path', 'ocr_chandra_path'];

        foreach ($pages as $page) {
            foreach ($ocrColumns as $col) {
                $filePath = $page->{$col};
                if (!$filePath) continue;
                
                // Check in storage/app first, then storage/app/public
                $fullPath = storage_path('app/' . $filePath);
                if (!file_exists($fullPath)) {
                    $fullPath = storage_path('app/public/' . $filePath);
                }

                if (!file_exists($fullPath)) {
                    continue; // Skip missing files for now or report them differently
                }

                $content = file_get_contents($fullPath);
                $foundErrors = $this->detectErrors($content);

                if (!empty($foundErrors)) {
                    $results[] = [
                        'id' => $page->id,
                        'path' => $filePath,
                        'error' => implode(', ', $foundErrors),
                        'sample' => $this->getSample($content, $foundErrors),
                    ];
                    $errorsCount++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($errorsCount > 0) {
            $this->error("Found {$errorsCount} pages with potential OCR errors.");
            
            $headers = ['ID', 'Path', 'Error Type(s)', 'Sample'];
            $this->table($headers, $results);
            
            // Optionally save to a CSV for easier review
            $csvPath = storage_path('app/ocr_errors_report_' . date('Ymd_His') . '.csv');
            $this->saveToCsv($csvPath, $headers, $results);
            $this->info("Detailed report saved to: {$csvPath}");
        } else {
            $this->info("No OCR errors detected.");
        }
    }

    /**
     * Detect potential errors in the content.
     */
    private function detectErrors($content)
    {
        $errors = [];

        // 1. Repetitive Letters (e.g., aaaaaaaaaaaaaaaaaaa)
        if (preg_match('/([a-zA-Z])\1{15,}/', $content)) {
            $errors[] = 'Repetitive Letters';
        }

        // 2. Repetitive Symbols (e.g., ....................)
        // We'll use a higher threshold for dots since they might be "fill in the blank"
        if (preg_match('/([^\w\s])\1{25,}/', $content, $matches)) {
            $symbol = $matches[1];
            if ($symbol === '.') {
                if (preg_match('/\.{40,}/', $content)) {
                    $errors[] = 'Excessive Dots (40+)';
                }
            } else {
                $errors[] = 'Repetitive Symbols (' . $symbol . ')';
            }
        }

        // 3. Error Keywords (including script leaks)
        $keywords = [
            'error', 'exception', 'failed', 'timeout', 'internal server error', 
            'not found', 'traceback', 'revizyseeder', 'seeder.test', '_boost'
        ];
        foreach ($keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $errors[] = "Keyword Found: {$keyword}";
            }
        }

        // 4. Repetitive Words (e.g., Farine Farine Farine...)
        // Matches a word followed by 10+ occurrences of the same word (separated by whitespace/newlines)
        $strippedContent = strip_tags($content);
        if (preg_match('/(?:\b(\w{3,})\b)(?:\s+\1\b){10,}/iu', $strippedContent, $matches)) {
            $errors[] = "Repetitive Word: '{$matches[1]}'";
        }

        // 5. Browser Logger Leak Detection
        if (str_contains($content, 'browser-logs') || str_contains($content, 'seeder.test') || str_contains($content, 'logQueue')) {
            $errors[] = 'Browser Logger Script Leaked';
        }

        // 5. Excessive Image Placeholders (e.g., Image 1 Image 2 Image 3...)
        if (preg_match_all('/Image \d+/', $content, $matches)) {
            if (count($matches[0]) > 15) {
                $errors[] = 'Excessive Image Placeholders (' . count($matches[0]) . ')';
            }
        }

        // 6. HTML tags that shouldn't be there or indicate a dump
        if (stripos($content, '<pre') !== false && stripos($content, 'stack trace') !== false) {
            $errors[] = 'Stack Trace Found';
        }

        return $errors;
    }

    /**
     * Get a small sample of the content that triggered the error.
     */
    private function getSample($content, $errors)
    {
        // Simple sample extraction: first 100 chars or match area
        $content = strip_tags($content);
        $content = str_replace(["\n", "\r", "\t"], ' ', $content);
        return substr(trim($content), 0, 50) . '...';
    }

    /**
     * Save results to a CSV file.
     */
    private function saveToCsv($path, $headers, $results)
    {
        $handle = fopen($path, 'w');
        fputcsv($handle, $headers);
        foreach ($results as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}
