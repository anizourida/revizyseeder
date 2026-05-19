<?php

namespace App\Console\Commands;

use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RevizyPublishException;
use App\Services\Raiida\RevizyQuestionApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class RaiidaFixLongArticleGrammarTrapQuestionsCommand extends Command
{
    protected $signature = 'raiida:fix-long-article-grammar-traps
        {--grade= : Grade code (e.g. N1)}
        {--period= : Period code (e.g. P4)}
        {--week= : Week code (e.g. SEM2)}
        {--limit=5000 : Max questions to inspect}
        {--dry-run : Do not call Revizy update}';

    protected $description = 'Draft published article/gender trap questions (Le/La/Un/Une) when the underlying vocabulary phrase has more than 3 words.';

    public function handle(RevizyQuestionApiClient $client): int
    {
        $grade = strtoupper(trim((string) $this->option('grade')));
        $period = strtoupper(trim((string) $this->option('period')));
        $week = strtoupper(trim((string) $this->option('week')));
        $limit = max(1, min((int) $this->option('limit'), 50000));
        $dryRun = (bool) $this->option('dry-run');

        if ($grade === '') {
            $grade = null;
        }
        if ($period === '') {
            $period = null;
        }
        if ($week === '') {
            $week = null;
        }

        $vocabQuery = VocabularyItem::query()
            ->whereNotNull('concept_id')
            ->where('concept_id', '!=', '')
            ->orderBy('id');

        if ($grade !== null) {
            $vocabQuery->where('grade', $grade);
        }
        if ($period !== null) {
            $vocabQuery->where('period', $period);
        }
        if ($week !== null) {
            $vocabQuery->where('week', $week);
        }

        /** @var Collection<string, VocabularyItem> $vocabByConcept */
        $vocabByConcept = $vocabQuery->get()->keyBy(fn (VocabularyItem $item): string => (string) $item->concept_id);

        if ($vocabByConcept->isEmpty()) {
            $this->info('No vocabulary items found in the selected scope.');
            return self::SUCCESS;
        }

        // Candidate grammar traps created with a name like:
        // "Un X / Une X", "Une X / Un X", "Le X / La X", "La X / Le X".
        $attempts = QuestionPublishAttempt::query()
            ->where('status', 'published')
            ->whereNotNull('revizy_question_id')
            ->where(function ($q): void {
                $q->where('name', 'like', 'Un % / Une %')
                    ->orWhere('name', 'like', 'Une % / Un %')
                    ->orWhere('name', 'like', 'Le % / La %')
                    ->orWhere('name', 'like', 'La % / Le %');
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $drafted = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($attempts as $attempt) {
            $conceptId = (string) $attempt->concept_id;
            $vocab = $vocabByConcept->get($conceptId);
            if (! $vocab instanceof VocabularyItem) {
                $skipped++;
                continue;
            }

            $word = $this->normalizeText((string) $vocab->word);
            $tokens = preg_split('/\\s+/u', trim($word), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            if (count($tokens) <= 3) {
                $skipped++;
                continue;
            }

            $revizyId = trim((string) $attempt->revizy_question_id);
            if ($revizyId === '' || ! is_numeric($revizyId)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $drafted++;
                $this->line("Would draft revizy={$revizyId} concept={$conceptId} word=\"{$word}\" name=\"{$attempt->name}\"");
                continue;
            }

            try {
                $client->updateQuestion($revizyId, status: 'draft');

                QuestionPublishAttempt::query()->create([
                    'local_question_id' => (int) $attempt->local_question_id,
                    'concept_id' => $conceptId,
                    'name' => (string) $attempt->name,
                    'question_data' => (string) $attempt->question_data,
                    'status' => 'drafted',
                    'revizy_question_id' => $revizyId,
                    'unaccepted_at' => now(),
                    'error_message' => 'Auto-drafted: grammar trap question created for phrase with >3 words.',
                ]);

                $drafted++;
            } catch (Throwable $exception) {
                $failed++;
                $statusCode = $exception instanceof RevizyPublishException ? $exception->statusCode() : null;
                $responseBody = $exception instanceof RevizyPublishException ? $exception->responseBody() : null;

                Log::warning('raiida.fix_long_article_grammar_traps.failed', [
                    'concept_id' => $conceptId,
                    'vocabulary_item_id' => (int) $vocab->id,
                    'revizy_question_id' => $revizyId,
                    'status' => $statusCode,
                    'response' => $responseBody !== null ? mb_substr($responseBody, 0, 500, 'UTF-8') : null,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->info(
            "Done. drafted={$drafted} skipped={$skipped} failed={$failed} dry_run=" . ($dryRun ? '1' : '0')
        );

        return self::SUCCESS;
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace("\u{2019}", "'", $text);
        $text = str_replace(["\u{00A0}", "\u{202F}"], ' ', $text);
        $text = trim($text);
        $text = preg_replace('/\\s+/u', ' ', $text) ?? $text;

        return $text;
    }
}
