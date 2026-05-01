<?php

namespace App\Services\Raiida;

use App\Models\Raiida\RevizyCurriculumMapping;
use Illuminate\Support\Arr;

class RevizyCurriculumMappingImportService
{
    /**
     * @param  array<string,mixed>  $options
     * @return array<string,mixed>
     */
    public function importFromJsonFiles(string $skillsPath, string $unitesPath, array $options = []): array
    {
        $skills = $this->readJsonList($skillsPath);
        $unites = $this->readJsonList($unitesPath);

        return $this->importFromArrays($skills, $unites, $options);
    }

    /**
     * @param  array<int,array<string,mixed>>  $skills
     * @param  array<int,array<string,mixed>>  $unites
     * @param  array<string,mixed>  $options
     * @return array<string,mixed>
     */
    public function importFromArrays(array $skills, array $unites, array $options = []): array
    {
        $subjectCode = strtoupper(trim((string) ($options['subject_code'] ?? 'FR'))) ?: 'FR';

        $vocabSkillByGrade = [];
        $conjugaisonSkillByGrade = [];

        foreach ($skills as $row) {
            $gradeIndex = (int) ($row['grade_index'] ?? 0);
            $skillId = (int) ($row['skill_id'] ?? 0);
            $skillName = trim((string) ($row['skill_name'] ?? ''));
            if ($gradeIndex <= 0 || $skillId <= 0 || $skillName === '') {
                continue;
            }

            if (strcasecmp($skillName, 'Le vocabulaire') === 0) {
                $vocabSkillByGrade[$gradeIndex] = [
                    'id' => $skillId,
                    'name' => $skillName,
                ];
            }

            if (strcasecmp($skillName, 'Conjugaison') === 0) {
                $conjugaisonSkillByGrade[$gradeIndex] = [
                    'id' => $skillId,
                    'name' => $skillName,
                ];
            }
        }

        $summary = [
            'subject_code' => $subjectCode,
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($unites as $row) {
            $gradeIndex = (int) ($row['grade_index'] ?? 0);
            $periodIndex = $this->toPositiveInt($row['unite_index'] ?? null);
            $uniteId = (int) ($row['unite_id'] ?? 0);

            if ($gradeIndex <= 0 || $periodIndex <= 0 || $uniteId <= 0) {
                $summary['skipped']++;
                continue;
            }

            $summary['processed']++;

            $gradeCode = 'N' . $gradeIndex;
            $periodCode = 'P' . $periodIndex;

            $payload = [
                'subject_code' => $subjectCode,
                'grade_code' => $gradeCode,
                'grade_index' => $gradeIndex,
                'period_code' => $periodCode,
                'period_index' => $periodIndex,
                'revizy_grade_id' => $this->toPositiveInt($row['grade_id'] ?? null),
                'revizy_grade_name' => $this->nullIfEmpty($row['grade_name'] ?? null),
                'revizy_subject_id' => $this->toPositiveInt($row['subject_id'] ?? null),
                'revizy_subject_name' => $this->nullIfEmpty($row['subject_name'] ?? null),
                'revizy_unite_id' => $uniteId,
                'revizy_unite_name' => $this->nullIfEmpty($row['unite_name'] ?? null),
                'revizy_unite_code' => $this->nullIfEmpty($row['unite_code'] ?? null),
                'revizy_unite_index' => $this->nullIfEmpty($row['unite_index'] ?? null),
                'revizy_vocab_skill_id' => Arr::get($vocabSkillByGrade, $gradeIndex . '.id'),
                'revizy_vocab_skill_name' => Arr::get($vocabSkillByGrade, $gradeIndex . '.name'),
                'revizy_conjugaison_skill_id' => Arr::get($conjugaisonSkillByGrade, $gradeIndex . '.id'),
                'revizy_conjugaison_skill_name' => Arr::get($conjugaisonSkillByGrade, $gradeIndex . '.name'),
                'meta' => [
                    'source' => 'json',
                ],
            ];

            try {
                $mapping = RevizyCurriculumMapping::query()->firstOrNew([
                    'subject_code' => $subjectCode,
                    'grade_code' => $gradeCode,
                    'period_code' => $periodCode,
                ]);

                $wasExisting = $mapping->exists;
                $mapping->fill($payload);
                $mapping->save();

                if ($wasExisting) {
                    $summary['updated']++;
                } else {
                    $summary['created']++;
                }
            } catch (\Throwable $exception) {
                $summary['errors'][] = "{$gradeCode}/{$periodCode}: " . $exception->getMessage();
            }
        }

        return $summary;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function readJsonList(string $path): array
    {
        $contents = @file_get_contents($path);
        if (! is_string($contents) || trim($contents) === '') {
            return [];
        }

        $decoded = json_decode($contents, true);
        if (! is_array($decoded) || ! array_is_list($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn ($row): bool => is_array($row)));
    }

    private function toPositiveInt(mixed $value): ?int
    {
        $int = (int) $value;
        if ($int <= 0) {
            return null;
        }

        return $int;
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}

