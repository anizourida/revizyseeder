<?php

use App\Models\Raiida\VocabularyItem;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = VocabularyItem::whereNull('audio_path')->orWhere('audio_path', '')->get();

$count = 0;
foreach ($items as $item) {
    // Generate the path: "une_école.wav"
    $word = mb_strtolower(trim($item->word), 'UTF-8');
    $word = str_replace([' ', '\'', '’'], ['_', '_', '_'], $word);
    
    // remove any multiple underscores
    $word = preg_replace('/_+/', '_', $word);
    
    $item->audio_path = $word . '.wav';
    $item->save();
    $count++;
}

echo "Filled audio_path for {$count} vocabulary items.\n";
