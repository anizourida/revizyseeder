<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CategorizePagesByActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:categorize-pages-by-activity {--force : Re-process already categorized pages} {--limit= : Limit the number of pages to process} {--delay=0 : Delay in seconds between each page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Categorize pages by activity type using OCR text';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting categorization of pages by activity...');

        $query = \App\Models\Raiida\Page::query();

        if (!$this->option('force')) {
            $query->whereNull('activity_category');
        }

        if ($this->option('limit')) {
            $query->limit($this->option('limit'));
        }

        $pages = $query->get();

        if ($pages->isEmpty()) {
            $this->info('No pages to process.');
            return;
        }

        $bar = $this->output->createProgressBar($pages->count());
        $bar->start();

        $stats = [
            'processed' => 0,
            'categorized' => 0,
            'skipped' => 0,
        ];

        foreach ($pages as $page) {
            $category = $this->determineCategory($page);
            if ($category) {
                $page->update(['activity_category' => $category]);
                $stats['categorized']++;
            } else {
                $stats['skipped']++;
            }
            $stats['processed']++;
            $bar->advance();

            if ($this->option('delay') > 0) {
                sleep((int) $this->option('delay'));
            }
        }

        $bar->finish();
        $this->info("\n\nCategorization complete.");
        $this->info("Total processed: {$stats['processed']}");
        $this->info("Categorized: {$stats['categorized']}");
        $this->info("Skipped (no keyword found): {$stats['skipped']}");
    }

    private function determineCategory($page)
    {
        $ocrPaths = [
            $page->ocr_full_text_path,
            $page->ocr_olmocr_path,
            $page->ocr_chandra_path,
        ];

        foreach ($ocrPaths as $path) {
            if (!$path) continue;

            $fullPath = storage_path('app/' . $path);
            if (!file_exists($fullPath)) continue;

            $content = file_get_contents($fullPath);
            $text = strip_tags($content);
            $text = mb_strtolower($text);

            $category = $this->findCategoryInText($text);
            if ($category) {
                return $category;
            }
        }

        return null;
    }

    private function findCategoryInText($text)
    {
        // Priority 1: Exact activity phrases (case-insensitive)
        $exactPhrases = [
            'Activités de vocabulaire' => ['activités de vocabulaire'],
            'Activités orales' => ['activités orales'],
            'Activités de lecture' => ['activités de lecture'],
            'Activités d’écriture' => ['activités d’écriture', 'activités d\'écriture'],
        ];

        foreach ($exactPhrases as $categoryName => $variants) {
            foreach ($variants as $variant) {
                if (str_contains($text, mb_strtolower($variant))) {
                    return $categoryName;
                }
            }
        }

        // Priority 2: Keywords (more risky, so we look for them if exact phrase is missing)
        $keywords = [
            'Activités de vocabulaire' => ['vocabulaire'],
            'Activités orales' => ['orale'],
            'Activités de lecture' => ['lecture'],
            'Activités d’écriture' => ['écriture', 'ecriture'],
        ];

        foreach ($keywords as $categoryName => $words) {
            foreach ($words as $word) {
                // Use a simple boundary check to avoid matching "morale" for "orale"
                if (preg_match('/\b' . preg_quote(mb_strtolower($word), '/') . '\b/u', $text)) {
                    return $categoryName;
                }
            }
        }

        return null;
    }
}
