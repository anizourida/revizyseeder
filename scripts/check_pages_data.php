<?php

use App\Models\Raiida\Grade;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$directories = File::directories(storage_path('app/presentation_data'));
$gradesByCode = Grade::all()->keyBy('code');

$weeksData = []; // To group by Grade_Period_Week
$stats = [
    'dirs_scanned' => 0,
    'dirs_ignored_merged' => 0,
    'consistent_weeks' => 0,
    'inconsistent_weeks' => 0,
];

echo "Dry Run v3: Checking for consistency across sessions (S1-S6) and ignoring merged classes (&)...\n\n";

foreach ($directories as $dir) {
    $folderName = basename($dir);
    
    // Rule 1: Ignore merged classes (&)
    if (str_contains($folderName, '&')) {
        $stats['dirs_ignored_merged']++;
        continue;
    }

    $stats['dirs_scanned']++;
    $jsonPath = $dir . '/data.json';
    if (!File::exists($jsonPath)) continue;

    $data = json_decode(File::get($jsonPath), true);
    if (!$data || !isset($data['slides'])) continue;

    // Resolve Grade_Period_Week (e.g. FR_N1_P1_SEM1)
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
            sort($slideImages); // Sort to compare content easily
            $weeksData[$groupKey][$sessionKey] = $slideImages;
        }
    }
}

$inconsistencies = [];
foreach ($weeksData as $groupKey => $sessions) {
    if (count($sessions) <= 1) {
        $stats['consistent_weeks']++;
        continue;
    }

    $firstSessionImages = reset($sessions);
    $isConsistent = true;
    
    foreach ($sessions as $sessionKey => $images) {
        if ($images !== $firstSessionImages) {
            $isConsistent = false;
            $inconsistencies[$groupKey][$sessionKey] = count($images);
        }
    }

    if ($isConsistent) {
        $stats['consistent_weeks']++;
    } else {
        $stats['inconsistent_weeks']++;
    }
}

echo "Summary Found (Deduplication Check):\n";
echo "Directories scanned: {$stats['dirs_scanned']}\n";
echo "Directories ignored (Merged classes &): {$stats['dirs_ignored_merged']}\n";
echo "Consistent Weeks (S1-S6 have same pages): {$stats['consistent_weeks']}\n";
echo "Inconsistent Weeks (Pages differ between sessions): {$stats['inconsistent_weeks']}\n\n";

if (!empty($inconsistencies)) {
    echo "Inconsistent Weeks Detail:\n";
    foreach ($inconsistencies as $groupKey => $details) {
        echo " - $groupKey: ";
        $counts = [];
        foreach ($weeksData[$groupKey] as $s => $imgs) {
            $counts[] = "$s (" . count($imgs) . " images)";
        }
        echo implode(', ', $counts) . "\n";
    }
} else {
    echo "Great news! All weeks checked have identical 'Contenu de la semaine' slides across all sessions (S1-S6).\n";
}

echo "\nExamples of data that will be restored (Deduplicated):\n";
$exampleCount = 0;
foreach ($weeksData as $groupKey => $sessions) {
    if ($exampleCount >= 2) break;
    $firstSession = array_key_first($sessions);
    echo "--- Example " . ($exampleCount + 1) . " --- \n";
    echo "Week Group: $groupKey\n";
    echo "Sessions linked: " . implode(', ', array_keys($sessions)) . "\n";
    echo "Images found: " . count($sessions[$firstSession]) . "\n";
    foreach ($sessions[$firstSession] as $img) {
        echo "  - $img\n";
    }
    echo "\n";
    $exampleCount++;
}
