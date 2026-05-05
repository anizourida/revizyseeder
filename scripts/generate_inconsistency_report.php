<?php

use App\Models\Raiida\Grade;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$directories = File::directories(storage_path('app/presentation_data'));
$weeksData = [];
$stats = ['scanned' => 0, 'ignored' => 0];

echo "Generating Inconsistency Report and selecting 'Best' sessions...\n";

foreach ($directories as $dir) {
    $folderName = basename($dir);
    if (str_contains($folderName, '&')) {
        $stats['ignored']++;
        continue;
    }

    $stats['scanned']++;
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
            $weeksData[$groupKey][$sessionKey] = $slideImages;
        }
    }
}

// Generate HTML Report
$html = "<html><head><style>
    table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .inconsistent { background-color: #fff3cd; }
    .best { font-weight: bold; color: green; }
</style></head><body>";
$html .= "<h1>Revizy Seeder: Page Consistency Report</h1>";
$html .= "<p>Scanned: {$stats['scanned']} | Ignored (Merged): {$stats['ignored']}</p>";
$html .= "<table><tr><th>Week Group</th><th>S1</th><th>S2</th><th>S3</th><th>S4</th><th>S5</th><th>S6</th><th>Status</th></tr>";

$bestSessions = [];

foreach ($weeksData as $groupKey => $sessions) {
    $maxImages = -1;
    $bestS = null;
    $counts = [];
    $isConsistent = true;
    $firstCount = null;

    for ($i = 1; $i <= 6; $i++) {
        $s = "S$i";
        $imgCount = isset($sessions[$s]) ? count($sessions[$s]) : 0;
        $counts[$s] = $imgCount;
        
        if ($imgCount > $maxImages) {
            $maxImages = $imgCount;
            $bestS = $s;
        }

        if ($i <= 5) { // Usually S6 is empty, so we check consistency for S1-S5
            if ($firstCount === null && isset($sessions[$s])) $firstCount = $imgCount;
            if (isset($sessions[$s]) && $imgCount !== $firstCount) $isConsistent = false;
        }
    }

    $bestSessions[$groupKey] = [
        'session' => $bestS,
        'images' => $sessions[$bestS] ?? []
    ];

    $rowClass = $isConsistent ? "" : "inconsistent";
    $html .= "<tr class='$rowClass'><td>$groupKey</td>";
    for ($i = 1; $i <= 6; $i++) {
        $s = "S$i";
        $count = $counts[$s];
        $style = ($s === $bestS) ? "class='best'" : "";
        $html .= "<td $style>$count</td>";
    }
    $html .= "<td>" . ($isConsistent ? "Consistent" : "INCONSISTENT") . "</td></tr>";
}

$html .= "</table></body></html>";
File::put(base_path('inconsistency_report.html'), $html);

echo "\nReport generated: inconsistency_report.html\n";
echo "Best sessions identified for all weeks.\n";
