<?php

namespace App\Jobs;

use App\Models\Raiida\Page;
use App\Models\Raiida\BookPage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CategorizePageActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $modelId;
    protected $modelClass;

    /**
     * @param int $id
     * @param string $class
     */
    public function __construct(int $id, string $class = Page::class)
    {
        $this->modelId = $id;
        $this->modelClass = $class;
        $this->onQueue('revizyseeder-workflows');
    }

    public function handle(): void
    {
        $model = $this->modelClass::find($this->modelId);
        if (!$model) return;

        $category = $this->determineCategory($model);
        if ($category) {
            $model->update(['activity_category' => $category]);
            Log::info("Categorized {$this->modelClass} ID {$this->modelId} as {$category}");

            // If we just categorized a Page, also update all linked BookPages
            if ($this->modelClass === Page::class) {
                BookPage::where('page_id', $this->modelId)
                    ->whereNull('activity_category')
                    ->update(['activity_category' => $category]);
            }
        }
    }

    private function determineCategory($model)
    {
        $ocrPaths = [
            $model->ocr_full_text_path ?? null,
            $model->ocr_olmocr_path ?? null,
            $model->ocr_chandra_path ?? null,
        ];

        foreach ($ocrPaths as $path) {
            if (!$path) continue;

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                $content = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
            } else {
                $fullPath = storage_path('app/' . $path);
                if (!file_exists($fullPath)) continue;
                $content = file_get_contents($fullPath);
            }

            $text = strip_tags($content);
            $text = mb_strtolower($text);

            $category = $this->findCategoryInText($text);
            if ($category) {
                return $category;
            }
        }

        return null;
    }

    private function findCategoryInText($text)
    {
        $exactPhrases = [
            'Activités de vocabulaire' => ['activités de vocabulaire'],
            'Activités orales' => ['activités orales'],
            'Activités de lecture' => ['activités de lecture'],
            'Activités d’écriture' => ['activités d’écriture', 'activités d\'écriture'],
        ];

        foreach ($exactPhrases as $categoryName => $variants) {
            foreach ($variants as $variant) {
                if (str_contains($text, mb_strtolower($variant))) {
                    return $categoryName;
                }
            }
        }

        $keywords = [
            'Activités de vocabulaire' => ['vocabulaire'],
            'Activités orales' => ['orale'],
            'Activités de lecture' => ['lecture'],
            'Activités d’écriture' => ['écriture', 'ecriture'],
        ];

        foreach ($keywords as $categoryName => $words) {
            foreach ($words as $word) {
                if (preg_match('/\b' . preg_quote(mb_strtolower($word), '/') . '\b/u', $text)) {
                    return $categoryName;
                }
            }
        }

        return null;
    }
}
