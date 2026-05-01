<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RevizySeederExtractPagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revizyseeder:extract-pages';

    protected $description = 'Extract pages from presentation data slides containing "Contenu de la semaine"';

    public function handle()
    {
        $this->info('Starting pages extraction...');

        $directories = \Illuminate\Support\Facades\Storage::disk('local')->directories('presentation_data');
        
        $gradesByName = \App\Models\Raiida\Grade::all()->keyBy('name');

        $extractedCount = 0;

        foreach ($directories as $directory) {
            $jsonPath = $directory . '/data.json';
            
            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($jsonPath)) {
                continue;
            }

            $jsonData = json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get($jsonPath), true);
            
            if (!$jsonData || !isset($jsonData['slides'])) {
                continue;
            }

            // Extract grade name and n_p_sem from lesson_id (e.g., FR_N5_P3_SEM4_S6 => N5)
            $lessonId = $jsonData['lesson_id'] ?? basename($directory);
            
            // Expected format: FR_N5_P3_...
            $parts = explode('_', $lessonId);
            $gradeName = null;
            if (count($parts) >= 2) {
                // e.g. "N5" or "N5&6"
                $gradeName = str_replace('N', '', $parts[1]); 
            }

            $gradeId = null;
            if ($gradeName && isset($gradesByName[$gradeName])) {
                $gradeId = $gradesByName[$gradeName]->id;
            } elseif ($gradeName && str_contains($gradeName, '&')) {
                // Handle mixed grades by taking the first one
                $firstGrade = explode('&', $gradeName)[0];
                if (isset($gradesByName[$firstGrade])) {
                    $gradeId = $gradesByName[$firstGrade]->id;
                }
            }
            
            // Fallback
            if (!$gradeId) {
                $gradeId = $gradesByName->first()?->id;
            }

            foreach ($jsonData['slides'] as $slide) {
                if (!isset($slide['elements'])) {
                    continue;
                }

                $isTargetSlide = false;
                $images = [];

                foreach ($slide['elements'] as $element) {
                    if ($element['type'] === 'text' && str_contains(strtolower($element['content']), 'contenu de la semaine')) {
                        $isTargetSlide = true;
                    }
                    if ($element['type'] === 'image' && isset($element['file_path'])) {
                        $images[] = $element['file_path'];
                    }
                }

                if ($isTargetSlide && !empty($images)) {
                    foreach ($images as $imagePath) {
                        // The imagePath is usually 'assets/slide_X_image_Y.png' inside the directory.
                        // We need the full path relative to the disk.
                        $fullImagePath = $directory . '/' . $imagePath;
                        
                        // Copy image to public storage for Filament if needed or just use the local path
                        // For Filament PageResource, if disk is 'local', it expects public access usually, 
                        // but let's just create the record with the path string for now
                        
                        $page = \App\Models\Raiida\Page::firstOrCreate(
                            ['image_path' => $fullImagePath],
                            [
                                'grade_id' => $gradeId,
                                'n_p_sem' => $lessonId,
                            ]
                        );

                        if (!$page->page_number) {
                            $pythonBin = config('raiida.presentation_data.python_bin', env('RAIIDA_PRESENTATION_PYTHON_BIN', 'python'));
                            $scriptPath = base_path('scripts/extract_page_number.py');
                            $localPath = \Illuminate\Support\Facades\Storage::disk('local')->path($fullImagePath);
                            
                            $command = escapeshellcmd($pythonBin . ' ' . $scriptPath . ' ' . escapeshellarg($localPath));
                            $output = trim(shell_exec($command));
                            
                            if (!empty($output) && is_numeric($output)) {
                                $page->update(['page_number' => $output]);
                            }
                        }

                        $extractedCount++;
                    }
                }
            }
        }

        $this->info("Successfully extracted {$extractedCount} pages.");
        return self::SUCCESS;
    }
}
