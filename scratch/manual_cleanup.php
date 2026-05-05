<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = app(\App\Services\Raiida\External\RevizySystemClient::class);

$oldIds = [
    1189, 1190, 1191, 1192, 1193, 1194, 1195, 1196, 
    1197, 1198, 1199, 1200, 1201, 1202, 1203, 1204
];

echo "🗑️ Starting manual cleanup of old combined concepts...\n";

foreach ($oldIds as $id) {
    try {
        $client->delete("/concepts/{$id}");
        echo "✅ Deleted concept ID {$id}\n";
    } catch (\Throwable $e) {
        echo "❌ Failed to delete ID {$id}: " . $e->getMessage() . "\n";
    }
}

echo "✅ Done.\n";
