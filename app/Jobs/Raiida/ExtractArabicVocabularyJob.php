<?php

namespace App\Jobs\Raiida;

use App\Services\Raiida\ArabicVocabularyExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExtractArabicVocabularyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;

    /**
     * @param  array{grade?:?string,period?:?string,week?:?string,lesson_id?:?string,limit?:int,force?:bool}  $options
     */
    public function __construct(
        public array $options = [],
        public ?int $userId = null,
        public ?string $userEmail = null,
        public ?string $userRole = null
    ) {
        $this->onQueue(config('raiida.vocabulary.queue', 'revizyseeder-workflows'));
    }

    public function handle(ArabicVocabularyExtractionService $service): void
    {
        Log::info('Arabic vocabulary extraction background job started', [
            'options' => $this->options,
            'user' => $this->userEmail,
        ]);

        $summary = $service->runBatchExtraction($this->options);

        Log::info('Arabic vocabulary extraction background job finished', [
            'summary' => $summary,
            'user' => $this->userEmail,
        ]);
    }
}
