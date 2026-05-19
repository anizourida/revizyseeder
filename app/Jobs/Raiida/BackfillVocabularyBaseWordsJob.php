<?php

namespace App\Jobs\Raiida;

use App\Models\Raiida\VocabularyItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BackfillVocabularyBaseWordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 7200;

    public int $tries = 1;

    /**
     * @param  array{limit?:int,grade?:string,period?:string,week?:string,dry_run?:bool}  $options
     */
    public function __construct(
        public readonly array $options = [],
        public readonly ?string $workflowContextId = null,
        public readonly ?int $initiatedByUserId = null,
        public readonly ?string $initiatedByEmail = null,
        public readonly ?string $initiatedByRole = null
    ) {
        $this->queue = (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public function handle(): void
    {
        $audit = [
            'workflow_context_id' => $this->workflowContextId,
            'initiated_by_user_id' => $this->initiatedByUserId,
            'initiated_by_email' => $this->initiatedByEmail,
            'initiated_by_role' => $this->initiatedByRole,
            'options' => $this->options,
        ];

        Log::info('raiida.admin_mutation.job.backfill_vocab_base_words.started', $audit);

        $limit = max(1, min((int) ($this->options['limit'] ?? 5000), 50000));
        $dryRun = (bool) ($this->options['dry_run'] ?? false);

        $grade = strtoupper(trim((string) ($this->options['grade'] ?? '')));
        $grade = $grade !== '' ? $grade : null;

        $period = strtoupper(trim((string) ($this->options['period'] ?? '')));
        $period = $period !== '' ? $period : null;

        $week = strtoupper(trim((string) ($this->options['week'] ?? '')));
        $week = $week !== '' ? $week : null;

        $query = VocabularyItem::query()
            ->where(function (Builder $q): void {
                $q->whereNull('base_word')->orWhere('base_word', '');
            })
            ->orderBy('id');

        if ($grade !== null) {
            $query->where('grade', $grade);
        }
        if ($period !== null) {
            $query->where('period', $period);
        }
        if ($week !== null) {
            $query->where('week', $week);
        }

        $updated = 0;
        $skipped = 0;

        $prefixes = [
            "L'", "l'", 'Le ', 'le ', 'La ', 'la ', 'Les ', 'les ',
            'Un ', 'un ', 'Une ', 'une ', 'Des ', 'des ',
            'Ou ', 'ou ',
        ];

        $query->chunkById(500, function ($items) use (
            $limit,
            $dryRun,
            &$updated,
            &$skipped,
            $prefixes
        ): bool {
            foreach ($items as $item) {
                if (($updated + $skipped) >= $limit) {
                    return false;
                }

                $word = str_replace("\u{2019}", "'", trim((string) ($item->word ?? '')));
                if ($word === '') {
                    $skipped++;
                    continue;
                }

                $baseWord = $word;
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($baseWord, $prefix)) {
                        $baseWord = mb_substr($baseWord, mb_strlen($prefix, 'UTF-8'), null, 'UTF-8');
                        break;
                    }
                }
                $baseWord = trim((string) $baseWord);

                if ($baseWord === '' || mb_strtolower($baseWord, 'UTF-8') === mb_strtolower($word, 'UTF-8')) {
                    $skipped++;
                    continue;
                }

                if (! $dryRun) {
                    $item->base_word = $baseWord;
                    $item->save();
                }

                $updated++;
            }

            return true;
        });

        Log::info('raiida.admin_mutation.job.backfill_vocab_base_words.completed', $audit + [
            'updated' => $updated,
            'skipped' => $skipped,
            'dry_run' => $dryRun,
        ]);
    }
}

