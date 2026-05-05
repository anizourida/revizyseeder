<?php

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Week;
use App\Models\Raiida\Subject;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Period;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filesRoot = base_path('files');
$summary = [
    'updated_size' => 0,
    'already_correct' => 0,
    'missing_on_disk' => 0,
    'added_to_db' => 0,
    'errors' => []
];

echo "Step 1: Syncing existing database records with disk...\n";

$assets = FileAsset::where('is_downloaded', 1)->get();
foreach ($assets as $asset) {
    $fullPath = $filesRoot . '/' . $asset->local_path;
    if (file_exists($fullPath)) {
        $realSize = filesize($fullPath);
        if ($asset->size_bytes != $realSize) {
            $asset->size_bytes = $realSize;
            $asset->save();
            $summary['updated_size']++;
        } else {
            $summary['already_correct']++;
        }
    } else {
        $summary['missing_on_disk']++;
    }
}

echo "Step 2: Scanning disk for orphan files...\n";

$allFiles = File::allFiles($filesRoot);
foreach ($allFiles as $file) {
    if ($file->getExtension() !== 'pptx') continue;

    $relativePath = str_replace($filesRoot . '/', '', $file->getRealPath());
    $filename = $file->getFilename();

    // Check if this path exists in DB
    $exists = FileAsset::where('local_path', $relativePath)->exists();
    if (!$exists) {
        // Attempt to parse metadata from path
        // Pattern: Subject/niveau_X/periode_Y/semaine_Z/filename
        $parts = explode('/', $relativePath);
        $weekId = null;

        if (count($parts) >= 4) {
            $subjectCode = $parts[0];
            $gradeNum = (int) str_replace('niveau_', '', $parts[1]);
            $periodNum = (int) str_replace('periode_', '', $parts[2]);
            $weekNum = (int) str_replace('semaine_', '', $parts[3]);

            // Find matching week
            $week = Week::where('week_number', $weekNum)
                ->whereHas('period', function($q) use ($periodNum, $gradeNum, $subjectCode) {
                    $q->where('period_number', $periodNum)
                      ->whereHas('subject', function($q) use ($gradeNum, $subjectCode) {
                          $q->where('code', $subjectCode)
                            ->whereHas('grade', function($q) use ($gradeNum) {
                                $q->where('grade_number', $gradeNum);
                            });
                      });
                })->first();
            
            if ($week) {
                $weekId = $week->id;
            }
        }

        try {
            FileAsset::create([
                'week_id' => $weekId,
                'filename' => $filename,
                'local_path' => $relativePath,
                'size_bytes' => $file->getSize(),
                'is_downloaded' => 1,
                'download_state' => 'completed',
                'downloaded_at' => now(),
            ]);
            $summary['added_to_db']++;
        } catch (\Exception $e) {
            $summary['errors'][] = "Failed to add $filename: " . $e->getMessage();
        }
    }
}

echo "\nSummary:\n";
echo "Existing records updated with correct size: {$summary['updated_size']}\n";
echo "Existing records already correct: {$summary['already_correct']}\n";
echo "Records marked as downloaded but missing on disk: {$summary['missing_on_disk']}\n";
echo "New files found on disk and added to DB: {$summary['added_to_db']}\n";

if (!empty($summary['errors'])) {
    echo "\nErrors:\n";
    foreach ($summary['errors'] as $error) {
        echo "- $error\n";
    }
}
