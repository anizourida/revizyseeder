<?php

namespace App\Jobs\Raiida;

use App\Services\Raiida\ConjugaisonExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExtractConjugaisonLessonsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;

    public int $tries = 1;

    public function __construct(
        public readonly bool $force = false,
        public readonly ?string $workflowContextId = null,
        public readonly ?int $initiatedByUserId = null,
        public readonly ?string $initiatedByEmail = null,
        public readonly ?string $initiatedByRole = null
    ) {
        $this->queue = (string) config('raiida.conjugaison_extraction.queue', config('raiida.workflow_queue', 'revizyseeder-workflows'));
    }

    public function handle(ConjugaisonExtractionService $service): void
    {
        $audit = [
            'force' => $this->force,
            'workflow_context_id' => $this->workflowContextId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_email' => $this->initiatedByEmail,
            'initiated_by_role' => $this->initiatedByRole,
            'attempt' => $this->attempts(),
        ];

        try {
            $summary = $service->run($this->force);

            Log::info('raiida.admin_mutation.job.conjugaison_extract.completed', $audit + $summary);
        } catch (Throwable $exception) {
            Log::warning('raiida.admin_mutation.job.conjugaison_extract.failed', $audit + [
                'exception' => $exception::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
