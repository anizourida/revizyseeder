<?php

use App\Models\Raiida\Page;
use App\Models\Raiida\Grade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$directories = File::directories(storage_path('app/presentation_data'));
$gradesByCode = Grade::all()->keyBy('code');

$weeksData = [];
echo "Step 1: Identifying all sessions and grouping by week...\n";

foreach ($directories as $dir) {
    $folderName = basename($dir);
    if (str_contains($folderName, '&')) continue;

    $jsonPath = $dir . '/data.json';
    if (!File::exists($jsonPath)) continue;

    $data = json_decode(File::get($jsonPath), true);
    if (!$data || !isset($data['slides'])) continue;

    $parts = explode('_', $folderName);
    if (count($parts) < 4) continue;
    
    $groupKey = $parts[0] . '_' . $parts[1] . '_' . $parts[2] . '_' . $parts[3];
    $sessionKey = $parts[4] ?? 'S1';

    foreach ($data['slides'] as $slide) {
        $hasTargetText = false;
        $slideImages = [];
        foreach ($slide['elements'] ?? [] as $element) {
            if ($element['type'] === 'text' && str_contains(strtolower($element['content'] ?? ''), 'contenu de la semaine')) {
                $hasTargetText = true;
            }
            if ($element['type'] === 'image' && isset($element['file_path'])) {
                $slideImages[] = $element['file_path'];
            }
        }
        if ($hasTargetText) {
            $weeksData[$groupKey][$sessionKey] = [
                'images' => $slideImages,
                'folder' => $folderName,
                'grade_code' => explode('&', $parts[1])[0]
            ];
        }
    }
}

echo "Step 2: Performing Smart Restoration (Cross-Session OCR Discovery)...\n";

DB::beginTransaction();
$totalRestored = 0;
$ocrLinksFound = 0;

try {
    foreach ($weeksData as $groupKey => $sessions) {
        // 1. Identify best session for images
        $bestS = null;
        $maxImg = -1;
        foreach ($sessions as $s => $info) {
            if (count($info['images']) > $maxImg) {
                $maxImg = count($info['images']);
                $bestS = $s;
            }
        }
        if (!$bestS) continue;

        $bestInfo = $sessions[$bestS];
        $imageFolder = $bestInfo['folder'];
        $gradeCode = $bestInfo['grade_code'];
        $gradeId = isset($gradesByCode[$gradeCode]) ? $gradesByCode[$gradeCode]->id : null;

        // 2. Process each image from the best session
        foreach ($bestInfo['images'] as $imageRelPath) {
            $fullImagePath = 'presentation_data/' . $imageFolder . '/' . $imageRelPath;
            $filename = basename($imageRelPath);
            $baseName = pathinfo($filename, PATHINFO_FILENAME);
            
            // Smarter OCR Discovery: Look across ALL sessions for this week
            $ocr = [
                'full_text' => null,
                'olmocr' => null,
                'chandra' => null
            ];

            foreach ($sessions as $sKey => $sInfo) {
                $checkFolder = $sInfo['folder'];
                $possibleFiles = [
                    'full_text' => "presentation_data/$checkFolder/assets/{$baseName}.html",
                    'olmocr' => "presentation_data/$checkFolder/assets/{$baseName}_olmocr.html",
                    'chandra' => "presentation_data/$checkFolder/assets/{$baseName}_chandra.html"
                ];

                foreach ($possibleFiles as $type => $relPath) {
                    if (!$ocr[$type] && File::exists(storage_path('app/' . $relPath))) {
                        $ocr[$type] = $relPath;
                    }
                }
            }

            if ($ocr['olmocr'] || $ocr['chandra'] || $ocr['full_text']) {
                $ocrLinksFound++;
            }

            Page::updateOrCreate(
                ['image_path' => $fullImagePath],
                [
                    'grade_id' => $gradeId,
                    'n_p_sem' => $imageFolder,
                    'ocr_full_text_path' => $ocr['full_text'],
                    'ocr_olmocr_path' => $ocr['olmocr'],
                    'ocr_chandra_path' => $ocr['chandra'],
                    'page_number_extraction_method' => 'manual_recovery'
                ]
            );
            $totalRestored++;
        }
    }
    DB::commit();
    echo "Successfully restored {$totalRestored} page records!\n";
    echo "Smart Linking: Found and linked OCR results for {$ocrLinksFound} pages using cross-session discovery.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
