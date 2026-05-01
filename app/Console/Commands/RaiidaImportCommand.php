<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class RaiidaImportCommand extends Command
{
    protected $signature = 'raiida:import {--source= : Path to source raiida.db}';

    protected $description = 'Import Raiida data from legacy SQLite source into Laravel tables';

    public function handle(): int
    {
        $this->call('app:db-backup');
        $sourcePath = $this->option('source') ?: config('raiida.source_sqlite_path');

        if (! is_string($sourcePath) || ! file_exists($sourcePath)) {
            $this->error("Source DB file not found: {$sourcePath}");

            return self::FAILURE;
        }

        $this->info("Starting import from {$sourcePath}");

        try {
            $source = new PDO('sqlite:' . $sourcePath);
            $source->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $e) {
            $this->error('Could not connect to source SQLite DB: ' . $e->getMessage());

            return self::FAILURE;
        }

        $importers = [
            'grade' => fn (array $rows) => $this->importGrades($rows),
            'subject' => fn (array $rows) => $this->importSubjects($rows),
            'period' => fn (array $rows) => $this->importPeriods($rows),
            'week' => fn (array $rows) => $this->importWeeks($rows),
            'fileasset' => fn (array $rows) => $this->importFileAssets($rows),
            'vocabularyitem' => fn (array $rows) => $this->importVocabularyItems($rows),
            'audio' => fn (array $rows) => $this->importAudios($rows),
            'conjugaison' => fn (array $rows) => $this->importConjugaisons($rows),
            'grammaire' => fn (array $rows) => $this->importGrammaires($rows),
            'questionpublishattempt' => fn (array $rows) => $this->importQuestionAttempts($rows),
        ];

        foreach ($importers as $sourceTable => $importer) {
            $rows = $this->fetchRows($source, $sourceTable);
            $this->line("Importing {$sourceTable}: " . count($rows) . ' rows');

            if ($rows === []) {
                continue;
            }

            DB::transaction(function () use ($importer, $rows): void {
                $importer($rows);
            });
        }

        $this->info('Raiida import completed.');

        return self::SUCCESS;
    }

    private function fetchRows(PDO $source, string $table): array
    {
        try {
            $statement = $source->query("SELECT * FROM {$table}");
            $rows = $statement ? $statement->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Throwable) {
            $this->warn("Source table missing or unreadable: {$table}");
            return [];
        }

        return is_array($rows) ? $rows : [];
    }

    private function importGrades(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            $name = (string) ($row['name'] ?? '');
            $trimmed = trim($name);

            return [
                'id' => (int) ($row['id'] ?? 0),
                'code' => $this->normalizeGradeCode($trimmed),
                'name' => $trimmed,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks('grades', $payload, ['id'], ['code', 'name', 'updated_at']);
    }

    private function importSubjects(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            $name = trim((string) ($row['name'] ?? ''));

            return [
                'id' => (int) ($row['id'] ?? 0),
                'grade_id' => (int) ($row['grade_id'] ?? 0),
                'code' => $name,
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks('subjects', $payload, ['id'], ['grade_id', 'code', 'name', 'updated_at']);
    }

    private function importPeriods(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            $name = trim((string) ($row['name'] ?? ''));

            return [
                'id' => (int) ($row['id'] ?? 0),
                'subject_id' => (int) ($row['subject_id'] ?? 0),
                'code' => $name,
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks('periods', $payload, ['id'], ['subject_id', 'code', 'name', 'updated_at']);
    }

    private function importWeeks(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            $name = trim((string) ($row['name'] ?? ''));

            return [
                'id' => (int) ($row['id'] ?? 0),
                'period_id' => (int) ($row['period_id'] ?? 0),
                'code' => $name,
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks('weeks', $payload, ['id'], ['period_id', 'code', 'name', 'updated_at']);
    }

    private function importFileAssets(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'week_id' => $this->nullableInt($row['week_id'] ?? null),
                'filename' => (string) ($row['filename'] ?? ''),
                'local_path' => $this->nullableString($row['local_path'] ?? null),
                'original_url' => $this->nullableString($row['original_url'] ?? null),
                'size_bytes' => (int) ($row['size_bytes'] ?? 0),
                'is_downloaded' => $this->toBool($row['is_downloaded'] ?? false),
                'is_integrity_checked' => $this->toBool($row['is_integrity_checked'] ?? false),
                'is_corrupt' => $this->toBool($row['is_corrupt'] ?? false),
                'is_vocab_extracted' => $this->toBool($row['is_vocab_extracted'] ?? false),
                'download_state' => $this->toBool($row['is_downloaded'] ?? false) ? 'downloaded' : 'pending',
                'download_progress' => $this->toBool($row['is_downloaded'] ?? false) ? 100 : 0,
                'download_error' => null,
                'download_started_at' => null,
                'download_finished_at' => $this->toBool($row['is_downloaded'] ?? false)
                    ? ($this->normalizeDate($row['downloaded_at'] ?? null) ?: $now)
                    : null,
                'session_id' => $this->nullableString($row['session_id'] ?? null),
                'vocab_count' => (int) ($row['vocab_count'] ?? 0),
                'downloaded_at' => $this->normalizeDate($row['downloaded_at'] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks(
            'file_assets',
            $payload,
            ['id'],
            [
                'week_id',
                'filename',
                'local_path',
                'original_url',
                'size_bytes',
                'is_downloaded',
                'is_integrity_checked',
                'is_corrupt',
                'is_vocab_extracted',
                'download_state',
                'download_progress',
                'download_error',
                'download_started_at',
                'download_finished_at',
                'session_id',
                'vocab_count',
                'downloaded_at',
                'updated_at',
            ]
        );
    }

    private function importVocabularyItems(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'word' => (string) ($row['word'] ?? ''),
                'image_path' => $this->nullableString($row['image_path'] ?? null),
                'audio_path' => $this->nullableString($row['audio_path'] ?? null),
                'grade' => (string) ($row['grade'] ?? ''),
                'subject' => (string) ($row['subject'] ?? 'FR'),
                'period' => (string) ($row['period'] ?? ''),
                'week' => (string) ($row['week'] ?? ''),
                'lesson_id' => (string) ($row['lesson_id'] ?? ''),
                'ar_translation' => $this->nullableString($row['ar_translation'] ?? null),
                'lexical_type' => $this->nullableString($row['lexical_type'] ?? null),
                'gender' => $this->nullableString($row['gender'] ?? null),
                'distractor_group' => $this->nullableString($row['distractor_group'] ?? null),
                'distractor_subgroup' => $this->nullableString($row['distractor_subgroup'] ?? null),
                'revizy_image_file_id' => $this->nullableString($row['revizy_image_file_id'] ?? null),
                'revizy_audio_file_id' => $this->nullableString($row['revizy_audio_file_id'] ?? null),
                'walidio_image_id' => $this->nullableString($row['walidio_image_id'] ?? null),
                'flashcard_id' => $this->nullableString($row['flashcard_id'] ?? null),
                'concept_id' => $this->nullableString($row['concept_id'] ?? null),
                'revizy_skill_id' => $this->nullableInt($row['revizy_skill_id'] ?? null),
                'revizy_unite_id' => $this->nullableInt($row['revizy_unite_id'] ?? null),
                'extracted_at' => $this->normalizeDate($row['extracted_at'] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks(
            'vocabulary_items',
            $payload,
            ['id'],
            [
                'word',
                'image_path',
                'audio_path',
                'grade',
                'subject',
                'period',
                'week',
                'lesson_id',
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
                'revizy_skill_id',
                'revizy_unite_id',
                'extracted_at',
                'updated_at',
            ]
        );
    }

    private function importAudios(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'vocabulary_item_id' => (int) ($row['vocabulary_id'] ?? 0),
                'text' => (string) ($row['text'] ?? ''),
                'file_path' => (string) ($row['file_path'] ?? ''),
                'created_at' => $this->normalizeDate($row['created_at'] ?? null) ?: $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks(
            'audios',
            $payload,
            ['id'],
            ['vocabulary_item_id', 'text', 'file_path', 'created_at', 'updated_at']
        );
    }

    private function importConjugaisons(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'n' => (string) ($row['n'] ?? ''),
                'p' => (string) ($row['p'] ?? ''),
                'sem' => (string) ($row['sem'] ?? ''),
                'verbe' => $this->nullableString($row['verbe'] ?? null),
                'tense' => $this->nullableString($row['tense'] ?? null),
                'raw_data' => (string) ($row['raw_data'] ?? ''),
                'concept_id' => $this->nullableString($row['concept_id'] ?? null),
                'week' => $this->nullableInt($row['week'] ?? null),
                'revizy_skill_id' => $this->nullableInt($row['revizy_skill_id'] ?? null),
                'revizy_unite_id' => $this->nullableInt($row['revizy_unite_id'] ?? null),
                'extracted_at' => $this->normalizeDate($row['extracted_at'] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks(
            'conjugaisons',
            $payload,
            ['id'],
            [
                'n',
                'p',
                'sem',
                'verbe',
                'tense',
                'raw_data',
                'concept_id',
                'week',
                'revizy_skill_id',
                'revizy_unite_id',
                'extracted_at',
                'updated_at',
            ]
        );
    }

    private function importGrammaires(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'n' => (string) ($row['n'] ?? ''),
                'p' => (string) ($row['p'] ?? ''),
                'sem' => (string) ($row['sem'] ?? ''),
                'objectif' => $this->nullableString($row['objectif'] ?? null),
                'lesson_title' => $this->nullableString($row['lesson_title'] ?? null),
                'raw_data' => (string) ($row['raw_data'] ?? ''),
                'extracted_at' => $this->normalizeDate($row['extracted_at'] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks(
            'grammaires',
            $payload,
            ['id'],
            ['n', 'p', 'sem', 'objectif', 'lesson_title', 'raw_data', 'extracted_at', 'updated_at']
        );
    }

    private function importQuestionAttempts(array $rows): void
    {
        $now = now()->toDateTimeString();

        $payload = array_map(function (array $row) use ($now): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'local_question_id' => (int) ($row['local_question_id'] ?? 0),
                'concept_id' => (string) ($row['concept_id'] ?? ''),
                'name' => (string) ($row['name'] ?? ''),
                'question_data' => (string) ($row['question_data'] ?? '{}'),
                'status' => (string) ($row['status'] ?? 'pending'),
                'revizy_question_id' => $this->nullableString($row['revizy_question_id'] ?? null),
                'error_message' => $this->nullableString($row['error_message'] ?? null),
                'published_at' => $this->normalizeDate($row['published_at'] ?? null),
                'unaccepted_at' => $this->normalizeDate($row['unaccepted_at'] ?? null),
                'failed_at' => $this->normalizeDate($row['failed_at'] ?? null),
                'created_at' => $this->normalizeDate($row['created_at'] ?? null) ?: $now,
                'updated_at' => $now,
            ];
        }, $rows);

        $this->upsertInChunks(
            'question_publish_attempts',
            $payload,
            ['id'],
            [
                'local_question_id',
                'concept_id',
                'name',
                'question_data',
                'status',
                'revizy_question_id',
                'error_message',
                'published_at',
                'unaccepted_at',
                'failed_at',
                'created_at',
                'updated_at',
            ]
        );
    }

    private function upsertInChunks(
        string $table,
        array $payload,
        array $uniqueBy,
        array $updateColumns,
        int $chunkSize = 500
    ): void {
        foreach (array_chunk($payload, $chunkSize) as $chunk) {
            DB::table($table)->upsert($chunk, $uniqueBy, $updateColumns);
        }
    }

    private function normalizeGradeCode(string $name): string
    {
        $upper = strtoupper($name);
        if (str_starts_with($upper, 'N')) {
            return $upper;
        }

        if (is_numeric($name)) {
            return 'N' . trim($name);
        }

        return $upper;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->toDateTimeString();
        } catch (Throwable) {
            return null;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return in_array(strtolower((string) $value), ['true', 'yes', 'on'], true);
    }
}
