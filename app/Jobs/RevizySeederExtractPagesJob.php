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
     */
    public function __construct()
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
        
        foreach ($directories as $directory) {
            \App\Jobs\RevizySeederExtractPagesFromFolderJob::dispatch($directory);
        }
    }
}
