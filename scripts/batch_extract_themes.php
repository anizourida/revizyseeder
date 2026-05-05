<?php

use App\Models\Raiida\Page;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pythonBin = base_path('.venv/bin/python');
$scriptPath = base_path('scripts/extract_page_theme.py');

echo "Fetching pages without theme color...\n";

// Get pages missing a theme color
$pages = Page::whereNull('theme_color')->get();
$total = $pages->count();

if ($total === 0) {
    echo "No pages need theme color extraction.\n";
    exit;
}

echo "Starting extraction for {$total} pages...\n\n";

$successCount = 0;
$errorCount = 0;

foreach ($pages as $i => $page) {
    $imagePath = storage_path('app/' . $page->image_path);
    $pct = round(($i + 1) / $total * 100);

    if (!File::exists($imagePath)) {
        echo "[{$pct}%] ID {$page->id} -> Missing File\n";
        $errorCount++;
        continue;
    }

    $cmd = escapeshellcmd($pythonBin) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($imagePath);
    
    // Execute python script
    $output = trim(shell_exec($cmd));
    $result = json_decode($output, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($result['status']) && $result['status'] === 'success') {
        $hex = $result['hex'];
        $page->update(['theme_color' => $hex]);
        echo "[{$pct}%] ID {$page->id} -> Found Theme: {$hex} ✓\n";
        $successCount++;
    } else {
        $errorMsg = $result['message'] ?? 'Unknown error';
        echo "[{$pct}%] ID {$page->id} -> Error: {$errorMsg}\n";
        $errorCount++;
    }
}

echo "\n=== Done ===\n";
echo "Successfully extracted: {$successCount}\n";
echo "Errors / Skipped: {$errorCount}\n";
