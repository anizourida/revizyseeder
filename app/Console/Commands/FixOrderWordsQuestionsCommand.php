<?php

namespace App\Console\Commands;

use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RevizyPublishException;
use App\Services\Raiida\RevizyQuestionApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class FixOrderWordsQuestionsCommand extends Command
{
    protected $signature = 'raiida:fix-order-words
        {--period= : Period code (e.g. P5)}
        {--week= : Week code (e.g. SEM2)}
        {--grade= : Grade code (e.g. N1)}
        {--limit=5000 : Max items to process}
        {--dry-run : Do not call Revizy update}';

    protected $description = 'Backfill already-published order_words questions so they have no question body and rely on image/audio media.';

    public function handle(RevizyQuestionApiClient $client): int
    {
        $period = strtoupper(trim((string) $this->option('period')));
        $week = strtoupper(trim((string) $this->option('week')));
        $grade = strtoupper(trim((string) $this->option('grade')));
        $limit = max(1, min((int) $this->option('limit'), 50000));
        $dryRun = (bool) $this->option('dry-run');
        $verbose = $this->output->isVerbose();

        if ($period === '') {
            $period = null;
        }
        if ($week === '') {
            $week = null;
        }
        if ($grade === '') {
            $grade = null;
        }

        $query = VocabularyItem::query()
            ->whereNotNull('concept_id')
            ->where('concept_id', '!=', '')
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

        $items = $query->limit($limit)->get();

        $updated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($items as $item) {
            $word = trim((string) $item->word);
            $tokens = preg_split('/\s+/u', $word, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            if (count($tokens) < 3) {
                continue;
            }

            $attempt = QuestionPublishAttempt::query()
                ->where('concept_id', (string) $item->concept_id)
                ->where('status', 'published')
                ->where('name', 'like', '%ترتيب الكلمات%')
                ->whereNotNull('revizy_question_id')
                ->orderByDesc('id')
                ->first();

            if (! $attempt instanceof QuestionPublishAttempt) {
                $skipped++;
                continue;
            }

            $revizyId = trim((string) $attempt->revizy_question_id);
            if ($revizyId === '') {
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

            if (! is_array($data)) {
                $skipped++;
                continue;
            }

            $hasImage = trim((string) $item->revizy_image_file_id) !== '';
            $hasAudio = trim((string) $item->revizy_audio_file_id) !== '';
            if (! $hasImage && ! $hasAudio) {
                $skipped++;
                continue;
            }

            $data['body'] = null;
            $data['media'] = is_array($data['media'] ?? null) ? $data['media'] : [];
            $data['media']['image'] = $item->revizy_image_file_id ?: null;
            $data['media']['audio'] = $item->revizy_audio_file_id ?: null;

            $alreadyOk = empty($attempt->question_data) ? false : (
                ($decoded['body'] ?? null) === null
                && (($decoded['media']['image'] ?? null) === ($data['media']['image'] ?? null))
                && (($decoded['media']['audio'] ?? null) === ($data['media']['audio'] ?? null))
            );
            if ($alreadyOk) {
                $skipped++;
                continue;
            }

            if ($verbose) {
                $this->line("Fixing concept={$item->concept_id} revizy={$revizyId} word={$word}");
            }

            if ($dryRun) {
                $updated++;
                continue;
            }

            try {
                $client->updateQuestion($revizyId, type: 'order_words', status: 'published', data: $data);

                $attempt->question_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: $attempt->question_data;
                $attempt->save();

                $updated++;
            } catch (Throwable $exception) {
                $failed++;
                $statusCode = $exception instanceof RevizyPublishException ? $exception->statusCode() : null;
                $responseBody = $exception instanceof RevizyPublishException ? $exception->responseBody() : null;
                Log::warning('raiida.fix_order_words.failed', [
                    'concept_id' => (string) $item->concept_id,
                    'vocabulary_item_id' => (int) $item->id,
                    'revizy_question_id' => $revizyId,
                    'status' => $statusCode,
                    'response' => $responseBody !== null ? mb_substr($responseBody, 0, 500, 'UTF-8') : null,
                    'error' => $exception->getMessage(),
                ]);
                if ($verbose) {
                    $details = $exception->getMessage();
                    if ($statusCode !== null) {
                        $details .= " | HTTP {$statusCode}";
                    }
                    if (is_string($responseBody) && trim($responseBody) !== '') {
                        $details .= ' | ' . mb_substr(trim($responseBody), 0, 300, 'UTF-8');
                    }
                    $this->error("Failed revizy={$revizyId}: " . $details);
                }
            }
        }

        $this->info("Done. updated={$updated} skipped={$skipped} failed={$failed} dry_run=" . ($dryRun ? '1' : '0'));

        return self::SUCCESS;
    }
}
