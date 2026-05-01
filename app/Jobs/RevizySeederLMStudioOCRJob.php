<?php

namespace App\Jobs;

use App\Models\Raiida\Page;
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

        try {
            $htmlRelativePath = null;
            $pageNumber = null;

            if ($this->mode === 'text_only' || $this->mode === 'full') {
                Log::info("LM Studio: Extracting TEXT for ID {$this->pageId} using: {$model}");
                
                $resText = Http::timeout(400)->post($apiUrl, [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => [
                        ['type' => 'text', 'text' => self::EXTRACTION_PROMPT],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]]
                    ]]],
                    'temperature' => 0.1,
                    'max_tokens' => 4000,
                ]);

                if ($resText->failed()) throw new \Exception("Text Extraction Failed: " . $resText->body());

                $htmlContent = $this->postProcessOCR($resText->json('choices.0.message.content'));
                // Save to model-specific path
                $suffix = str_contains(strtolower($model), 'chandra') ? '_chandra.html' : '_olmocr.html';
                $htmlRelativePath = preg_replace('/\.[^.]+$/', '', $page->image_path) . $suffix;
                Storage::disk('local')->put($htmlRelativePath, $htmlContent);
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
            }

            Log::info("LM Studio Task Completed for ID {$this->pageId}");

        } catch (\Exception $e) {
            Log::error("LM Studio Failed: " . $e->getMessage());
            foreach ($duplicates as $dup) $dup->update(['page_number_extraction_error' => $e->getMessage()]);
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
