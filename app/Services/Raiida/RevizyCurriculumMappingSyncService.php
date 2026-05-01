<?php

namespace App\Services\Raiida;

use App\Models\Raiida\RevizyCurriculumMapping;
use App\Services\Raiida\External\RevizySystemClient;
use RuntimeException;

class RevizyCurriculumMappingSyncService
{
    public function __construct(
        private readonly RevizySystemClient $revizy
    ) {
    }

    /**
     * @return array{synced:bool,mapping:\App\Models\Raiida\RevizyCurriculumMapping}
     */
    public function syncScope(string $subjectCode, string $gradeCode, string $periodCode): array
    {
        $subjectCode = $this->normalizeSubjectCode($subjectCode);
        $gradeCode = $this->normalizeGradeCode($gradeCode);
        $periodCode = $this->normalizePeriodCode($periodCode);

        $gradeIndex = $this->extractIndex($gradeCode, 'N');
        $periodIndex = $this->extractIndex($periodCode, 'P');

        $subjectRevizyCode = $subjectCode . '_' . $gradeCode;
        $uniteRevizyCode = $subjectRevizyCode . '_' . $periodCode;
        $vocabSkillRevizyCode = $subjectRevizyCode . '_VOC';
        $conjSkillRevizyCode = $subjectRevizyCode . '_CON';

        $grade = $this->revizy->get('/grades/code/' . rawurlencode($gradeCode));
        $subject = $this->revizy->get('/subjects/code/' . rawurlencode($subjectRevizyCode));
        $unite = $this->revizy->get('/unites/code/' . rawurlencode($uniteRevizyCode));
        $vocabSkill = $this->revizy->get('/skills/code/' . rawurlencode($vocabSkillRevizyCode));
        $conjSkill = $this->revizy->get('/skills/code/' . rawurlencode($conjSkillRevizyCode));

        $gradeId = $this->toPositiveInt($grade['id'] ?? null);
        $subjectId = $this->toPositiveInt($subject['id'] ?? null);
        $uniteId = $this->toPositiveInt($unite['id'] ?? null);
        $vocabSkillId = $this->toPositiveInt($vocabSkill['id'] ?? null);
        $conjSkillId = $this->toPositiveInt($conjSkill['id'] ?? null);

        if ($gradeId === null || $subjectId === null || $uniteId === null || $vocabSkillId === null || $conjSkillId === null) {
            throw new RuntimeException("Revizy taxonomy lookup did not return required IDs (Vocabulary or Conjugaison) for {$subjectCode}/{$gradeCode}/{$periodCode}.");
        }

        $mapping = RevizyCurriculumMapping::query()->firstOrNew([
            'subject_code' => $subjectCode,
            'grade_code' => $gradeCode,
            'period_code' => $periodCode,
        ]);

        $mapping->fill([
            'grade_index' => $gradeIndex,
            'period_index' => $periodIndex,
            'revizy_grade_id' => $gradeId,
            'revizy_grade_name' => $this->toNullableString($grade['name'] ?? null),
            'revizy_subject_id' => $subjectId,
            'revizy_subject_name' => $this->toNullableString($subject['name'] ?? null),
            'revizy_unite_id' => $uniteId,
            'revizy_unite_name' => $this->toNullableString($unite['name'] ?? null),
            'revizy_unite_code' => $this->toNullableString($unite['code'] ?? null),
            'revizy_unite_index' => $this->toNullableString($unite['index'] ?? null),
            'revizy_vocab_skill_id' => $vocabSkillId,
            'revizy_vocab_skill_name' => $this->toNullableString($vocabSkill['name'] ?? null),
            'revizy_conjugaison_skill_id' => $conjSkillId,
            'revizy_conjugaison_skill_name' => $this->toNullableString($conjSkill['name'] ?? null),
            'meta' => [
                'source' => 'auto_sync_by_code',
                'synced_at' => now()->toIso8601String(),
                'codes' => [
                    'grade' => $gradeCode,
                    'subject' => $subjectRevizyCode,
                    'unite' => $uniteRevizyCode,
                    'vocab_skill' => $vocabSkillRevizyCode,
                    'conj_skill' => $conjSkillRevizyCode,
                ],
            ],
        ]);
        $mapping->save();

        return [
            'synced' => true,
            'mapping' => $mapping,
        ];
    }

    private function normalizeSubjectCode(string $subjectCode): string
    {
        $normalized = strtoupper(trim($subjectCode));

        return $normalized !== '' ? $normalized : 'FR';
    }

    private function normalizeGradeCode(string $gradeCode): string
    {
        $normalized = strtoupper(trim($gradeCode));
        if (preg_match('/^N[1-6]$/', $normalized) === 1) {
            return $normalized;
        }

        throw new RuntimeException("Invalid grade code [{$gradeCode}]. Expected N1..N6.");
    }

    private function normalizePeriodCode(string $periodCode): string
    {
        $normalized = strtoupper(trim($periodCode));
        if (preg_match('/^P[1-9][0-9]*$/', $normalized) === 1) {
            return $normalized;
        }

        throw new RuntimeException("Invalid period code [{$periodCode}]. Expected P1..Pn.");
    }

    private function extractIndex(string $code, string $prefix): int
    {
        $index = (int) substr($code, strlen($prefix));
        if ($index <= 0) {
            throw new RuntimeException("Unable to parse index from code [{$code}].");
        }

        return $index;
    }

    private function toPositiveInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? $int : null;
    }

    private function toNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }
}
