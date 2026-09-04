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

class RevizySeederDispatchLMStudioPageNumberBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public ?string $model = null,
        public int $limit = 200,
        public int $delayIncrementSeconds = 0,
        public bool $includeSuspiciousLowNumbers = true,
    ) {
        $this->onQueue(WorkflowState::workflowQueue());
    }

    public function handle(): void
    {
        if (WorkflowState::isPaused()) {
            $this->release(300);
            return;
        }

        $model = $this->model ?: (string) env('LM_STUDIO_MODEL', 'allenai/olmocr-2-7b');

        $suspiciousMin = (int) config('revizyseeder.page_number.suspicious_min', 1);
        $suspiciousMax = (int) config('revizyseeder.page_number.suspicious_max', 9);

        $query = Page::query()
            ->where(function ($q) use ($suspiciousMin, $suspiciousMax) {
                $q->whereNull('page_number')
                    ->orWhere('page_number', '<', 1);

                if ($this->includeSuspiciousLowNumbers) {
                    $q->orWhere(function ($low) use ($suspiciousMin, $suspiciousMax) {
                        $low->whereNotNull('page_number')
                            ->whereBetween('page_number', [$suspiciousMin, $suspiciousMax]);
                    });
                }
            })
            ->where(function ($q) {
                $q->whereNull('page_number_extraction_method')
                    ->orWhere('page_number_extraction_method', '')
                    ->orWhere('page_number_extraction_method', 'ocr_failed')
                    ->orWhere('page_number_extraction_method', 'ocr');
            })
            ->orderByDesc('id');

        $pages = $query->limit(max(1, $this->limit))->get(['id']);

        if ($pages->isEmpty()) {
            Log::info('RevizySeederDispatchLMStudioPageNumberBatchJob: no pages found needing page number.');
            return;
        }

        foreach ($pages->values() as $index => $page) {
            $job = RevizySeederLMStudioOCRJob::dispatch((int) $page->id, $model, 'page_only');
            $seconds = $index * max(0, $this->delayIncrementSeconds);
            if ($seconds > 0) {
                $job->delay(now()->addSeconds($seconds));
            }
        }

        Log::info('RevizySeederDispatchLMStudioPageNumberBatchJob: dispatched ' . $pages->count() . ' LM Studio jobs.');
    }
}
