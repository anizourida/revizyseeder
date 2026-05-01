<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$queueDefault = (string) config('queue.default');
$queueDriver = (string) config("queue.connections.{$queueDefault}.driver");
$queueName = 'revizyseeder-workflows';

$paused = (bool) cache()->get('revizyseeder:workflows_paused', false);

echo "queue.default={$queueDefault}\n";
echo "queue.driver={$queueDriver}\n";
echo "paused=" . ($paused ? '1' : '0') . "\n";

try {
    $now = time();

    $jobs = \DB::table('jobs')->where('queue', $queueName);
    $total = (int) (clone $jobs)->count();
    $reserved = (int) (clone $jobs)->whereNotNull('reserved_at')->count();
    $delayed = (int) (clone $jobs)->whereNull('reserved_at')->where('available_at', '>', $now)->count();
    $pending = (int) (clone $jobs)->whereNull('reserved_at')->where('available_at', '<=', $now)->count();

    echo "jobs_in_queue={$total}\n";
    echo "jobs_pending={$pending}\n";
    echo "jobs_reserved={$reserved}\n";
    echo "jobs_delayed={$delayed}\n";
} catch (\Throwable $e) {
    echo "jobs_in_queue=ERROR: {$e->getMessage()}\n";
}

try {
    $failed = (int) \DB::table('failed_jobs')->where('queue', $queueName)->count();
    echo "failed_jobs_in_queue={$failed}\n";
} catch (\Throwable $e) {
    echo "failed_jobs_in_queue=ERROR: {$e->getMessage()}\n";
}
