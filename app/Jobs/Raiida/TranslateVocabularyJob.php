<?php

namespace App\Jobs\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\DeepLTranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateVocabularyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 7200;

    public function __construct(
        public readonly array $vocabularyIds,
        public readonly ?int $initiatedByUserId = null
    ) {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(DeepLTranslationService $translationService): void
    {
        Log::info('TranslateVocabularyJob started', ['total_ids' => count($this->vocabularyIds)]);

        $items = VocabularyItem::whereIn('id', $this->vocabularyIds)
            ->where(function ($query) {
                $query->whereNull('ar_translation')->orWhere('ar_translation', '');
            })
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        // DeepL API rate limits & batching (chunk of 50 to be safe)
        $chunks = $items->chunk(50);
        $translatedCount = 0;

        foreach ($chunks as $chunk) {
            $words = $chunk->pluck('word')->toArray();
            $translations = $translationService->translateBatch($words);

            if (count($translations) === count($words)) {
                $index = 0;
                foreach ($chunk as $item) {
                    $item->ar_translation = $translations[$index];
                    $item->save();
                    $index++;
                    $translatedCount++;
                }
            } else {
                Log::warning('TranslateVocabularyJob chunk mismatch or translation failed.', [
                    'requested_count' => count($words),
                    'returned_count' => count($translations)
                ]);
            }
            
            // Sleep briefly to avoid hitting rate limits too aggressively
            usleep(500000); // 0.5s
        }

        Log::info('TranslateVocabularyJob completed', ['translated_count' => $translatedCount]);
    }
}
