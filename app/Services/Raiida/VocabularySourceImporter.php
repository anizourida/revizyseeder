<?php

namespace App\Services\Raiida;

use App\Models\Raiida\VocabularyItem;
use Illuminate\Support\Facades\Log;
use PDO;
use Throwable;

class VocabularySourceImporter
{
    /**
     * Import vocabulary items from the Python source SQLite database (raiida.db)
     * into the Laravel vocabulary_items table.
     *
     * @return array<string, int>
     */
    public function importFromSource(): array
    {
        $sourcePath = (string) config('raiida.source_sqlite_path');

        if ($sourcePath === '' || ! is_file($sourcePath)) {
            Log::warning('VocabularySourceImporter: source SQLite not found', [
                'path' => $sourcePath,
            ]);

            return [
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => 0,
                'total' => 0,
            ];
        }

        $pdo = new PDO('sqlite:' . $sourcePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $stmt = $pdo->query('SELECT * FROM vocabularyitem ORDER BY id ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $summary = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'total' => count($rows),
        ];

        foreach ($rows as $row) {
            try {
                $word = trim((string) ($row['word'] ?? ''));
                $lessonId = trim((string) ($row['lesson_id'] ?? ''));
                $grade = trim((string) ($row['grade'] ?? ''));

                if ($word === '' || $lessonId === '' || $grade === '') {
                    $summary['skipped']++;

                    continue;
                }

                $existing = VocabularyItem::query()
                    ->where('word', $word)
                    ->where('lesson_id', $lessonId)
                    ->where('grade', $grade)
                    ->first();

                $attributes = [
                    'image_path' => $this->nullIfEmpty($row['image_path'] ?? null),
                    'audio_path' => $this->nullIfEmpty($row['audio_path'] ?? null),
                    'subject' => trim((string) ($row['subject'] ?? 'FR')) ?: 'FR',
                    'period' => trim((string) ($row['period'] ?? '')),
                    'week' => trim((string) ($row['week'] ?? '')),
                    'ar_translation' => $this->nullIfEmpty($row['ar_translation'] ?? null),
                    'lexical_type' => $this->nullIfEmpty($row['lexical_type'] ?? null),
                    'gender' => $this->nullIfEmpty($row['gender'] ?? null),
                    'distractor_group' => $this->nullIfEmpty($row['distractor_group'] ?? null),
                    'distractor_subgroup' => $this->nullIfEmpty($row['distractor_subgroup'] ?? null),
                    'revizy_image_file_id' => $this->nullIfEmpty($row['revizy_image_file_id'] ?? null),
                    'revizy_audio_file_id' => $this->nullIfEmpty($row['revizy_audio_file_id'] ?? null),
                    'walidio_image_id' => $this->nullIfEmpty($row['walidio_image_id'] ?? null),
                    'flashcard_id' => $this->nullIfEmpty($row['flashcard_id'] ?? null),
                    'concept_id' => $this->nullIfEmpty($row['concept_id'] ?? null),
                    'revizy_skill_id' => $this->nullableInt($row['revizy_skill_id'] ?? null),
                    'revizy_unite_id' => $this->nullableInt($row['revizy_unite_id'] ?? null),
                    'extracted_at' => $this->nullIfEmpty($row['extracted_at'] ?? null),
                ];

                if ($existing) {
                    $existing->update($attributes);
                    $summary['updated']++;
                } else {
                    VocabularyItem::query()->create(array_merge([
                        'word' => $word,
                        'lesson_id' => $lessonId,
                        'grade' => $grade,
                    ], $attributes));
                    $summary['imported']++;
                }
            } catch (Throwable $e) {
                $summary['errors']++;
                Log::warning('VocabularySourceImporter: row import failed', [
                    'source_id' => $row['id'] ?? null,
                    'word' => $row['word'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('VocabularySourceImporter: import completed', $summary);

        return $summary;
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed !== '' ? $trimmed : null;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
