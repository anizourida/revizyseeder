<?php

use App\Models\Raiida\Grade;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$directories = File::directories(storage_path('app/presentation_data'));
$weeksData = [];

echo "Dry Run v4: Cross-Session OCR Discovery Strategy...\n\n";

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
                'folder' => $folderName
            ];
        }
    }
}

$examples = [];
$totalOcrLinked = 0;

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
    
    // 2. Process images and look for OCR in ANY session of this week
    foreach ($bestInfo['images'] as $imageRelPath) {
        $filename = basename($imageRelPath);
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        
        $foundOcr = [];
        foreach ($sessions as $s => $info) {
            $checkFolder = $info['folder'];
            $possibleOcr = [
                'olmocr' => "presentation_data/$checkFolder/assets/{$baseName}_olmocr.html",
                'chandra' => "presentation_data/$checkFolder/assets/{$baseName}_chandra.html"
            ];
            foreach ($possibleOcr as $type => $path) {
                if (File::exists(storage_path('app/' . $path))) {
                    $foundOcr[$type] = $path;
                }
            }
        }

        if (!empty($foundOcr)) {
            $totalOcrLinked++;
            if ($groupKey === 'FR_N6_P4_SEM2' || count($examples) < 1) {
                $examples[] = [
                    'week' => $groupKey,
                    'image_source' => $imageFolder . '/' . $imageRelPath,
                    'ocr' => $foundOcr
                ];
            }
        }
    }
}

echo "Summary of Cross-Session OCR Discovery:\n";
echo "Total Page-OCR links discovered: $totalOcrLinked\n\n";

echo "Example of Smarter Linking:\n";
foreach ($examples as $ex) {
    echo "--- Week: {$ex['week']} ---\n";
    echo "Image taken from: {$ex['image_source']}\n";
    echo "OCR Files discovered and linked:\n";
    foreach ($ex['ocr'] as $type => $path) {
        echo "  - $type found in: $path\n";
    }
    echo "\n";
}
