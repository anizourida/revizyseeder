<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExtractWritingLetters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-writing-letters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract letters/titles from Grade 1 writing activities OCR';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pages = \App\Models\Raiida\Page::where('grade_id', 1)
            ->where('activity_category', 'Activités d’écriture')
            ->orderBy('n_p_sem')
            ->get();

        if ($pages->isEmpty()) {
            $this->warn('No writing activity pages found for Grade 1.');
            return;
        }

        $results = [];

        foreach ($pages as $page) {
            $ocrPath = $page->ocr_olmocr_path ?: $page->ocr_chandra_path ?: $page->ocr_full_text_path;
            $letters = "N/A";

            if ($ocrPath) {
                $fullPath = storage_path('app/' . $ocrPath);
                if (file_exists($fullPath)) {
                    $content = file_get_contents($fullPath);
                    
                    // Priority 1: <h1> or <h2> tags (often contains the letter pair like "D - d")
                    if (preg_match_all('/<h[12]>(.*?)<\/h[12]>/i', $content, $matches)) {
                        foreach ($matches[1] as $match) {
                            $clean = trim(strip_tags($match));
                            if (!str_contains(mb_strtolower($clean), 'activité') && !str_contains(mb_strtolower($clean), 'semaine')) {
                                $letters = $clean;
                                break;
                            }
                        }
                    } 
                    
                    // Priority 2: <h3> if still N/A
                    if ($letters === "N/A" && preg_match_all('/<h3>(.*?)<\/h3>/i', $content, $matches)) {
                        foreach ($matches[1] as $match) {
                            $clean = trim(strip_tags($match));
                            if (!str_contains(mb_strtolower($clean), 'activité') && !str_contains(mb_strtolower($clean), 'semaine')) {
                                $letters = $clean;
                                break;
                            }
                        }
                    }

                    // Fallback: Just the first few characters of text if still N/A
                    if ($letters === "N/A") {
                        $text = strip_tags($content);
                        $lines = array_filter(array_map('trim', explode("\n", $text)));
                        foreach ($lines as $line) {
                            $clean = mb_strtolower($line);
                            if (!str_contains($clean, 'activité') && !str_contains($clean, 'semaine') && !str_contains($clean, 'période') && strlen($line) < 50) {
                                $letters = $line;
                                break;
                            }
                        }
                    }
                }
            }

            $results[] = [
                'n_p_sem' => $page->n_p_sem,
                'letters' => $letters,
                'id' => $page->id
            ];
        }

        $this->table(['N_P_SEM', 'Extracted Letters/Title', 'Page ID'], $results);
    }
}
