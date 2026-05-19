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

class RaiidaFixPluralGrammarTrapQuestionsCommand extends Command
{
    protected $signature = 'raiida:fix-plural-grammar-traps
        {--grade= : Grade code (e.g. N1)}
        {--period= : Period code (e.g. P4)}
        {--week= : Week code (e.g. SEM2)}
        {--limit=5000 : Max questions to inspect}
        {--dry-run : Do not call Revizy update}';

    protected $description = 'Draft wrong published grammar-trap questions (Un/Une) for plural vocab (Les/Des) already online in Revizy.';

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
        // "Un X / Une X" or "Une X / Un X".
        $attempts = QuestionPublishAttempt::query()
            ->where('status', 'published')
            ->whereNotNull('revizy_question_id')
            ->where(function ($q): void {
                $q->where('name', 'like', 'Un % / Une %')
                    ->orWhere('name', 'like', 'Une % / Un %');
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
            $lower = mb_strtolower($word, 'UTF-8');
            $isPluralDefinite = str_starts_with($lower, 'les ') || str_starts_with($lower, 'des ');
            if (! $isPluralDefinite) {
                $skipped++;
                continue;
            }

            $revizyId = trim((string) $attempt->revizy_question_id);
            if ($revizyId === '' || ! is_numeric($revizyId)) {
                $skipped++;
                continue;
            }

            $data = null;
            try {
                $decoded = json_decode((string) $attempt->question_data, true, 512, JSON_THROW_ON_ERROR);
                $data = is_array($decoded) ? $decoded : null;
            } catch (Throwable) {
                $data = null;
            }

            if (! is_array($data) || ! $this->looksLikeUnUneTrap($data)) {
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
                    'error_message' => 'Auto-drafted: plural vocab (Les/Des) had Un/Une grammar trap.',
                ]);

                $drafted++;
            } catch (Throwable $exception) {
                $failed++;
                $statusCode = $exception instanceof RevizyPublishException ? $exception->statusCode() : null;
                $responseBody = $exception instanceof RevizyPublishException ? $exception->responseBody() : null;

                Log::warning('raiida.fix_plural_grammar_traps.failed', [
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
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }

    /**
     * @param  array<string, mixed>  $questionData
     */
    private function looksLikeUnUneTrap(array $questionData): bool
    {
        $answers = $questionData['answers'] ?? null;
        if (! is_array($answers) || count($answers) < 2) {
            return false;
        }

        $bodies = [];
        foreach ($answers as $answer) {
            if (! is_array($answer)) {
                continue;
            }
            $body = trim((string) ($answer['body'] ?? ''));
            if ($body !== '') {
                $bodies[] = $this->normalizeText($body);
            }
        }

        $bodies = array_values(array_unique($bodies));
        if (count($bodies) !== 2) {
            return false;
        }

        $a = $bodies[0];
        $b = $bodies[1];

        $aLower = mb_strtolower($a, 'UTF-8');
        $bLower = mb_strtolower($b, 'UTF-8');

        $pairs = [
            ['un ', 'une '],
            ['une ', 'un '],
        ];

        foreach ($pairs as [$p1, $p2]) {
            if (str_starts_with($aLower, $p1) && str_starts_with($bLower, $p2)) {
                $restA = trim(mb_substr($a, mb_strlen($p1, 'UTF-8'), null, 'UTF-8'));
                $restB = trim(mb_substr($b, mb_strlen($p2, 'UTF-8'), null, 'UTF-8'));

                return $restA !== '' && mb_strtolower($restA, 'UTF-8') === mb_strtolower($restB, 'UTF-8');
            }
        }

        return false;
    }
}

