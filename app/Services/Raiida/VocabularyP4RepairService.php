<?php

namespace App\Services\Raiida;

use App\Jobs\Raiida\TranslateVocabularyJob;
use App\Models\Raiida\Audio;
use App\Models\Raiida\FileAsset;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\External\RevizySystemClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VocabularyP4RepairService
{
    public function __construct(
        private readonly VocabularyExtractionService $extraction,
        private readonly AudioGenerationService $audioGeneration,
        private readonly RevizySystemClient $revizy,
        private readonly MediaFileLocator $mediaLocator,
    ) {
    }

    /**
     * @param  array{subject?:string,period?:string,weeks?:array<int,string>,grades?:array<int,string>}  $options
     * @return array{scope:array<string,mixed>,summary:array<string,mixed>,rows:array<int,array<string,mixed>>,lessons:array<int,array<string,mixed>>}
     */
    public function buildCorrectionMap(array $options = []): array
    {
        $scope = $this->normalizeScope($options);

        $query = VocabularyItem::query()
            ->select([
                'id',
                'word',
                'lesson_id',
                'grade',
                'subject',
                'period',
                'week',
                'image_path',
                'audio_path',
                'ar_translation',
                'concept_id',
                'flashcard_id',
                'revizy_audio_file_id',
            ])
            ->where('subject', $scope['subject'])
            ->where('period', $scope['period'])
            ->whereIn('grade', $scope['grades'])
            ->whereIn('week', $scope['weeks'])
            ->orderBy('lesson_id')
            ->orderBy('id');

        $items = $query->get();

        $rows = [];
        $lessonSummaries = [];

        $grouped = $items->groupBy(fn (VocabularyItem $item): string => $item->lesson_id . '|' . $item->grade);

        foreach ($grouped as $groupKey => $lessonItems) {
            [$lessonId, $grade] = explode('|', $groupKey, 2);
            $subject = (string) ($lessonItems->first()?->subject ?? $scope['subject']);
            $period = (string) ($lessonItems->first()?->period ?? $scope['period']);
            $week = (string) ($lessonItems->first()?->week ?? $scope['weeks'][0]);

            $source = $this->resolveLessonFileSource($lessonId);
            if (! is_array($source)) {
                $lessonSummaries[] = [
                    'lesson_id' => $lessonId,
                    'grade' => $grade,
                    'status' => 'missing_source_file',
                ];

                foreach ($lessonItems as $item) {
                    $rows[] = $this->buildRow(
                        $item,
                        (string) $item->word,
                        ['no_source_file']
                    );
                }

                continue;
            }

            $previewRows = $this->extraction->previewLessonVocabulary(
                (string) $source['full_path'],
                $lessonId,
                $grade,
                $subject,
                $period,
                $week
            );

            if ($previewRows === []) {
                $lessonSummaries[] = [
                    'lesson_id' => $lessonId,
                    'grade' => $grade,
                    'status' => 'empty_preview',
                    'file_asset_id' => $source['file_asset_id'],
                    'source_path' => $source['full_path'],
                ];

                foreach ($lessonItems as $item) {
                    $rows[] = $this->buildRow(
                        $item,
                        (string) $item->word,
                        ['empty_preview']
                    );
                }

                continue;
            }

            $indexByImage = [];
            $indexByWord = [];
            foreach ($previewRows as $previewRow) {
                $imagePath = (string) ($previewRow['image_path'] ?? '');
                $previewWord = (string) ($previewRow['word'] ?? '');
                if ($imagePath !== '') {
                    $indexByImage[$imagePath][] = $previewWord;
                }

                $normalizedPreviewWord = $this->normalizeWord($previewWord);
                if ($normalizedPreviewWord !== '') {
                    $indexByWord[$normalizedPreviewWord][] = $previewWord;
                }
            }

            $changed = 0;
            $ambiguous = 0;

            foreach ($lessonItems as $item) {
                $oldWord = (string) $item->word;
                $newWord = $oldWord;
                $flags = [];

                $imagePath = trim((string) ($item->image_path ?? ''));
                if ($imagePath !== '' && isset($indexByImage[$imagePath])) {
                    $candidates = array_values(array_unique($indexByImage[$imagePath]));
                    if (count($candidates) === 1) {
                        $newWord = (string) $candidates[0];
                    } else {
                        $flags[] = 'multiple_image_candidates';
                    }
                } else {
                    $normalized = $this->normalizeWord($oldWord);
                    $candidates = $normalized !== ''
                        ? array_values(array_unique($indexByWord[$normalized] ?? []))
                        : [];

                    if (count($candidates) === 1) {
                        $newWord = (string) $candidates[0];
                        $flags[] = 'fallback_word_match';
                    } elseif (count($candidates) > 1) {
                        $flags[] = 'multiple_word_candidates';
                    } else {
                        $flags[] = 'no_source_match';
                    }
                }

                if (trim($newWord) === '') {
                    $flags[] = 'empty_new_word';
                    $newWord = $oldWord;
                }

                if ($newWord !== $oldWord) {
                    $changed++;
                }
                if ($this->hasAmbiguity($flags)) {
                    $ambiguous++;
                }

                $rows[] = $this->buildRow($item, $newWord, $flags);
            }

            $lessonSummaries[] = [
                'lesson_id' => $lessonId,
                'grade' => $grade,
                'status' => 'ok',
                'file_asset_id' => $source['file_asset_id'],
                'source_path' => $source['full_path'],
                'source_rows' => count($previewRows),
                'db_rows' => $lessonItems->count(),
                'changed_rows' => $changed,
                'ambiguous_rows' => $ambiguous,
            ];
        }

        $summary = [
            'scanned_rows' => count($rows),
            'changed_rows' => count(array_filter($rows, static fn (array $row): bool => (bool) ($row['changed'] ?? false))),
            'ambiguous_rows' => count(array_filter($rows, fn (array $row): bool => $this->hasAmbiguity($this->extractFlags($row)))),
            'ready_to_apply_rows' => count(array_filter($rows, fn (array $row): bool => $this->isActionable($row))),
            'lessons_total' => count($lessonSummaries),
        ];

        return [
            'scope' => $scope,
            'summary' => $summary,
            'rows' => $rows,
            'lessons' => $lessonSummaries,
        ];
    }

    /**
     * @param  array<int,array<string,mixed>>  $rows
     * @param  array{sync_audio?:bool,queue_translations?:bool}  $options
     * @return array<string,mixed>
     */
    public function applyCorrectionMap(array $rows, array $options = []): array
    {
        $syncAudio = (bool) ($options['sync_audio'] ?? true);
        $queueTranslations = (bool) ($options['queue_translations'] ?? true);

        $summary = [
            'targeted_rows' => count($rows),
            'applied_rows' => 0,
            'skipped_rows' => 0,
            'collision_merges' => 0,
            'deleted_duplicate_rows' => 0,
            'failed_rows' => 0,
            'audio_regenerated' => 0,
            'audio_secret_replaced' => 0,
            'translation_rows_queued' => 0,
            'final_item_ids' => [],
            'errors' => [],
            'changes' => [],
        ];

        $finalItemIds = [];

        foreach ($rows as $row) {
            if (! $this->isActionable($row)) {
                $summary['skipped_rows']++;
                continue;
            }

            $itemId = (int) ($row['vocabulary_item_id'] ?? 0);
            $newWord = trim((string) ($row['new_word'] ?? ''));

            try {
                $change = DB::transaction(function () use ($itemId, $newWord): array {
                    $item = VocabularyItem::query()->lockForUpdate()->find($itemId);
                    if (! $item instanceof VocabularyItem) {
                        return [
                            'status' => 'missing_item',
                            'item_id' => $itemId,
                        ];
                    }

                    $oldWord = (string) $item->word;
                    if ($oldWord === $newWord) {
                        return [
                            'status' => 'already_updated',
                            'item_id' => (int) $item->id,
                            'final_item_id' => (int) $item->id,
                            'old_word' => $oldWord,
                            'new_word' => $newWord,
                        ];
                    }

                    $target = VocabularyItem::query()
                        ->lockForUpdate()
                        ->where('lesson_id', (string) $item->lesson_id)
                        ->where('grade', (string) $item->grade)
                        ->where('word', $newWord)
                        ->where('id', '!=', (int) $item->id)
                        ->first();

                    if (! $target instanceof VocabularyItem) {
                        $item->word = $newWord;
                        $item->save();

                        return [
                            'status' => 'updated',
                            'item_id' => (int) $item->id,
                            'final_item_id' => (int) $item->id,
                            'old_word' => $oldWord,
                            'new_word' => $newWord,
                            'merged' => false,
                        ];
                    }

                    [$keeper, $loser] = $this->chooseKeeperAndLoser($item, $target);
                    $this->mergeVocabularyRows($keeper, $loser);

                    $this->mergeAudioRows($keeper, $loser);

                    $deletedId = (int) $loser->id;
                    $loser->delete();

                    $keeperOldWord = (string) $keeper->word;
                    if ($keeperOldWord !== $newWord) {
                        $keeper->word = $newWord;
                        $keeper->save();
                    }

                    return [
                        'status' => 'updated_with_collision_merge',
                        'item_id' => (int) $item->id,
                        'final_item_id' => (int) $keeper->id,
                        'deleted_item_id' => $deletedId,
                        'old_word' => $oldWord,
                        'new_word' => $newWord,
                        'merged' => true,
                    ];
                });

                $status = (string) ($change['status'] ?? '');
                if (in_array($status, ['updated', 'updated_with_collision_merge', 'already_updated'], true)) {
                    $summary['applied_rows']++;
                    $finalId = (int) ($change['final_item_id'] ?? 0);
                    if ($finalId > 0) {
                        $finalItemIds[$finalId] = true;
                    }

                    if (($change['merged'] ?? false) === true) {
                        $summary['collision_merges']++;
                        if (isset($change['deleted_item_id'])) {
                            $summary['deleted_duplicate_rows']++;
                        }
                    }

                    $summary['changes'][] = $change;
                    continue;
                }

                $summary['failed_rows']++;
                $this->pushError($summary['errors'], "Vocabulary #{$itemId}: {$status}");
            } catch (\Throwable $exception) {
                $summary['failed_rows']++;
                $this->pushError($summary['errors'], "Vocabulary #{$itemId}: {$exception->getMessage()}");
            }
        }

        $finalIds = array_values(array_map('intval', array_keys($finalItemIds)));
        $summary['final_item_ids'] = $finalIds;

        if ($syncAudio && $finalIds !== []) {
            foreach ($finalIds as $itemId) {
                try {
                    $audioSummary = $this->audioGeneration->generateBatch([
                        'item_id' => $itemId,
                        'limit' => 1,
                        'force' => true,
                        'verbose' => false,
                    ]);

                    if ((int) ($audioSummary['generated_total'] ?? 0) > 0) {
                        $summary['audio_regenerated']++;
                    }

                    $item = VocabularyItem::query()->find($itemId);
                    if (! $item instanceof VocabularyItem) {
                        continue;
                    }

                    $secret = trim((string) ($item->revizy_audio_file_id ?? ''));
                    if ($secret === '') {
                        continue;
                    }

                    $audioPath = $this->mediaLocator->resolveAudioPath($item);
                    if (! is_string($audioPath) || ! is_file($audioPath)) {
                        $this->pushError($summary['errors'], "Vocabulary #{$itemId}: regenerated audio file missing for secret replacement");
                        continue;
                    }

                    $this->revizy->updateFile($secret, $audioPath, (string) ($item->word ?: ('Asset ' . $itemId)));
                    $summary['audio_secret_replaced']++;
                } catch (\Throwable $exception) {
                    $this->pushError($summary['errors'], "Vocabulary #{$itemId}: audio regeneration/sync failed: {$exception->getMessage()}");
                }
            }
        }

        if ($queueTranslations && $finalIds !== []) {
            VocabularyItem::query()->whereIn('id', $finalIds)->update(['ar_translation' => null]);
            TranslateVocabularyJob::dispatch($finalIds, null);
            $summary['translation_rows_queued'] = count($finalIds);
        }

        return $summary;
    }

    /**
     * @param  array<string,mixed>  $report
     * @return array{json:string,csv:string}
     */
    public function exportReport(array $report, string $exportBasePath): array
    {
        $basePath = trim($exportBasePath);
        if ($basePath === '') {
            $basePath = storage_path('app/vocab_repair/p4_repair_' . now()->format('Ymd_His'));
        }

        $jsonPath = str_ends_with(strtolower($basePath), '.json')
            ? $basePath
            : $basePath . '.json';

        $csvPath = preg_replace('/\.json$/i', '.csv', $jsonPath) ?: ($jsonPath . '.csv');

        $jsonDir = dirname($jsonPath);
        if (! is_dir($jsonDir)) {
            @mkdir($jsonDir, 0777, true);
        }

        file_put_contents(
            $jsonPath,
            json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $rows = $this->resolveRowsForExport($report);
        $this->writeCsv($csvPath, $rows);

        return [
            'json' => $jsonPath,
            'csv' => $csvPath,
        ];
    }

    /**
     * @return array{subject:string,period:string,weeks:array<int,string>,grades:array<int,string>}
     */
    private function normalizeScope(array $options): array
    {
        $subject = strtoupper(trim((string) ($options['subject'] ?? 'FR')));
        if ($subject === '') {
            $subject = 'FR';
        }

        $period = strtoupper(trim((string) ($options['period'] ?? 'P4')));
        if ($period === '') {
            $period = 'P4';
        }

        $weeksRaw = $options['weeks'] ?? ['SEM1', 'SEM2', 'SEM3', 'SEM4'];
        $weeks = [];
        foreach ((array) $weeksRaw as $week) {
            $weekCode = strtoupper(trim((string) $week));
            if ($weekCode !== '') {
                $weeks[] = $weekCode;
            }
        }
        if ($weeks === []) {
            $weeks = ['SEM1', 'SEM2', 'SEM3', 'SEM4'];
        }

        $gradesRaw = $options['grades'] ?? ['N1', 'N2', 'N3', 'N4', 'N5', 'N6'];
        $grades = [];
        foreach ((array) $gradesRaw as $grade) {
            $gradeCode = strtoupper(trim((string) $grade));
            if ($gradeCode !== '') {
                $grades[] = $gradeCode;
            }
        }
        if ($grades === []) {
            $grades = ['N1', 'N2', 'N3', 'N4', 'N5', 'N6'];
        }

        return [
            'subject' => $subject,
            'period' => $period,
            'weeks' => array_values(array_unique($weeks)),
            'grades' => array_values(array_unique($grades)),
        ];
    }

    /**
     * @return array{file_asset_id:int,full_path:string}|null
     */
    private function resolveLessonFileSource(string $lessonId): ?array
    {
        $fileAsset = FileAsset::query()
            ->whereIn('filename', [$lessonId . '.pptx', $lessonId . '.ppsx'])
            ->whereNotNull('local_path')
            ->orderByDesc('is_downloaded')
            ->orderByDesc('id')
            ->first();

        if (! $fileAsset instanceof FileAsset) {
            return null;
        }

        $localPath = trim((string) $fileAsset->local_path);
        if ($localPath === '') {
            return null;
        }

        $filesRoot = rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);
        $fullPath = $filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $localPath);

        if (! is_file($fullPath)) {
            return null;
        }

        return [
            'file_asset_id' => (int) $fileAsset->id,
            'full_path' => $fullPath,
        ];
    }

    /**
     * @param  array<int,string>  $flags
     * @return array<string,mixed>
     */
    private function buildRow(VocabularyItem $item, string $newWord, array $flags): array
    {
        $oldWord = (string) $item->word;

        return [
            'lesson_id' => (string) $item->lesson_id,
            'vocabulary_item_id' => (int) $item->id,
            'grade' => (string) $item->grade,
            'period' => (string) $item->period,
            'week' => (string) $item->week,
            'subject' => (string) $item->subject,
            'image_path' => (string) ($item->image_path ?? ''),
            'old_word' => $oldWord,
            'new_word' => $newWord,
            'changed' => $oldWord !== $newWord,
            'concept_id' => (string) ($item->concept_id ?? ''),
            'flashcard_id' => (string) ($item->flashcard_id ?? ''),
            'revizy_audio_file_id' => (string) ($item->revizy_audio_file_id ?? ''),
            'old_ar_translation' => (string) ($item->ar_translation ?? ''),
            'new_ar_translation' => (string) ($item->ar_translation ?? ''),
            'ambiguity_flags' => array_values(array_unique($flags)),
        ];
    }

    /**
     * @param  array<string,mixed>  $row
     */
    private function isActionable(array $row): bool
    {
        $changed = (bool) ($row['changed'] ?? false);
        if (! $changed) {
            return false;
        }

        return ! $this->hasAmbiguity($this->extractFlags($row));
    }

    /**
     * @param  array<int,string>  $flags
     */
    private function hasAmbiguity(array $flags): bool
    {
        foreach ($flags as $flag) {
            if (in_array($flag, [
                'multiple_image_candidates',
                'multiple_word_candidates',
                'no_source_match',
                'empty_new_word',
                'no_source_file',
                'empty_preview',
            ], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string,mixed>  $row
     * @return array<int,string>
     */
    private function extractFlags(array $row): array
    {
        $flags = $row['ambiguity_flags'] ?? [];
        if (is_string($flags)) {
            $parts = array_map('trim', explode('|', $flags));
            return array_values(array_filter($parts, static fn (string $value): bool => $value !== ''));
        }

        if (! is_array($flags)) {
            return [];
        }

        return array_values(array_filter(array_map('strval', $flags), static fn (string $value): bool => $value !== ''));
    }

    private function normalizeWord(string $word): string
    {
        $value = mb_strtolower(trim($word), 'UTF-8');
        $value = str_replace(['’', '`', '´'], "'", $value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    /**
     * @return array{0:VocabularyItem,1:VocabularyItem}
     */
    private function chooseKeeperAndLoser(VocabularyItem $left, VocabularyItem $right): array
    {
        $leftScore = $this->externalScore($left);
        $rightScore = $this->externalScore($right);

        if ($leftScore > $rightScore) {
            return [$left, $right];
        }
        if ($rightScore > $leftScore) {
            return [$right, $left];
        }

        $leftRich = $this->richnessScore($left);
        $rightRich = $this->richnessScore($right);
        if ($leftRich > $rightRich) {
            return [$left, $right];
        }
        if ($rightRich > $leftRich) {
            return [$right, $left];
        }

        return ((int) $left->id <= (int) $right->id)
            ? [$left, $right]
            : [$right, $left];
    }

    private function externalScore(VocabularyItem $item): int
    {
        $score = 0;
        foreach (['concept_id', 'flashcard_id', 'revizy_audio_file_id', 'revizy_image_file_id', 'walidio_image_id'] as $field) {
            if (trim((string) ($item->{$field} ?? '')) !== '') {
                $score++;
            }
        }

        return $score;
    }

    private function richnessScore(VocabularyItem $item): int
    {
        $score = 0;
        foreach (['audio_path', 'image_path', 'ar_translation', 'lexical_type', 'gender', 'distractor_group', 'distractor_subgroup'] as $field) {
            if (trim((string) ($item->{$field} ?? '')) !== '') {
                $score++;
            }
        }

        if ((int) ($item->revizy_skill_id ?? 0) > 0) {
            $score++;
        }
        if ((int) ($item->revizy_unite_id ?? 0) > 0) {
            $score++;
        }

        return $score;
    }

    private function mergeVocabularyRows(VocabularyItem $keeper, VocabularyItem $loser): void
    {
        $fillableWhenMissing = [
            'subject',
            'period',
            'week',
            'lesson_id',
            'image_path',
            'audio_path',
            'ar_translation',
            'lexical_type',
            'gender',
            'distractor_group',
            'distractor_subgroup',
            'revizy_image_file_id',
            'revizy_audio_file_id',
            'walidio_image_id',
            'flashcard_id',
            'concept_id',
        ];

        $dirty = false;
        foreach ($fillableWhenMissing as $field) {
            $current = trim((string) ($keeper->{$field} ?? ''));
            $incoming = trim((string) ($loser->{$field} ?? ''));
            if ($current === '' && $incoming !== '') {
                $keeper->{$field} = $loser->{$field};
                $dirty = true;
            }
        }

        if ((int) ($keeper->revizy_skill_id ?? 0) <= 0 && (int) ($loser->revizy_skill_id ?? 0) > 0) {
            $keeper->revizy_skill_id = (int) $loser->revizy_skill_id;
            $dirty = true;
        }

        if ((int) ($keeper->revizy_unite_id ?? 0) <= 0 && (int) ($loser->revizy_unite_id ?? 0) > 0) {
            $keeper->revizy_unite_id = (int) $loser->revizy_unite_id;
            $dirty = true;
        }

        if ($keeper->extracted_at === null && $loser->extracted_at !== null) {
            $keeper->extracted_at = $loser->extracted_at;
            $dirty = true;
        }

        if ($dirty) {
            $keeper->save();
        }
    }

    private function mergeAudioRows(VocabularyItem $keeper, VocabularyItem $loser): void
    {
        $keeperAudio = Audio::query()->where('vocabulary_item_id', (int) $keeper->id)->first();
        $loserAudio = Audio::query()->where('vocabulary_item_id', (int) $loser->id)->first();

        if (! $loserAudio instanceof Audio) {
            return;
        }

        if (! $keeperAudio instanceof Audio) {
            $loserAudio->vocabulary_item_id = (int) $keeper->id;
            $loserAudio->save();
            return;
        }

        if (trim((string) $keeperAudio->file_path) === '' && trim((string) $loserAudio->file_path) !== '') {
            $keeperAudio->file_path = (string) $loserAudio->file_path;
        }
        if (trim((string) $keeperAudio->text) === '' && trim((string) $loserAudio->text) !== '') {
            $keeperAudio->text = (string) $loserAudio->text;
        }

        $keeperAudio->save();
        $loserAudio->delete();
    }

    /**
     * @param  array<int,string>  $errors
     */
    private function pushError(array &$errors, string $message): void
    {
        Log::warning('raiida.vocab_repair.p4', ['message' => $message]);

        if (count($errors) >= 50) {
            if (($errors[49] ?? null) !== 'More errors omitted...') {
                $errors[49] = 'More errors omitted...';
            }

            return;
        }

        $errors[] = $message;
    }

    /**
     * @param  array<string,mixed>  $report
     * @return array<int,array<string,mixed>>
     */
    private function resolveRowsForExport(array $report): array
    {
        if (isset($report['rows']) && is_array($report['rows'])) {
            return $report['rows'];
        }

        if (isset($report['map']) && is_array($report['map']) && isset($report['map']['rows']) && is_array($report['map']['rows'])) {
            return $report['map']['rows'];
        }

        return [];
    }

    /**
     * @param  array<int,array<string,mixed>>  $rows
     */
    private function writeCsv(string $csvPath, array $rows): void
    {
        $directory = dirname($csvPath);
        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $handle = fopen($csvPath, 'w');
        if (! is_resource($handle)) {
            throw new \RuntimeException('Unable to write CSV report: ' . $csvPath);
        }

        try {
            fputcsv($handle, [
                'lesson_id',
                'vocabulary_item_id',
                'old_word',
                'new_word',
                'concept_id',
                'flashcard_id',
                'revizy_audio_file_id',
                'ambiguity_flags',
                'grade',
                'period',
                'week',
                'image_path',
                'old_ar_translation',
                'new_ar_translation',
            ]);

            foreach ($rows as $row) {
                $flags = $this->extractFlags($row);

                fputcsv($handle, [
                    (string) ($row['lesson_id'] ?? ''),
                    (int) ($row['vocabulary_item_id'] ?? 0),
                    (string) ($row['old_word'] ?? ''),
                    (string) ($row['new_word'] ?? ''),
                    (string) ($row['concept_id'] ?? ''),
                    (string) ($row['flashcard_id'] ?? ''),
                    (string) ($row['revizy_audio_file_id'] ?? ''),
                    implode('|', $flags),
                    (string) ($row['grade'] ?? ''),
                    (string) ($row['period'] ?? ''),
                    (string) ($row['week'] ?? ''),
                    (string) ($row['image_path'] ?? ''),
                    (string) ($row['old_ar_translation'] ?? ''),
                    (string) ($row['new_ar_translation'] ?? ''),
                ]);
            }
        } finally {
            fclose($handle);
        }
    }
}
