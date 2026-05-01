<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Raiida\Page;
use Illuminate\Support\Facades\Storage;

class RevizySeederBackfillChecksumsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revizyseeder:backfill-checksums {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill MD5 checksums for pages that are missing them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $pages = $force ? Page::all() : Page::where(fn($q) => $q->whereNull('md5_checksum')->orWhereNull('image_size'))->get();
        if ($pages->isEmpty()) {
            $this->info("No pages found missing metadata.");
            return self::SUCCESS;
        }

        $this->info("Backfilling metadata for " . $pages->count() . " pages...");
        $bar = $this->output->createProgressBar($pages->count());
        $bar->start();

        foreach ($pages as $page) {
            // 1. Try automated presentation_data path (storage/app/)
            $path = storage_path('app/' . $page->image_path);
            
            // 2. Try manual upload path (storage/app/public/)
            if (!file_exists($path)) {
                $path = storage_path('app/public/' . $page->image_path);
            }

            if (file_exists($path)) {
                if ($force || !$page->md5_checksum) {
                    $page->md5_checksum = md5_file($path);
                }
                if ($force || !$page->image_size) {
                    $page->image_size = filesize($path);
                }
                $page->save();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Checksum backfill completed successfully.");
        return self::SUCCESS;
    }
}
