<?php

namespace App\Console\Commands;

use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RevizyPublishException;
use App\Services\Raiida\RevizyQuestionApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RaiidaFixLetterByLetterBasewordQuestionsCommand extends Command
{
    protected $signature = 'raiida:fix-letter-by-letter-baseword
        {--grade= : Grade code (e.g. N1)}
        {--period= : Period code (e.g. P4)}
        {--week= : Week code (e.g. SEM2)}
        {--limit=5000 : Max items to process}
        {--create-missing : Create missing letter_by_letter questions when eligible}
        {--dry-run : Do not call Revizy update/publish}';

    protected $description = 'Ensure already-published letter_by_letter questions use base word in answers and base-word audio (Revizy file id) in question media.';

    private const INSTRUCTION = 'كوّن الكلمة حرفاً حرفاً.';

    public function handle(RevizyQuestionApiClient $client): int
    {
        $grade = strtoupper(trim((string) $this->option('grade')));
        $period = strtoupper(trim((string) $this->option('period')));
        $week = strtoupper(trim((string) $this->option('week')));
        $limit = max(1, min((int) $this->option('limit'), 50000));
        $dryRun = (bool) $this->option('dry-run');
        $createMissing = (bool) $this->option('create-missing');
        $verbose = $this->output->isVerbose();

        if ($grade === '') {
            $grade = null;
        }
        if ($period === '') {
            $period = null;
        }
        if ($week === '') {
            $week = null;
        }

        $query = VocabularyItem::query()
            ->whereNotNull('concept_id')
            ->where('concept_id', '!=', '')
            ->with(['baseWordAudio:id,vocabulary_item_id,revizy_file_id'])
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
        $created = 0;
        $skipped = 0;
        $failed = 0;
        $skipReasons = [
            'no_revizy_concept' => 0,
            'not_eligible_length' => 0,
            'missing_image' => 0,
            'missing_base_audio_revizy_id' => 0,
            'missing_audio_revizy_id' => 0,
            'no_attempt' => 0,
            'missing_revizy_question_id' => 0,
            'invalid_payload' => 0,
            'already_ok' => 0,
        ];

        foreach ($items as $item) {
            $conceptId = trim((string) $item->concept_id);
            if ($conceptId === '' || ! is_numeric($conceptId)) {
                $skipped++;
                $skipReasons['no_revizy_concept']++;
                continue;
            }

            $word = $this->normalizeText((string) $item->word);
            $bare = $this->determineBaseWord($item);
            if ($bare === '') {
                $skipped++;
                $skipReasons['invalid_payload']++;
                continue;
            }

            $imageId = trim((string) $item->revizy_image_file_id);
            if ($imageId === '') {
                $skipped++;
                $skipReasons['missing_image']++;
                continue;
            }

            $attempt = $this->findPublishedLetterByLetterAttempt($conceptId);

            if (! $attempt instanceof QuestionPublishAttempt) {
                if (! $createMissing) {
                    $skipped++;
                    $skipReasons['no_attempt']++;
                    continue;
                }

                if (! $this->isEligibleBareWord($item, $bare)) {
                    $skipped++;
                    $skipReasons['not_eligible_length']++;
                    continue;
                }

                $baseWordAudioRevizyId = trim((string) ($item->baseWordAudio?->revizy_file_id ?? ''));
                if ($baseWordAudioRevizyId === '') {
                    $skipped++;
                    $skipReasons['missing_base_audio_revizy_id']++;
                    if ($verbose) {
                        $this->line("Skip create concept={$conceptId} word=\"{$word}\": missing base_word_audio_revizy_id");
                    }
                    continue;
                }

                $payload = [
                    'instruction' => self::INSTRUCTION,
                    'body' => null,
                    'media' => [
                        'image' => $imageId,
                        'audio' => $baseWordAudioRevizyId,
                    ],
                    'answers' => [
                        ['body' => $bare, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]],
                    ],
                ];

                $name = $word . ' - Lettre par lettre';

                if ($verbose) {
                    $this->line("Create concept={$conceptId} word=\"{$word}\" base_word=\"{$bare}\" base_word_audio_revizy_id={$baseWordAudioRevizyId}");
                }

                if ($dryRun) {
                    $created++;
                    continue;
                }

                try {
                    $result = $client->publishQuestion($conceptId, $name, 'letter_by_letter', 'published', $payload);
                    $revizyQuestionId = (string) ($result['id'] ?? '');

                    QuestionPublishAttempt::query()->create([
                        'local_question_id' => (int) $item->id,
                        'concept_id' => $conceptId,
                        'name' => $name,
                        'question_data' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '',
                        'status' => 'published',
                        'revizy_question_id' => $revizyQuestionId !== '' ? $revizyQuestionId : null,
                        'published_at' => now(),
                    ]);

                    $created++;
                } catch (Throwable $exception) {
                    $failed++;
                    $this->logFailure('create', $conceptId, $item->id, null, $exception);
                }

                continue;
            }

            $revizyId = trim((string) $attempt->revizy_question_id);
            if ($revizyId === '' || ! is_numeric($revizyId)) {
                $skipped++;
                $skipReasons['missing_revizy_question_id']++;
                continue;
            }

            $stored = null;
            try {
                $decoded = json_decode((string) $attempt->question_data, true, 512, JSON_THROW_ON_ERROR);
                $stored = is_array($decoded) ? $decoded : null;
            } catch (Throwable) {
                $stored = null;
            }

            if (! is_array($stored)) {
                $skipped++;
                $skipReasons['invalid_payload']++;
                continue;
            }

            $audioRevizyId = $this->resolveLetterByLetterAudioRevizyId($item, $bare);
            if ($audioRevizyId === null) {
                $skipped++;
                $skipReasons['missing_audio_revizy_id']++;
                if ($verbose) {
                    $this->line("Skip fix concept={$conceptId} word=\"{$word}\": missing base_word_audio_revizy_id and revizy_audio_file_id");
                }
                continue;
            }

            $next = $stored;
            $next['instruction'] = self::INSTRUCTION;
            $next['body'] = null;
            $next['media'] = is_array($next['media'] ?? null) ? $next['media'] : [];
            $next['media']['image'] = $imageId;
            $next['media']['audio'] = $audioRevizyId;
            $next['answers'] = [
                ['body' => $bare, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]],
            ];

            $alreadyOk = ($stored == $next);
            if ($alreadyOk) {
                $skipped++;
                $skipReasons['already_ok']++;
                continue;
            }

            if ($verbose) {
                $this->line("Fix concept={$conceptId} revizy={$revizyId} word=\"{$word}\" base_word=\"{$bare}\" audio_revizy_id={$audioRevizyId}");
            }

            if ($dryRun) {
                $updated++;
                continue;
            }

            try {
                $client->updateQuestion($revizyId, type: 'letter_by_letter', status: 'published', data: $next);
                $attempt->question_data = json_encode($next, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: (string) $attempt->question_data;
                $attempt->save();
                $updated++;
            } catch (Throwable $exception) {
                $failed++;
                $this->logFailure('update', $conceptId, $item->id, $revizyId, $exception);
            }
        }

        $this->info(
            "Done. updated={$updated} created={$created} skipped={$skipped} failed={$failed} dry_run=" . ($dryRun ? '1' : '0')
                . ' skip_reasons=' . json_encode($skipReasons, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return self::SUCCESS;
    }

    private function findPublishedLetterByLetterAttempt(string $conceptId): ?QuestionPublishAttempt
    {
        return QuestionPublishAttempt::query()
            ->where('concept_id', $conceptId)
            ->where('status', 'published')
            ->whereNotNull('revizy_question_id')
            ->where('name', 'like', '%Lettre par lettre%')
            ->orderByDesc('id')
            ->first();
    }

    private function isEligibleBareWord(VocabularyItem $item, string $bare): bool
    {
        if (mb_strlen($bare, 'UTF-8') >= 7) {
            return false;
        }

        $grade = strtoupper(trim((string) $item->grade));
        $gradeNum = (int) preg_replace('/[^0-9]/', '', $grade);
        if ($gradeNum <= 1 && $gradeNum > 0 && mb_strlen($bare, 'UTF-8') > 5) {
            return false;
        }

        return true;
    }

    private function resolveLetterByLetterAudioRevizyId(VocabularyItem $item, string $bare): ?string
    {
        $baseWordAudio = trim((string) ($item->baseWordAudio?->revizy_file_id ?? ''));
        if ($baseWordAudio !== '') {
            return $baseWordAudio;
        }

        // If there is no dedicated base-word audio, fallback to the general vocab audio
        // when the "base word" is effectively the same as the vocabulary word.
        $audio = trim((string) ($item->revizy_audio_file_id ?? ''));
        if ($audio === '') {
            return null;
        }

        $word = $this->normalizeText((string) $item->word);
        if ($word === '') {
            return null;
        }

        $bareNorm = mb_strtolower($this->normalizeText($bare), 'UTF-8');
        $wordNorm = mb_strtolower($this->normalizeText($word), 'UTF-8');

        return $bareNorm === $wordNorm ? $audio : null;
    }

    private function determineBaseWord(VocabularyItem $item): string
    {
        $base = $this->normalizeText((string) ($item->base_word ?? ''));
        if ($base !== '') {
            return $base;
        }

        $word = $this->normalizeText((string) $item->word);
        if ($word === '') {
            return '';
        }

        $prefixes = [
            "L'", "l'", "D'", "d'",
            'Le ', 'le ', 'La ', 'la ', 'Les ', 'les ',
            'Un ', 'un ', 'Une ', 'une ', 'Des ', 'des ',
            'Du ', 'du ', 'De ', 'de ',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($word, $prefix)) {
                return trim((string) mb_substr($word, mb_strlen($prefix, 'UTF-8'), null, 'UTF-8'));
            }
        }

        return $word;
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace("\u{2019}", "'", $text);
        $text = str_replace(["\u{00A0}", "\u{202F}"], ' ', $text);
        $text = trim($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }

    private function logFailure(string $action, string $conceptId, int $vocabId, ?string $revizyId, Throwable $exception): void
    {
        $statusCode = $exception instanceof RevizyPublishException ? $exception->statusCode() : null;
        $responseBody = $exception instanceof RevizyPublishException ? $exception->responseBody() : null;

        Log::warning('raiida.fix_letter_by_letter_baseword.failed', [
            'action' => $action,
            'concept_id' => $conceptId,
            'vocabulary_item_id' => $vocabId,
            'revizy_question_id' => $revizyId,
            'status' => $statusCode,
            'response' => $responseBody !== null ? mb_substr($responseBody, 0, 500, 'UTF-8') : null,
            'error' => $exception->getMessage(),
        ]);
    }
}
