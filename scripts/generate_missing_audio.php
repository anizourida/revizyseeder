<?php

use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\AudioGenerationService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$audioService = $app->make(AudioGenerationService::class);

$items = VocabularyItem::whereNull('audio_path')->orWhere('audio_path', '')->get();

echo "Found " . $items->count() . " items missing audio_path.\n";

$success = 0;
$failed = 0;

foreach ($items as $item) {
    try {
        $result = $audioService->generateBatch([
            'item_id' => $item->id,
            'limit' => 1,
            'force' => false,
            'verbose' => false
        ]);
        
        // Ensure the item instance is fresh as generateBatch updates the db
        $item->refresh();
        if (!empty($item->audio_path)) {
            $success++;
        } else {
            $failed++;
            echo "Failed to generate audio for item {$item->id}: {$item->word}\n";
        }
    } catch (\Exception $e) {
        $failed++;
        echo "Error for item {$item->id}: " . $e->getMessage() . "\n";
    }
}

echo "Successfully generated audio for {$success} items. Failed: {$failed}.\n";
