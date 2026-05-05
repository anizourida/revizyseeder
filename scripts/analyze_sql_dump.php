<?php

$sqlFile = __DIR__ . '/../database/revizy_production_db_exported_1-may-2026_2-14.sql';
$handle = fopen($sqlFile, "r");

$tables = ['files', 'concepts', 'flashcards', 'flashcard_categories'];
$found = [];

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        foreach ($tables as $table) {
            if (strpos($line, "INSERT INTO `$table`") === 0) {
                // Just print the first 500 chars of the insert to understand columns and format
                echo "Found INSERT for $table:\n";
                echo substr($line, 0, 500) . "...\n\n";
                $found[$table] = true;
            }
        }
        if (count($found) === count($tables)) {
            break;
        }
    }
    fclose($handle);
}
