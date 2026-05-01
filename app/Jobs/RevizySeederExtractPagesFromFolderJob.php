<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Raiida\Page;
use App\Models\Raiida\Grade;
use App\Support\RevizySeeder\WorkflowState;

class RevizySeederExtractPagesFromFolderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $directory;

    /**
     * Create a new job instance.
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
        $this->onQueue('revizyseeder-workflows');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (WorkflowState::isPaused()) {
            $this->release(300);
            return;
        }

        $jsonPath = $this->directory . '/data.json';
        
        if (!Storage::disk('local')->exists($jsonPath)) {
            return;
        }

        $jsonData = json_decode(Storage::disk('local')->get($jsonPath), true);
        
        if (!$jsonData || !isset($jsonData['slides'])) {
            return;
        }

        // Pre-fetch grades to avoid N+1 if many jobs run, though per-job it's okay
        $gradesByName = Grade::all()->keyBy('name');

        $lessonId = $jsonData['lesson_id'] ?? basename($this->directory);
        
        $parts = explode('_', $lessonId);
        $gradeName = null;
        if (count($parts) >= 2) {
            $gradeName = str_replace('N', '', $parts[1]); 
        }

        $gradeId = null;
        if ($gradeName && isset($gradesByName[$gradeName])) {
            $gradeId = $gradesByName[$gradeName]->id;
        } elseif ($gradeName && str_contains($gradeName, '&')) {
            $firstGrade = explode('&', $gradeName)[0];
            if (isset($gradesByName[$firstGrade])) {
                $gradeId = $gradesByName[$firstGrade]->id;
            }
        }
        
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
                    $fullImagePath = $this->directory . '/' . $imagePath;
                    
                    // Use updateOrCreate or firstOrCreate
                    $page = Page::firstOrCreate(
                        ['image_path' => $fullImagePath],
                        [
                            'grade_id' => $gradeId,
                            'n_p_sem' => $lessonId,
                        ]
                    );

                    // If page number is missing or suspiciously low (<10) and extraction method is empty, try OCR
                    if (empty($page->page_number_extraction_method) && (!$page->page_number || $page->page_number < 10)) {
                        $this->extractPageNumber($page, $fullImagePath);
                    }
                }
            }
        }
    }

    protected function extractPageNumber(Page $page, string $fullImagePath)
    {
        $pythonBin = config('raiida.presentation_data.python_bin', env('RAIIDA_PRESENTATION_PYTHON_BIN', 'python'));
        $scriptPath = base_path('scripts/extract_page_number.py');
        $localPath = Storage::disk('local')->path($fullImagePath);
        
        if (!file_exists($localPath)) {
            return;
        }

        $command = escapeshellcmd($pythonBin . ' ' . $scriptPath . ' ' . escapeshellarg($localPath));
        $output = trim(shell_exec($command));
        
        if (!empty($output) && is_numeric($output)) {
            $page->update([
                'page_number' => $output,
                'page_number_extraction_method' => 'ocr'
            ]);
        }
    }
}
