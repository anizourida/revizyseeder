<?php

use App\Models\Raiida\Page;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pythonBin = base_path('.venv/bin/python');
$scriptPath = base_path('scripts/extract_page_number.py');

echo "Pre-calculating data and filtering sessions...\n";

// Get all pages needing extraction
$allPages = Page::where(function($q) {
        $q->whereNull('page_number')->orWhere('page_number', '');
    })
    ->where(function($q) {
        $q->whereNull('page_number_extraction_method')
          ->orWhereNotIn('page_number_extraction_method', ['skipped', 'admin_manually', 'python_ocr_confirmed']);
    })
    ->get();

// Count pages per session to filter out sessions with <= 3 images
$sessionCounts = $allPages->groupBy('n_p_sem')->map->count();

$data = [];
foreach ($allPages as $page) {
    // SKIP sessions with 3 or fewer images
    if (($sessionCounts[$page->n_p_sem] ?? 0) <= 3) {
        continue;
    }

    $path = storage_path('app/' . $page->image_path);
    $size = File::exists($path) ? File::size($path) : 0;
    $data[] = ['page' => $page, 'size' => $size];
}

// Sort: Largest (HD) first
usort($data, function($a, $b) {
    return $b['size'] <=> $a['size'];
});

$total = count($data);
echo "Simple Hybrid Extraction (2+ Digits, HD First) — {$total} pages\n\n";

$autoSaved = 0;
$manual = 0;

foreach ($data as $i => $item) {
    $page = $item['page'];
    $fileSize = $item['size'];
    $imagePath = storage_path('app/' . $page->image_path);
    
    if ($fileSize === 0) { $manual++; continue; }

    $sizeMb = round($fileSize / 1024 / 1024, 2);
    
    // Simple Python call (no range args)
    $cmd = escapeshellcmd($pythonBin) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath);
    $output = trim(shell_exec($cmd));
    
    $parts = explode('|', $output);
    $number = $parts[0] ?? '';
    $confidence = $parts[1] ?? 'none';

    $pct = round(($i + 1) / $total * 100);

    // Python script now only returns 2-3 digits
    if (!empty($number) && in_array($confidence, ['high', 'medium'])) {
        $page->update([
            'page_number' => (int)$number,
            'page_number_extraction_method' => 'python_ocr',
            'page_number_extraction_error' => null,
        ]);
        $autoSaved++;
        echo "[{$pct}%] [{$sizeMb}MB] {$page->n_p_sem} → Page {$number} ✓ ({$confidence})\n";
    } else {
        $page->update([
            'page_number_extraction_method' => 'needs_manual',
            'page_number_extraction_error' => "OCR: {$output}",
        ]);
        $manual++;
        echo "[{$pct}%] [{$sizeMb}MB] {$page->n_p_sem} → manual (result: '{$number}')\n";
    }
}

echo "\n=== Done ===\n";
echo "Sent to Review OCR (python_ocr): {$autoSaved}\n";
echo "Left for manual: {$manual}\n";
