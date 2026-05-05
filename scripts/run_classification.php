<?php

use App\Services\Raiida\VocabularyMetadataClassificationService;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(VocabularyMetadataClassificationService::class);

$options = [
    'period' => 'P4',
    'limit' => 500,
    'dry_run' => false,
    'force' => false,
];

try {
    $result = $service->classify($options);
    print_r($result);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
