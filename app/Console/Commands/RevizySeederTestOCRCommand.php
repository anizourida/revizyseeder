<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RevizySeederLMStudioOCRJob;
use App\Models\Raiida\Page;

class RevizySeederTestOCRCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revizyseeder:test-ocr {id=23}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger LM Studio OCR evaluation for a specific Page ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $page = Page::find($id);

        if (!$page) {
            $this->error("Page with ID {$id} not found.");
            return Command::FAILURE;
        }

        $this->info("Dispatching OCR Job for Page ID {$id} (" . basename($page->image_path) . ")...");
        
        RevizySeederLMStudioOCRJob::dispatch($id);
        
        $this->info("Job dispatched to queue 'revizyseeder-workflows'. Check your worker log for progress.");
        
        return Command::SUCCESS;
    }
}
