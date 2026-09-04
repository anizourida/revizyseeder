<?php

namespace App\Jobs;

use App\Models\Raiida\Page;
use App\Models\Raiida\BookPage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Support\RevizySeeder\WorkflowState;

class RevizySeederLMStudioOCRJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pageId;
    protected $modelOverride;
    protected $mode; // 'full', 'text_only', 'page_only'
    
    const EXTRACTION_PROMPT = "Extract all the text from the image and structure it as HTML";
    const PAGE_NUMBER_PROMPT = "return the page number without any explanation just number (Int)";

    public $timeout = 600;

    /**
     * @param string $mode 'full', 'text_only', 'page_only'
     */
    public function __construct(int $pageId, ?string $model = null, string $mode = 'full')
    {
        $this->pageId = $pageId;
        $this->modelOverride = $model;
        $this->mode = $mode;
        $this->onQueue('revizyseeder-workflows');
    }

    public function handle(): void
    {
        $startTime = microtime(true);

        if (WorkflowState::isPaused()) {
            Log::info("LM Studio OCR deferred for page ID {$this->pageId} because workflows are paused.");
            $this->release(300);
            return;
        }

        ini_set('memory_limit', '512M');
        $page = Page::find($this->pageId);
        if (!$page) {
            Log::warning("LM Studio OCR aborted: page ID {$this->pageId} not found.");
            return;
        }

        $currentMethod = (string) $page->page_number_extraction_method;
        $shouldSkipPageNumber = \Illuminate\Support\Str::contains($currentMethod, ['admin', 'llm-allenai/olmocr']);

        $duplicates = $page->md5_checksum
            ? Page::where('md5_checksum', $page->md5_checksum)->get()
            : Page::whereKey($page->id)->get();

        if ($duplicates->isEmpty()) {
            $duplicates = collect([$page]);
        }

        // Many manual uploads are stored under storage/app/public, while extracted slides are in storage/app.
        $localPath = Storage::disk('local')->path($page->image_path);
        $publicPath = storage_path('app/public/' . ltrim((string) $page->image_path, '/'));
        $imagePath = file_exists($localPath) ? $localPath : $publicPath;

        if (!file_exists($imagePath) && $page->md5_checksum) {
            $standardLocalPath = Storage::disk('local')->path("pages/{$page->md5_checksum}.png");
            $standardPublicPath = Storage::disk('public')->path("pages/{$page->md5_checksum}.png");

            if (file_exists($standardLocalPath)) {
                $imagePath = $standardLocalPath;
            } elseif (file_exists($standardPublicPath)) {
                $imagePath = $standardPublicPath;
            }
        }

        if (!file_exists($imagePath)) {
            $message = 'Image file not found for OCR at local or public storage path.';
            Log::warning("LM Studio OCR aborted for page ID {$this->pageId}: {$message}", [
                'image_path' => $page->image_path,
                'local_path' => $localPath,
                'public_path' => $publicPath,
            ]);
            $page->update(['page_number_extraction_error' => $message]);
            return;
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $dataUrl = "data:" . (mime_content_type($imagePath) ?: 'image/png') . ";base64,{$imageData}";

        $apiUrl = env('LM_STUDIO_API_URL', 'http://localhost:1234/v1/chat/completions');
        $model = $this->modelOverride ?: env('LM_STUDIO_MODEL', 'allenai/olmocr-2-7b');

        $log = \App\Models\Raiida\ExtractionLog::create([
            'model_type' => get_class($page),
            'model_id' => $page->id,
            'type' => 'ocr',
            'status' => 'pending',
            'payload' => [
                'model' => $model,
                'mode' => $this->mode,
                'image_path' => $page->image_path
            ]
        ]);

        try {
            $htmlRelativePath = null;
            $pageNumber = null;

            if ($this->mode === 'text_only' || $this->mode === 'full') {
                $msg = "LM Studio: Extracting TEXT for Page ID {$this->pageId} using: {$model}";
                Log::info($msg);
                echo "[Page {$this->pageId}] {$msg}\n"; // Print to console for the user to see
                
                $resText = Http::timeout(400)->post($apiUrl, [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => self::EXTRACTION_PROMPT],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]]
                    ]]],
                    'temperature' => 0.1,
                    'max_tokens' => 1500, // Reduced from 4000 to prevent runaway generation
                ]);

                if ($resText->failed()) throw new \Exception("Text Extraction Failed: " . $resText->body());

                $htmlContent = $this->postProcessOCR($resText->json('choices.0.message.content'));
                // Use PathResolver for standardized pathing
                $resolver = app(\App\Services\Raiida\PathResolver::class);
                $suffix = str_contains(strtolower($model), 'chandra') ? 'chandra' : 'olmocr';
                $htmlRelativePath = $resolver->getOcrPath($this->pageId, $suffix, $page->md5_checksum);
                Storage::disk('public')->put($htmlRelativePath, $htmlContent);
            }

            if ($this->mode === 'page_only' || $this->mode === 'full') {
                if ($shouldSkipPageNumber) {
                    Log::info("Skipping PAGE OCR for page ID {$this->pageId} - already extracted via {$currentMethod}");
                } else {
                Log::info("LM Studio: Extracting PAGE for ID {$this->pageId} using: {$model}");
                
                $resPage = Http::timeout(300)->post($apiUrl, [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => self::PAGE_NUMBER_PROMPT],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]]
                    ]]],
                    'temperature' => 0.1,
                    'max_tokens' => 100,
                ]);

                if ($resPage->failed()) throw new \Exception("Page Number Extraction Failed: " . $resPage->body());

                $pageNumber = $this->parsePageNumber($resPage->json('choices.0.message.content'));
                }
            }

            // Sync Updates
            foreach ($duplicates as $dup) {
                $updateData = ['page_number_extraction_error' => null];
                
                if ($pageNumber) {
                    $updateData['page_number'] = $pageNumber;
                    $updateData['page_number_extraction_method'] = 'llm-' . $model . '-num';
                }

                if ($htmlRelativePath) {
                    $col = str_contains(strtolower($model), 'chandra') ? 'ocr_chandra_path' : 'ocr_olmocr_path';
                    $updateData[$col] = $htmlRelativePath;
                    $updateData['page_number_extraction_method'] = 'llm-' . $model . '-text';
                }

                $dup->update($updateData);

                if ($htmlRelativePath) {
                    BookPage::where('page_id', $dup->id)->update([
                        $col => $htmlRelativePath,
                    ]);
                }
            }

            Log::info("LM Studio Task Completed for ID {$this->pageId}");
            echo "[Page {$this->pageId}] SUCCESS: Text extraction completed.\n";

            // Trigger categorization after successful OCR
            if ($htmlRelativePath) {
                \App\Jobs\CategorizePageActivityJob::dispatch($this->pageId);
            }

            $log->update([
                'status' => 'success',
                'duration' => microtime(true) - $startTime,
                'result' => ['path' => $htmlRelativePath ?? null, 'page_number' => $pageNumber ?? null]
            ]);

        } catch (\Exception $e) {
            Log::error("LM Studio Failed: " . $e->getMessage());
            echo "[Page {$this->pageId}] FAILED: " . $e->getMessage() . "\n";
            foreach ($duplicates as $dup) $dup->update(['page_number_extraction_error' => $e->getMessage()]);

            if (isset($log)) {
                $log->update([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'duration' => microtime(true) - $startTime
                ]);
            }
        }
    }

    protected function postProcessOCR(?string $text): string
    {
        if (!$text) return "";
        $text = preg_replace('/\.{21,}/', '....................', $text);
        return trim($text);
    }

    protected function parsePageNumber(?string $text): ?int
    {
        if (!$text) return null;
        $text = trim($text);
        if (preg_match('/^```(?:\w+)?\n?(.*?)\n?```$/s', $text, $matches)) $text = trim($matches[1]);
        if (strtolower($text) === 'null') return null;
        if (preg_match('/\b(\d+)\b/', $text, $matches)) return (int) $matches[1];
        return null;
    }
}
