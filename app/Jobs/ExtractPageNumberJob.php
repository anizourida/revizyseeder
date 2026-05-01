<?php

namespace App\Jobs;

use App\Models\Raiida\Page;
use App\Support\RevizySeeder\WorkflowState;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractPageNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    protected int $pageId;

    public function __construct(int $pageId)
    {
        $this->pageId = $pageId;
        $this->onQueue('revizyseeder-workflows');
    }

    public function handle(): void
    {
        if (WorkflowState::isPaused()) {
            // Keep the job around, but back off while paused.
            $this->release(300);
            return;
        }

        $page = Page::find($this->pageId);

        if (!$page) {
            Log::warning("ExtractPageNumberJob: Page ID {$this->pageId} not found, skipping.");
            return;
        }

        // Skip if already extracted by admin or LLM
        if ($page->page_number_extraction_method && in_array($page->page_number_extraction_method, ['admin_manually', 'admin'])) {
            Log::info("ExtractPageNumberJob: Skipping page ID {$this->pageId} — already set by admin.");
            return;
        }

        $fullImagePath = Storage::disk('local')->path($page->image_path);

        if (!file_exists($fullImagePath)) {
            Log::warning("ExtractPageNumberJob: Image not found for page ID {$this->pageId}: {$fullImagePath}");
            $page->update(['page_number_extraction_error' => 'Image file not found']);
            return;
        }

        $pythonBin = config('raiida.presentation_data.python_bin', env('RAIIDA_PRESENTATION_PYTHON_BIN', 'python'));
        $scriptPath = base_path('scripts/extract_page_number.py');

        $command = escapeshellcmd($pythonBin . ' ' . $scriptPath . ' ' . escapeshellarg($fullImagePath));

        $output = null;
        $returnCode = null;
        exec($command . ' 2>&1', $outputLines, $returnCode);
        $output = trim(implode("\n", $outputLines));

        if ($returnCode !== 0) {
            Log::error("ExtractPageNumberJob: Python script failed for page ID {$this->pageId} (exit code: {$returnCode}): {$output}");
            $page->update(['page_number_extraction_error' => "Script exit code {$returnCode}: {$output}"]);
            return;
        }

        if (!empty($output) && is_numeric($output)) {
            $page->update([
                'page_number' => (int) $output,
                'page_number_extraction_method' => 'ocr',
                'page_number_extraction_error' => null,
            ]);
            Log::info("ExtractPageNumberJob: Page ID {$this->pageId} → page number {$output}");
        } else {
            $page->update([
                'page_number_extraction_error' => 'OCR returned no valid number: ' . ($output ?: '(empty)'),
                'page_number_extraction_method' => 'ocr_failed',
            ]);
            Log::info("ExtractPageNumberJob: Page ID {$this->pageId} — no valid number returned.");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ExtractPageNumberJob FAILED for page ID {$this->pageId}: " . $exception->getMessage());

        $page = Page::find($this->pageId);
        if ($page) {
            $page->update([
                'page_number_extraction_error' => 'Job failed: ' . $exception->getMessage(),
            ]);
        }
    }
}
