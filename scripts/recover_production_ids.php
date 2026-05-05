<?php

use App\Models\Raiida\VocabularyItem;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sqlFile = __DIR__ . '/../database/revizy_production_db_exported_1-may-2026_2-14.sql';

if (!file_exists($sqlFile)) {
    die("SQL dump not found.\n");
}

echo "Parsing SQL dump...\n";

$filesMap = []; // id => secret_id
$conceptsMap = []; // grade_period_word => id
$flashcardsMap = []; // word => array of flashcard info

$handle = fopen($sqlFile, "r");
$currentTable = null;

while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'INSERT INTO `files`') === 0) {
        $currentTable = 'files';
        continue;
    } elseif (strpos($line, 'INSERT INTO `concepts`') === 0) {
        $currentTable = 'concepts';
        continue;
    } elseif (strpos($line, 'INSERT INTO `flashcards`') === 0) {
        $currentTable = 'flashcards';
        continue;
    } elseif (strpos($line, 'INSERT INTO `') === 0) {
        $currentTable = null;
        continue;
    }

    if (!$currentTable) continue;

    $line = trim($line);
    if (empty($line) || strpos($line, '--') === 0) continue;

    if ($line[0] === '(') {
        $dataStr = substr($line, 1, strrpos($line, ')') - 1);
        $values = str_getcsv($dataStr, ',', "'", "\\");

        if ($currentTable === 'files') {
            $id = trim($values[0]);
            $secretId = trim($values[2]);
            if ($secretId !== 'NULL') {
                $filesMap[$id] = $secretId;
            }
        } elseif ($currentTable === 'concepts') {
            $id = trim($values[0]);
            $name = stripslashes(trim($values[3]));
            $code = trim($values[4]);
            
            // Parse FR_N2_P1_SEM3_VOC_C002
            $parts = explode('_', $code);
            if (count($parts) >= 3) {
                $grade = strtoupper(trim($parts[1]));
                $period = strtoupper(trim($parts[2]));
                $word = mb_strtolower(trim($name), 'UTF-8');
                $word = str_replace(['’', '`', '´'], "'", $word);
                $key = "{$grade}_{$period}_{$word}";
                $conceptsMap[$key] = $id;
            }
        } elseif ($currentTable === 'flashcards') {
            $id = trim($values[0]);
            $frontText = stripslashes(trim($values[2]));
            // Strip any [BLUE] or [PINK] tags or any other [TAG]
            $frontText = preg_replace('/\[.*?\]/', '', $frontText);
            
            $frontMediaId = trim($values[4]);
            $frontAudioId = trim($values[5]);
            
            $wordKey = mb_strtolower(trim($frontText), 'UTF-8');
            $wordKey = str_replace(['’', '`', '´'], "'", $wordKey);
            if (!isset($flashcardsMap[$wordKey])) {
                $flashcardsMap[$wordKey] = [];
            }
            $flashcardsMap[$wordKey][] = [
                'id' => $id,
                'front_media_id' => $frontMediaId !== 'NULL' ? $frontMediaId : null,
                'front_audio_id' => $frontAudioId !== 'NULL' ? $frontAudioId : null,
            ];
        }
    }
}
fclose($handle);

echo "Parsed " . count($filesMap) . " files, " . count($conceptsMap) . " concepts, " . count($flashcardsMap) . " unique flashcard words.\n";

echo "Updating database...\n";

$items = VocabularyItem::all();
$successCount = 0;
$missingCount = 0;

foreach ($items as $item) {
    $word = mb_strtolower(trim($item->word), 'UTF-8');
    $word = str_replace(['’', '`', '´'], "'", $word);
    $grade = strtoupper(trim($item->grade));
    $period = strtoupper(trim($item->period));
    
    $conceptKey = "{$grade}_{$period}_{$word}";
    
    $updated = false;
    
    if (isset($conceptsMap[$conceptKey])) {
        $item->concept_id = $conceptsMap[$conceptKey];
        $updated = true;
    }
    
    if (isset($flashcardsMap[$word])) {
        // Find the best flashcard. If multiple, just pick the first one with assets
        $bestFc = $flashcardsMap[$word][0];
        foreach ($flashcardsMap[$word] as $fc) {
            if ($fc['front_media_id'] || $fc['front_audio_id']) {
                $bestFc = $fc;
                break;
            }
        }
        
        $item->flashcard_id = $bestFc['id'];
        
        if ($bestFc['front_media_id'] && isset($filesMap[$bestFc['front_media_id']])) {
            $item->revizy_image_file_id = $filesMap[$bestFc['front_media_id']];
        }
        if ($bestFc['front_audio_id'] && isset($filesMap[$bestFc['front_audio_id']])) {
            $item->revizy_audio_file_id = $filesMap[$bestFc['front_audio_id']];
        }
        $updated = true;
    }
    
    if ($updated) {
        $item->save();
        $successCount++;
    } else {
        $missingCount++;
        if ($missingCount <= 5) {
            echo "Missed: '{$item->word}' (Grade: {$item->grade}, Period: {$item->period}). ConceptKey: '{$conceptKey}'. Has Concept: " . (isset($conceptsMap[$conceptKey]) ? 'Yes' : 'No') . ", Has Flashcard: " . (isset($flashcardsMap[$word]) ? 'Yes' : 'No') . "\n";
        }
    }
}

echo "\n--- Summary ---\n";
echo "Successfully recovered IDs for {$successCount} items.\n";
echo "Could not find matches for {$missingCount} items.\n";
echo "----------------\n";
