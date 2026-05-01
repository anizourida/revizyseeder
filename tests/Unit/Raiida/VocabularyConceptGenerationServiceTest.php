<?php

namespace Tests\Unit\Raiida;

use App\Models\Raiida\RevizyCurriculumMapping;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\VocabularyConceptGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VocabularyConceptGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('raiida.revizy.base_url', 'https://api.revizy.test');
        config()->set('raiida.revizy.api_key', 'test-revizy-key');
    }

    public function test_it_links_existing_concept_from_search_without_creating(): void
    {
        $item = $this->createVocabularyItem('parc', 'N5', 'P3', 'SEM4');
        $this->createMapping('FR', 'N5', 'P3', 530, 531);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => Http::response([
                'count' => 1,
                'data' => [
                    ['id' => 9001, 'name' => 'Parc'],
                ],
            ], 200),
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['targeted'] ?? 0));
        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($summary['created_total'] ?? 0));
        $this->assertSame(0, (int) ($summary['failed_total'] ?? 0));
        $this->assertSame('9001', (string) $item->concept_id);
        $this->assertSame(531, (int) $item->revizy_skill_id);
        $this->assertSame(530, (int) $item->revizy_unite_id);

        Http::assertNotSent(function ($request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'https://api.revizy.test/concepts';
        });
    }

    public function test_it_creates_concept_when_search_returns_no_result(): void
    {
        $item = $this->createVocabularyItem('parc', 'N5', 'P3', 'SEM4');
        $this->createMapping('FR', 'N5', 'P3', 530, 531);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => Http::response([
                'count' => 0,
                'data' => [],
            ], 200),
            'https://api.revizy.test/concepts' => Http::response([
                'data' => ['id' => 9100],
            ], 201),
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['created_total'] ?? 0));
        $this->assertSame(0, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($summary['failed_total'] ?? 0));
        $this->assertSame('9100', (string) $item->concept_id);

        Http::assertSent(function ($request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'https://api.revizy.test/concepts'
                && (int) ($request['skill_id'] ?? 0) === 531
                && (int) ($request['unite_id'] ?? 0) === 530
                && (int) ($request['week'] ?? -1) === 4;
        });
    }

    public function test_it_picks_first_match_when_multiple_candidates_exist_without_exact_name(): void
    {
        $item = $this->createVocabularyItem('parc', 'N5', 'P3', 'SEM4');
        $this->createMapping('FR', 'N5', 'P3', 530, 531);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => Http::response([
                'count' => 2,
                'data' => [
                    ['id' => 9200, 'name' => 'parc vert'],
                    ['id' => 9201, 'name' => 'grand parc'],
                ],
            ], 200),
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame('9200', (string) $item->concept_id);
    }

    public function test_it_auto_syncs_mapping_when_missing_then_links_found_concept(): void
    {
        $item = $this->createVocabularyItem('parc', 'N5', 'P3', 'SEM4');

        Http::fake([
            'https://api.revizy.test/grades/code/N5' => Http::response([
                'id' => 5,
                'name' => 'Niveau 5',
                'code' => 'N5',
            ], 200),
            'https://api.revizy.test/subjects/code/FR_N5' => Http::response([
                'id' => 15,
                'name' => 'Francais',
                'code' => 'FR_N5',
            ], 200),
            'https://api.revizy.test/unites/code/FR_N5_P3' => Http::response([
                'id' => 153,
                'name' => 'Periode 3',
                'code' => 'FR_N5_P3',
                'index' => 3,
            ], 200),
            'https://api.revizy.test/skills/code/FR_N5_VOC' => Http::response([
                'id' => 1513,
                'name' => 'Le vocabulaire',
                'code' => 'FR_N5_VOC',
            ], 200),
            'https://api.revizy.test/concepts/search*' => Http::response([
                'count' => 1,
                'data' => [
                    ['id' => 9300, 'name' => 'parc'],
                ],
            ], 200),
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['mapping_synced_total'] ?? 0));
        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($summary['created_total'] ?? 0));
        $this->assertSame('9300', (string) $item->concept_id);

        $this->assertDatabaseHas('revizy_curriculum_mappings', [
            'subject_code' => 'FR',
            'grade_code' => 'N5',
            'period_code' => 'P3',
            'revizy_unite_id' => 153,
            'revizy_vocab_skill_id' => 1513,
        ]);
    }

    public function test_it_normalizes_subject_name_to_subject_code_for_lookup_scope(): void
    {
        $item = VocabularyItem::query()->create([
            'word' => 'parc',
            'grade' => 'N5',
            'subject' => 'Français',
            'period' => 'P3',
            'week' => 'SEM4',
            'lesson_id' => 'FR_N5_P3_SEM4_S2',
            'ar_translation' => 'حديقة',
        ]);

        $this->createMapping('FR', 'N5', 'P3', 530, 531);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => Http::response([
                'count' => 1,
                'data' => [
                    ['id' => 9400, 'name' => 'parc'],
                ],
            ], 200),
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame('9400', (string) $item->concept_id);
    }

    public function test_it_falls_back_to_period_scope_search_when_week_scoped_search_misses(): void
    {
        $item = $this->createVocabularyItem('parc', 'N5', 'P3', 'SEM4');
        $this->createMapping('FR', 'N5', 'P3', 530, 531);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => function ($request) {
                $url = $request->url();
                if (str_contains($url, 'code_prefix=FR_N5_P3_SEM4')) {
                    return Http::response([
                        'count' => 0,
                        'data' => [],
                    ], 200);
                }

                if (str_contains($url, 'code_prefix=FR_N5_P3')) {
                    return Http::response([
                        'count' => 1,
                        'data' => [
                            ['id' => 9500, 'name' => 'parc'],
                        ],
                    ], 200);
                }

                return Http::response(['count' => 0, 'data' => []], 200);
            },
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame('9500', (string) $item->concept_id);
        $this->assertSame(0, (int) ($summary['created_total'] ?? 0));
    }

    public function test_it_links_existing_concept_when_search_payload_is_paginated(): void
    {
        $item = $this->createVocabularyItem('parc', 'N5', 'P3', 'SEM4');
        $this->createMapping('FR', 'N5', 'P3', 530, 531);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => Http::response([
                'data' => [
                    'data' => [
                        ['id' => 9600, 'name' => 'Parc'],
                    ],
                    'links' => [],
                    'meta' => ['total' => 1],
                ],
            ], 200),
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($summary['created_total'] ?? 0));
        $this->assertSame('9600', (string) $item->concept_id);

        Http::assertNotSent(function ($request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'https://api.revizy.test/concepts';
        });
    }

    public function test_it_prefers_strict_prefix_without_name_to_match_apostrophe_variants(): void
    {
        $item = $this->createVocabularyItem("L'épicerie", 'N6', 'P1', 'SEM1');
        $this->createMapping('FR', 'N6', 'P1', 23, 9);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => function ($request) {
                $url = $request->url();

                if (str_contains($url, 'code_prefix=FR_N6_P1_SEM1') && ! str_contains($url, 'name=')) {
                    return Http::response([
                        'count' => 1,
                        'data' => [
                            ['id' => 496, 'name' => 'L’épicerie'],
                        ],
                    ], 200);
                }

                return Http::response(['count' => 0, 'data' => []], 200);
            },
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($summary['created_total'] ?? 0));
        $this->assertSame('496', (string) $item->concept_id);

        Http::assertNotSent(function ($request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'https://api.revizy.test/concepts';
        });
    }

    public function test_it_uses_period_fallback_name_variants_when_exact_apostrophe_search_misses(): void
    {
        $item = $this->createVocabularyItem("L'épicerie", 'N6', 'P1', 'SEM1');
        $this->createMapping('FR', 'N6', 'P1', 23, 9);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => function ($request) {
                $url = $request->url();

                if (str_contains($url, 'code_prefix=FR_N6_P1_SEM1') && ! str_contains($url, 'name=')) {
                    return Http::response(['count' => 0, 'data' => []], 200);
                }

                if (str_contains($url, 'code_prefix=FR_N6_P1') && str_contains($url, 'name=epicerie')) {
                    return Http::response([
                        'count' => 1,
                        'data' => [
                            ['id' => 496, 'name' => 'L’épicerie'],
                        ],
                    ], 200);
                }

                return Http::response(['count' => 0, 'data' => []], 200);
            },
        ]);

        $summary = app(VocabularyConceptGenerationService::class)->generateBatch([
            'limit' => 10,
            'wait_ms' => 0,
        ]);

        $item->refresh();

        $this->assertSame(1, (int) ($summary['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($summary['created_total'] ?? 0));
        $this->assertSame('496', (string) $item->concept_id);
    }

    private function createVocabularyItem(string $word, string $grade, string $period, string $week): VocabularyItem
    {
        return VocabularyItem::query()->create([
            'word' => $word,
            'grade' => $grade,
            'subject' => 'FR',
            'period' => $period,
            'week' => $week,
            'lesson_id' => "FR_{$grade}_{$period}_{$week}_S1",
            'ar_translation' => 'حديقة',
        ]);
    }

    private function createMapping(string $subjectCode, string $gradeCode, string $periodCode, int $uniteId, int $skillId): void
    {
        RevizyCurriculumMapping::query()->create([
            'subject_code' => $subjectCode,
            'grade_code' => $gradeCode,
            'period_code' => $periodCode,
            'grade_index' => (int) substr($gradeCode, 1),
            'period_index' => (int) substr($periodCode, 1),
            'revizy_unite_id' => $uniteId,
            'revizy_vocab_skill_id' => $skillId,
        ]);
    }
}
