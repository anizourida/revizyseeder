<?php

use App\Models\Raiida\VocabularyItem;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = VocabularyItem::where('period', 'P4')->get();
$targetDir = '/Users/macbook/Rida/fichiers-raiida/backend/static/audios/';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$count = 0;
foreach ($items as $item) {
    if (empty($item->audio_path)) {
        continue;
    }
    
    $filePath = $targetDir . $item->audio_path;
    
    // Check if the file already exists (skip if it does)
    if (file_exists($filePath)) {
        continue;
    }
    
    // We need to clean the word string to make sure it plays well
    // Remove formatting tags like [BLUE]
    $wordToSay = preg_replace('/\[.*?\]/', '', $item->word);
    
    // Ensure wordToSay doesn't have command injection vulnerabilities
    $escapedWord = escapeshellarg(trim($wordToSay));
    $escapedPath = escapeshellarg($filePath);
    
    $cmd = "say -v Thomas {$escapedWord} --data-format=LEI16@44100 -o {$escapedPath}";
    exec($cmd);
    $count++;
}

echo "Generated {$count} audio files using 'say' command.\n";
