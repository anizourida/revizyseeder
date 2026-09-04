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

class RevizySeederDispatchPageNumberOCRBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public int $limit = 500,
        public int $delayIncrementSeconds = 0,
    ) {
        $this->onQueue(WorkflowState::workflowQueue());
    }

    public function handle(): void
    {
        if (WorkflowState::isPaused()) {
            $this->release(300);
            return;
        }

        $query = Page::query()
            ->where(function ($q) {
                $q->whereNull('page_number')
                    ->orWhere('page_number', '<', 1);
            })
            ->where(function ($q) {
                $q->whereNull('page_number_extraction_method')
                    ->orWhere('page_number_extraction_method', '')
                    ->orWhere('page_number_extraction_method', 'ocr_failed');
            })
            ->orderBy('id');

        $pages = $query->limit(max(1, $this->limit))->get(['id']);

        if ($pages->isEmpty()) {
            Log::info('RevizySeederDispatchPageNumberOCRBatchJob: no pages found needing OCR.');
            return;
        }

        foreach ($pages->values() as $index => $page) {
            $job = ExtractPageNumberJob::dispatch((int) $page->id);
            $seconds = $index * max(0, $this->delayIncrementSeconds);
            if ($seconds > 0) {
                $job->delay(now()->addSeconds($seconds));
            }
        }

        Log::info('RevizySeederDispatchPageNumberOCRBatchJob: dispatched ' . $pages->count() . ' OCR jobs.');
    }
}

