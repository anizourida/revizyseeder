<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use App\Support\RevizySeeder\WorkflowState;

class RevizySeederExtractPagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200; // 20 minutes

    /**
     * Create a new job instance.
     *
     * @param  array{grade?:string,period?:string,week?:string}  $options
     */
    public function __construct(
        public readonly array $options = []
    )
    {
        $this->onQueue('revizyseeder-workflows');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (WorkflowState::isPaused()) {
            $this->release(300);
            return;
        }

        $directories = \Illuminate\Support\Facades\Storage::disk('local')->directories('presentation_data');

        $grade = strtoupper(trim((string) ($this->options['grade'] ?? '')));
        $period = strtoupper(trim((string) ($this->options['period'] ?? '')));
        $week = strtoupper(trim((string) ($this->options['week'] ?? '')));

        if ($grade === '') {
            $grade = null;
        }
        if ($period === '') {
            $period = null;
        }
        if ($week === '') {
            $week = null;
        }

        foreach ($directories as $directory) {
            $dir = (string) $directory;
            if (str_contains($dir, '&')) {
                continue;
            }

            if ($grade !== null && ! str_contains($dir, '_' . $grade . '_')) {
                continue;
            }
            if ($period !== null && ! str_contains($dir, '_' . $period . '_')) {
                continue;
            }
            if ($week !== null && ! str_contains($dir, '_' . $week . '_')) {
                continue;
            }

            \App\Jobs\RevizySeederExtractPagesFromFolderJob::dispatch($directory);
        }
    }
}
