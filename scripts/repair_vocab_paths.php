<?php

use App\Models\Raiida\VocabularyItem;
use Illuminate\Support\Facades\File;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$periods = ['P1', 'P2', 'P3'];
$items = VocabularyItem::whereIn('period', $periods)->get();

echo "Found " . $items->count() . " vocabulary items for periods " . implode(', ', $periods) . ".\n";

$success = 0;
$missing = 0;
$already_exists = 0;

foreach ($items as $item) {
    $dbPath = $item->image_path;
    if (empty($dbPath)) continue;

    $lessonId = $item->lesson_id;
    $basename = basename($dbPath);

    // Transform slide_X_img_Y.png to slide_X_image_Y.png
    $searchBasename = str_replace('_img_', '_image_', $basename);

    $sourcePath = storage_path("app/presentation_data/{$lessonId}/assets/{$searchBasename}");
    $targetPath = public_path($dbPath);

    if (File::exists($targetPath)) {
        $already_exists++;
        continue;
    }

    if (!File::exists($sourcePath)) {
        // Try alternate extensions
        $pathInfo = pathinfo($sourcePath);
        $alternates = ['png', 'jpg', 'jpeg', 'gif'];
        foreach ($alternates as $ext) {
            $altPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $ext;
            if (File::exists($altPath)) {
                $sourcePath = $altPath;
                break;
            }
        }
    }

    if (File::exists($sourcePath)) {
        $targetDir = dirname($targetPath);
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        File::copy($sourcePath, $targetPath);
        $success++;
        // echo "Fixed: {$dbPath}\n";
    } else {
        $missing++;
        echo "Missing source for: {$dbPath} (Tried: {$sourcePath})\n";
    }
}

echo "\n--- Summary ---\n";
echo "Successfully repaired: {$success}\n";
echo "Already existed: {$already_exists}\n";
echo "Missing source files: {$missing}\n";
echo "----------------\n";
