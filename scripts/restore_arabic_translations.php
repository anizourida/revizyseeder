<?php

use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\DeepLTranslationService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = $app->make(DeepLTranslationService::class);

$periods = ['P3', 'P4'];
$items = VocabularyItem::whereIn('period', $periods)
    ->where(function($query) {
        $query->whereNull('ar_translation')->orWhere('ar_translation', '');
    })
    ->get();

echo "Found " . $items->count() . " vocabulary items missing Arabic translations for periods " . implode(', ', $periods) . ".\n";

if ($items->isEmpty()) {
    echo "Nothing to translate.\n";
    exit;
}

$chunks = $items->chunk(50);
$successCount = 0;
$failedCount = 0;

foreach ($chunks as $chunk) {
    $words = $chunk->pluck('word')->toArray();
    echo "Translating chunk of " . count($words) . " words...\n";

    try {
        $translations = $service->translateBatch($words);

        if (count($translations) === count($words)) {
            $index = 0;
            foreach ($chunk as $item) {
                $item->ar_translation = $translations[$index];
                $item->save();
                $index++;
                $successCount++;
            }
            echo "Successfully updated " . count($translations) . " items.\n";
        } else {
            echo "Mismatch in translation count. Expected " . count($words) . " but got " . count($translations) . ".\n";
            $failedCount += count($words);
        }
    } catch (\Exception $e) {
        echo "Error translating chunk: " . $e->getMessage() . "\n";
        $failedCount += count($words);
    }

    // Small delay to be nice to the API
    usleep(500000); 
}

echo "\n--- Summary ---\n";
echo "Successfully translated: {$successCount}\n";
echo "Failed: {$failedCount}\n";
echo "----------------\n";
