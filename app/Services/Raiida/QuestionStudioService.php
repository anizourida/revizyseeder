<?php

namespace App\Services\Raiida;

use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\Exceptions\RevizyPublishException;
use Throwable;

class QuestionStudioService
{
    public function __construct(
        private readonly QuestionGeneratorService $generator,
        private readonly QuestionJsonNormalizer $normalizer,
        private readonly RevizyQuestionApiClient $revizyClient
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function generateQuestionsForAsset(int $assetId): array
    {
        return $this->generateQuestionsForAssetWithMode($assetId, false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function generateStandardQuestionsForAsset(int $assetId): array
    {
        return $this->generateQuestionsForAssetWithMode($assetId, true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function generateQuestionsForAssetWithMode(int $assetId, bool $standardOnly): array
    {
        $target = VocabularyItem::query()->with('baseWordAudio')->find($assetId);
        if (! $target instanceof VocabularyItem) {
            throw new RaiidaApiException('Vocabulary item not found', 404);
        }

        if (empty($target->concept_id)) {
            throw new RaiidaApiException('Item has no concept_id. Create a concept first.', 400);
        }

        if (empty($target->revizy_image_file_id)) {
            throw new RaiidaApiException('Item has no Revizy image ID. Upload image first.', 400);
        }

        if (empty($target->lexical_type)) {
            throw new RaiidaApiException('Item has no lexical_type. Please set it in the database.', 400);
        }

        $poolItems = VocabularyItem::query()
            ->where('grade', $target->grade)
            ->where('id', '!=', $target->id)
            ->with('baseWordAudio')
            ->get();

        $targetDict = $this->toQuestionDictionary($target);
        $poolDicts = $poolItems->map(fn (VocabularyItem $item): array => $this->toQuestionDictionary($item))->values()->all();

        $questions = $this->generator->generateQuestions(
            $targetDict,
            $poolDicts,
            includeFillText: ! $standardOnly,
            includeLetterByLetter: true
        );
        if ($questions === []) {
            throw new RaiidaApiException(
                'Could not generate questions. Insufficient distractors or missing data.',
                422
            );
        }

        return $questions;
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @return array{duplicates: array<int, array<string, mixed>>}
     */
    public function checkDuplicates(array $questions): array
    {
        $results = [];

        $conceptIds = collect($questions)
            ->map(static fn (array $question): string => (string) ($question['concept_id'] ?? ''))
            ->filter(static fn (string $conceptId): bool => $conceptId !== '')
            ->unique()
            ->values()
            ->all();

        if ($conceptIds === []) {
            return ['duplicates' => []];
        }

        $publishedAttempts = QuestionPublishAttempt::query()
            ->whereIn('concept_id', $conceptIds)
            ->where('status', 'published')
            ->get()
            ->groupBy('concept_id');

        foreach ($questions as $question) {
            $questionConceptId = (string) ($question['concept_id'] ?? '');
            if ($questionConceptId === '' || ! $publishedAttempts->has($questionConceptId)) {
                continue;
            }

            $questionData = $question['data'] ?? [];
            if (! is_array($questionData)) {
                continue;
            }

            $normalizedIncoming = $this->normalizer->normalize($questionData);
            $existingRevizyId = null;
            $isDuplicate = false;

            foreach ($publishedAttempts[$questionConceptId] as $attempt) {
                try {
                    $storedData = json_decode((string) $attempt->question_data, true, 512, JSON_THROW_ON_ERROR);
                    if (! is_array($storedData)) {
                        continue;
                    }

                    if ($this->normalizer->normalize($storedData) === $normalizedIncoming) {
                        $isDuplicate = true;
                        $existingRevizyId = $attempt->revizy_question_id;
                        break;
                    }
                } catch (Throwable) {
                    continue;
                }
            }

            if ($isDuplicate) {
                $results[] = [
                    'index' => (int) ($question['index'] ?? 0),
                    'is_published' => true,
                    'revizy_question_id' => $existingRevizyId,
                ];
            }
        }

        return ['duplicates' => $results];
    }

    /**
     * @param  array{concept_id:mixed,name:mixed,type:mixed,status?:mixed,data:mixed}  $payload
     * @return array<string, mixed>
     */
    public function publishQuestion(int $localQuestionId, array $payload): array
    {
        $conceptId = (string) ($payload['concept_id'] ?? '');
        $name = (string) ($payload['name'] ?? '');
        $type = (string) ($payload['type'] ?? 'universal');
        $status = (string) ($payload['status'] ?? 'published');
        $questionData = $payload['data'] ?? [];

        if (! is_array($questionData)) {
            throw new RaiidaApiException('Invalid request payload.', 422);
        }

        $attempt = QuestionPublishAttempt::query()->create([
            'local_question_id' => $localQuestionId,
            'concept_id' => $conceptId,
            'name' => $name,
            'question_data' => $this->encodeQuestionData($questionData),
            'status' => 'pending',
        ]);

        $publishedAttempts = QuestionPublishAttempt::query()
            ->where('concept_id', $conceptId)
            ->where('status', 'published')
            ->get();

        $normalizedIncoming = $this->normalizer->normalize($questionData);
        $existingRevizyId = null;

        foreach ($publishedAttempts as $publishedAttempt) {
            try {
                $stored = json_decode((string) $publishedAttempt->question_data, true, 512, JSON_THROW_ON_ERROR);
                if (! is_array($stored)) {
                    continue;
                }

                if ($this->normalizer->normalize($stored) === $normalizedIncoming) {
                    $existingRevizyId = $publishedAttempt->revizy_question_id;
                    break;
                }
            } catch (Throwable) {
                continue;
            }
        }

        if ($existingRevizyId !== null) {
            $attempt->status = 'published';
            $attempt->published_at = now();
            $attempt->revizy_question_id = $existingRevizyId;
            $attempt->error_message = 'Duplicate of internal question';
            $attempt->save();

            return [
                'success' => true,
                'revizy_question_id' => $existingRevizyId,
                'attempt_id' => $attempt->id,
                'is_duplicate' => true,
            ];
        }

        try {
            $publishResult = $this->revizyClient->publishQuestion(
                $conceptId,
                $name,
                $type,
                $status,
                $questionData
            );

            $attempt->status = 'published';
            $attempt->published_at = now();
            $attempt->revizy_question_id = $publishResult['id'] ?? null;
            $attempt->save();

            return [
                'success' => true,
                'revizy_question_id' => $publishResult['id'] ?? null,
                'attempt_id' => $attempt->id,
            ];
        } catch (RevizyPublishException $exception) {
            $attempt->status = 'failed';
            $attempt->failed_at = now();
            if ($exception->statusCode() !== null) {
                $attempt->error_message = 'HTTP ' . $exception->statusCode() . ': ' . ($exception->responseBody() ?? '');
            } else {
                $attempt->error_message = $exception->getMessage();
            }
            $attempt->save();

            if ($exception->statusCode() !== null) {
                throw new RaiidaApiException('Failed to publish: ' . ($exception->responseBody() ?? ''), 500);
            }

            throw new RaiidaApiException($exception->getMessage(), 500);
        } catch (Throwable $exception) {
            $attempt->status = 'failed';
            $attempt->failed_at = now();
            $attempt->error_message = $exception->getMessage();
            $attempt->save();

            throw new RaiidaApiException($exception->getMessage(), 500);
        }
    }

    /**
     * @param  array{concept_id:mixed,name:mixed,data:mixed}  $payload
     * @return array<string, mixed>
     */
    public function unacceptQuestion(int $localQuestionId, array $payload): array
    {
        $questionData = $payload['data'] ?? [];
        if (! is_array($questionData)) {
            throw new RaiidaApiException('Invalid request payload.', 422);
        }

        $attempt = QuestionPublishAttempt::query()->create([
            'local_question_id' => $localQuestionId,
            'concept_id' => (string) ($payload['concept_id'] ?? ''),
            'name' => (string) ($payload['name'] ?? ''),
            'question_data' => $this->encodeQuestionData($questionData),
            'status' => 'unaccepted',
            'unaccepted_at' => now(),
        ]);

        return [
            'success' => true,
            'attempt_id' => $attempt->id,
            'status' => 'unaccepted',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function batchGenerateAndPublish(?string $period = null): array
    {
        $periodCode = $period !== null ? strtoupper(trim($period)) : null;
        if ($periodCode === '') {
            $periodCode = null;
        }

        $publishedConcepts = QuestionPublishAttempt::query()
            ->where('status', 'published')
            ->groupBy('concept_id')
            ->pluck('concept_id')
            ->map(static fn ($value): string => (string) $value)
            ->all();

        $publishedConceptSet = array_fill_keys($publishedConcepts, true);

        $allVocabularyQuery = VocabularyItem::query()
            ->whereNotNull('concept_id')
            ->where('concept_id', '!=', '');

        if ($periodCode !== null) {
            $allVocabularyQuery->where('period', $periodCode);
        }

        $allVocabulary = $allVocabularyQuery->get();

        $itemsToProcess = $allVocabulary
            ->filter(static function (VocabularyItem $item) use ($publishedConceptSet): bool {
                return ! isset($publishedConceptSet[(string) $item->concept_id]);
            })
            ->values();

        if ($itemsToProcess->isEmpty()) {
            $scope = $periodCode !== null ? ' for period '.$periodCode : '';

            return [
                'success' => true,
                'message' => 'All vocabulary items with concept_id already have questions'.$scope.'.',
                'total' => 0,
                'generated' => 0,
                'published' => 0,
                'failed' => 0,
                'skipped' => 0,
                'details' => [],
            ];
        }

        $itemsByGrade = $allVocabulary->groupBy('grade');

        $results = [];
        $totalGenerated = 0;
        $totalPublished = 0;
        $totalFailed = 0;
        $totalSkipped = 0;

        foreach ($itemsToProcess as $item) {
            $itemResult = [
                'id' => $item->id,
                'word' => $item->word,
                'concept_id' => $item->concept_id,
                'grade' => $item->grade,
                'questions_generated' => 0,
                'questions_published' => 0,
                'questions_failed' => 0,
                'status' => 'pending',
                'errors' => [],
            ];

            if (empty($item->revizy_image_file_id) || empty($item->lexical_type)) {
                $missingField = empty($item->revizy_image_file_id) ? 'image' : 'lexical_type';
                $itemResult['status'] = 'skipped';
                $itemResult['errors'][] = 'Missing ' . $missingField;
                $totalSkipped++;
                $results[] = $itemResult;

                continue;
            }

            $poolItems = ($itemsByGrade[$item->grade] ?? collect())
                ->filter(static fn (VocabularyItem $poolItem): bool => $poolItem->id !== $item->id)
                ->values();

            $targetDict = $this->toQuestionDictionary($item);
            $poolDicts = $poolItems->map(fn (VocabularyItem $poolItem): array => $this->toQuestionDictionary($poolItem))->all();

            try {
                $questions = $this->generator->generateQuestions($targetDict, $poolDicts);
            } catch (Throwable $exception) {
                $itemResult['status'] = 'error';
                $itemResult['errors'][] = 'Generation error: ' . $exception->getMessage();
                $totalFailed++;
                $results[] = $itemResult;

                continue;
            }

            if ($questions === []) {
                $itemResult['status'] = 'skipped';
                $itemResult['errors'][] = 'No questions generated';
                $totalSkipped++;
                $results[] = $itemResult;

                continue;
            }

            $itemResult['questions_generated'] = count($questions);
            $totalGenerated += count($questions);

            foreach ($questions as $index => $question) {
                $attempt = null;

                try {
                    $attempt = QuestionPublishAttempt::query()->create([
                        'local_question_id' => (int) $index,
                        'concept_id' => (string) $item->concept_id,
                        'name' => (string) ($question['name'] ?? ''),
                        'question_data' => $this->encodeQuestionData($question['data'] ?? []),
                        'status' => 'pending',
                    ]);

                    $publishResult = $this->revizyClient->publishQuestion(
                        (string) $item->concept_id,
                        (string) ($question['name'] ?? 'Unknown Question'),
                        (string) ($question['type'] ?? 'universal'),
                        'published',
                        is_array($question['data'] ?? null) ? $question['data'] : []
                    );

                    $attempt->status = 'published';
                    $attempt->published_at = now();
                    $attempt->revizy_question_id = $publishResult['id'] ?? null;
                    $attempt->save();

                    $itemResult['questions_published']++;
                    $totalPublished++;
                } catch (RevizyPublishException $exception) {
                    if ($attempt instanceof QuestionPublishAttempt) {
                        $attempt->status = 'failed';
                        $attempt->failed_at = now();
                        if ($exception->statusCode() !== null) {
                            $attempt->error_message = 'HTTP ' . $exception->statusCode() . ': '
                                . mb_substr((string) ($exception->responseBody() ?? ''), 0, 200, 'UTF-8');
                        } else {
                            $attempt->error_message = mb_substr($exception->getMessage(), 0, 200, 'UTF-8');
                        }
                        $attempt->save();
                    }

                    $itemResult['questions_failed']++;
                    if ($exception->statusCode() !== null) {
                        $itemResult['errors'][] = 'Q' . ($index + 1) . ': HTTP ' . $exception->statusCode();
                    } else {
                        $itemResult['errors'][] = 'Q' . ($index + 1) . ': '
                            . mb_substr($exception->getMessage(), 0, 100, 'UTF-8');
                    }
                    $totalFailed++;
                } catch (Throwable $exception) {
                    if ($attempt instanceof QuestionPublishAttempt) {
                        $attempt->status = 'failed';
                        $attempt->failed_at = now();
                        $attempt->error_message = mb_substr($exception->getMessage(), 0, 200, 'UTF-8');
                        $attempt->save();
                    }

                    $itemResult['questions_failed']++;
                    $itemResult['errors'][] = 'Q' . ($index + 1) . ': '
                        . mb_substr($exception->getMessage(), 0, 100, 'UTF-8');
                    $totalFailed++;
                }
            }

            $itemResult['status'] = $itemResult['questions_failed'] === 0 ? 'done' : 'partial';
            $results[] = $itemResult;
        }

        return [
            'success' => true,
            'message' => 'Processed ' . count($itemsToProcess) . ' items'
                . ($periodCode !== null ? ' for period ' . $periodCode : '') . '.',
            'total' => count($itemsToProcess),
            'generated' => $totalGenerated,
            'published' => $totalPublished,
            'failed' => $totalFailed,
            'skipped' => $totalSkipped,
            'details' => $results,
        ];
    }

    /**
     * Generate and publish standard questions (no fill_text) for vocabulary items with concept_id
     * that have no published questions yet.
     *
     * @param  array{limit?:int,grade?:string,period?:string,week?:string,verbose?:bool}  $options
     * @return array<string, mixed>
     */
    public function batchGenerateAndPublishStandard(array $options = []): array
    {
        $limit = max(1, min((int) ($options['limit'] ?? 5000), 50000));
        $verbose = (bool) ($options['verbose'] ?? false);

        $grade = strtoupper(trim((string) ($options['grade'] ?? '')));
        if ($grade === '') {
            $grade = null;
        }

        $period = strtoupper(trim((string) ($options['period'] ?? '')));
        if ($period === '') {
            $period = null;
        }

        $week = strtoupper(trim((string) ($options['week'] ?? '')));
        if ($week === '') {
            $week = null;
        }

        $publishedConcepts = QuestionPublishAttempt::query()
            ->where('status', 'published')
            ->groupBy('concept_id')
            ->pluck('concept_id')
            ->map(static fn ($value): string => (string) $value)
            ->all();

        $publishedConceptSet = array_fill_keys($publishedConcepts, true);

        $allVocabularyQuery = VocabularyItem::query()
            ->whereNotNull('concept_id')
            ->where('concept_id', '!=', '')
            ->with('baseWordAudio')
            ->orderBy('id');

        if ($grade !== null) {
            $allVocabularyQuery->where('grade', $grade);
        }
        if ($period !== null) {
            $allVocabularyQuery->where('period', $period);
        }
        if ($week !== null) {
            $allVocabularyQuery->where('week', $week);
        }

        $allVocabulary = $allVocabularyQuery->get();

        $itemsToProcess = $allVocabulary
            ->filter(static function (VocabularyItem $item) use ($publishedConceptSet): bool {
                return ! isset($publishedConceptSet[(string) $item->concept_id]);
            })
            ->values()
            ->take($limit);

        if ($itemsToProcess->isEmpty()) {
            return [
                'success' => true,
                'message' => 'No vocabulary items found that need standard questions in the selected scope.',
                'total' => 0,
                'generated' => 0,
                'published' => 0,
                'failed' => 0,
                'skipped' => 0,
                'details' => [],
            ];
        }

        $itemsByGrade = $allVocabulary->groupBy('grade');

        $results = [];
        $totalGenerated = 0;
        $totalPublished = 0;
        $totalFailed = 0;
        $totalSkipped = 0;

        foreach ($itemsToProcess as $item) {
            $itemResult = [
                'id' => $item->id,
                'word' => $item->word,
                'concept_id' => $item->concept_id,
                'grade' => $item->grade,
                'questions_generated' => 0,
                'questions_published' => 0,
                'questions_failed' => 0,
                'status' => 'pending',
                'errors' => [],
            ];

            if (empty($item->revizy_image_file_id) || empty($item->lexical_type)) {
                $missingField = empty($item->revizy_image_file_id) ? 'image' : 'lexical_type';
                $itemResult['status'] = 'skipped';
                $itemResult['errors'][] = 'Missing ' . $missingField;
                $totalSkipped++;
                $results[] = $itemResult;

                continue;
            }

            $poolItems = ($itemsByGrade[$item->grade] ?? collect())
                ->filter(static fn (VocabularyItem $poolItem): bool => $poolItem->id !== $item->id)
                ->values();

            $targetDict = $this->toQuestionDictionary($item);
            $poolDicts = $poolItems->map(fn (VocabularyItem $poolItem): array => $this->toQuestionDictionary($poolItem))->all();

            try {
                $questions = $this->generator->generateQuestions(
                    $targetDict,
                    $poolDicts,
                    includeFillText: false,
                    includeLetterByLetter: true
                );
            } catch (Throwable $exception) {
                $itemResult['status'] = 'error';
                $itemResult['errors'][] = 'Generation error: ' . $exception->getMessage();
                $totalFailed++;
                $results[] = $itemResult;

                continue;
            }

            if ($questions === []) {
                $itemResult['status'] = 'skipped';
                $itemResult['errors'][] = 'No questions generated';
                $totalSkipped++;
                $results[] = $itemResult;

                continue;
            }

            $itemResult['questions_generated'] = count($questions);
            $totalGenerated += count($questions);

            foreach ($questions as $index => $question) {
                try {
                    $result = $this->publishQuestion((int) $index, [
                        'concept_id' => (string) ($question['concept_id'] ?? ''),
                        'name' => (string) ($question['name'] ?? 'Question'),
                        'type' => (string) ($question['type'] ?? 'universal'),
                        'status' => 'published',
                        'data' => is_array($question['data'] ?? null) ? $question['data'] : [],
                    ]);

                    if ((bool) ($result['success'] ?? false)) {
                        $itemResult['questions_published']++;
                        $totalPublished++;
                    }
                } catch (Throwable $exception) {
                    $itemResult['questions_failed']++;
                    $itemResult['errors'][] = 'Q' . ($index + 1) . ': '
                        . mb_substr($exception->getMessage(), 0, 120, 'UTF-8');
                    $totalFailed++;
                }
            }

            $itemResult['status'] = $itemResult['questions_failed'] === 0 ? 'done' : 'partial';
            if ($verbose) {
                $itemResult['questions'] = $questions;
            }
            $results[] = $itemResult;
        }

        return [
            'success' => true,
            'message' => 'Processed ' . count($itemsToProcess) . ' items.',
            'total' => count($itemsToProcess),
            'generated' => $totalGenerated,
            'published' => $totalPublished,
            'failed' => $totalFailed,
            'skipped' => $totalSkipped,
            'details' => $results,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toQuestionDictionary(VocabularyItem $item): array
    {
        $data = $item->toArray();
        $data['name'] = $item->word;
        $data['name_ar'] = $item->ar_translation;
        $data['base_word_audio_revizy_id'] = $item->baseWordAudio?->revizy_file_id;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $questionData
     */
    private function encodeQuestionData(array $questionData): string
    {
        $encoded = json_encode($questionData);

        return $encoded !== false ? $encoded : '{}';
    }
}
