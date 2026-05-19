<?php

namespace App\Console\Commands;

use App\Jobs\Raiida\BackfillVocabularyBaseWordsJob;
use App\Models\Raiida\VocabularyItem;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RaiidaBackfillVocabularyBaseWordsCommand extends Command
{
    protected $signature = 'raiida:vocab:backfill-base-word
        {--period= : Period code (e.g. P4)}
        {--week= : Week code (e.g. SEM2)}
        {--grade= : Grade code (e.g. N1)}
        {--limit=5000 : Max items to process}
        {--dry-run : Do not write to database}
        {--debug : Print a few skipped examples}
        {--debug-limit=10 : Max debug lines}
        {--sync : Run immediately instead of queueing}';

    protected $description = 'Backfill vocabulary base_word field for items that start with an article/prefix (Le/La/Les/L\').';

    public function handle(): int
    {
        $options = [
            'period' => $this->option('period'),
            'week' => $this->option('week'),
            'grade' => $this->option('grade'),
            'limit' => (int) $this->option('limit'),
            'dry_run' => (bool) $this->option('dry-run'),
        ];

        if (! (bool) $this->option('sync')) {
            BackfillVocabularyBaseWordsJob::dispatch($options);

            $this->info('Queued vocabulary base_word backfill job.');
            $this->line('Queue: ' . (string) config('raiida.workflow_queue', 'revizyseeder-workflows'));

            return self::SUCCESS;
        }

        $limit = max(1, min((int) ($options['limit'] ?? 5000), 50000));
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $debug = (bool) $this->option('debug');
        $debugLimit = max(0, min((int) $this->option('debug-limit'), 200));

        $grade = strtoupper(trim((string) ($options['grade'] ?? '')));
        $grade = $grade !== '' ? $grade : null;

        $period = strtoupper(trim((string) ($options['period'] ?? '')));
        $period = $period !== '' ? $period : null;

        $week = strtoupper(trim((string) ($options['week'] ?? '')));
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
        $skipReasons = [
            'empty_word' => 0,
            'no_prefix' => 0,
            'base_equals_word' => 0,
            'base_empty' => 0,
        ];
        $debugLines = 0;

        $prefixes = [
            "L'", "l'", 'Le ', 'le ', 'La ', 'la ', 'Les ', 'les ',
            'Un ', 'un ', 'Une ', 'une ', 'Des ', 'des ',
            'Ou ', 'ou ',
        ];

        $query->chunkById(500, function ($items) use (
            $limit,
            $dryRun,
            $debug,
            $debugLimit,
            &$updated,
            &$skipped,
            &$skipReasons,
            &$debugLines,
            $prefixes
        ): bool {
            foreach ($items as $item) {
                if (($updated + $skipped) >= $limit) {
                    return false;
                }

                $word = str_replace("\u{2019}", "'", trim((string) ($item->word ?? '')));
                if ($word === '') {
                    $skipped++;
                    $skipReasons['empty_word']++;
                    continue;
                }

                $baseWord = $word;
                $hadPrefix = false;
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($baseWord, $prefix)) {
                        $baseWord = mb_substr($baseWord, mb_strlen($prefix, 'UTF-8'), null, 'UTF-8');
                        $hadPrefix = true;
                        break;
                    }
                }
                $baseWord = trim((string) $baseWord);

                if ($baseWord === '') {
                    $skipped++;
                    $skipReasons['base_empty']++;
                    continue;
                }

                if (! $hadPrefix) {
                    $skipped++;
                    $skipReasons['no_prefix']++;
                    continue;
                }

                if (mb_strtolower($baseWord, 'UTF-8') === mb_strtolower($word, 'UTF-8')) {
                    $skipped++;
                    $skipReasons['base_equals_word']++;
                    continue;
                }

                if (! $dryRun) {
                    $item->base_word = $baseWord;
                    $item->save();
                }

                $updated++;

                if ($debug && $debugLines < $debugLimit) {
                    $debugLines++;
                    /** @var mixed $id */
                    $id = $item->id;
                    $this->line("updated id={$id} word=\"{$word}\" base_word=\"{$baseWord}\"");
                }
            }

            return true;
        });

        $this->info(
            "Done. updated={$updated} skipped={$skipped} dry_run=" . ($dryRun ? '1' : '0')
                . ' skip_reasons=' . json_encode($skipReasons, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return self::SUCCESS;
    }
}
