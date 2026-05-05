<?php

use App\Models\Raiida\VocabularyItem;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = VocabularyItem::where('period', 'P4')->get();

$count = 0;
$updatedCount = 0;
foreach ($items as $item) {
    // Generate the path correctly with underscores instead of hyphens
    $word = mb_strtolower(trim($item->word), 'UTF-8');
    // Replace spaces, apostrophes, AND hyphens with underscores
    $word = str_replace([' ', '\'', '’', '-'], ['_', '_', '_', '_'], $word);
    
    // remove any multiple underscores
    $word = preg_replace('/_+/', '_', $word);
    
    $newPath = $word . '.wav';
    
    if ($item->audio_path !== $newPath) {
        $item->audio_path = $newPath;
        $item->save();
        $updatedCount++;
    }
    $count++;
}

echo "Checked {$count} items. Fixed audio_path for {$updatedCount} items to use underscores.\n";
