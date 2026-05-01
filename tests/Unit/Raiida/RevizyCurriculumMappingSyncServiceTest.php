<?php

namespace Tests\Unit\Raiida;

use App\Models\Raiida\RevizyCurriculumMapping;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\RevizyCurriculumMappingSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RevizyCurriculumMappingSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('raiida.revizy.base_url', 'https://api.revizy.test');
        config()->set('raiida.revizy.api_key', 'test-revizy-key');
    }

    public function test_it_syncs_mapping_for_scope_by_taxonomy_codes(): void
    {
        Http::fake([
            'https://api.revizy.test/grades/code/N4' => Http::response([
                'id' => 4,
                'name' => 'Niveau 4',
                'code' => 'N4',
            ], 200),
            'https://api.revizy.test/subjects/code/FR_N4' => Http::response([
                'id' => 14,
                'name' => 'Francais',
                'code' => 'FR_N4',
            ], 200),
            'https://api.revizy.test/unites/code/FR_N4_P2' => Http::response([
                'id' => 142,
                'name' => 'Periode 2',
                'code' => 'FR_N4_P2',
                'index' => 2,
            ], 200),
            'https://api.revizy.test/skills/code/FR_N4_VOC' => Http::response([
                'id' => 1412,
                'name' => 'Le vocabulaire',
                'code' => 'FR_N4_VOC',
            ], 200),
        ]);

        $service = app(RevizyCurriculumMappingSyncService::class);
        $result = $service->syncScope('FR', 'N4', 'P2');

        $this->assertTrue((bool) ($result['synced'] ?? false));
        $this->assertDatabaseHas('revizy_curriculum_mappings', [
            'subject_code' => 'FR',
            'grade_code' => 'N4',
            'period_code' => 'P2',
            'grade_index' => 4,
            'period_index' => 2,
            'revizy_grade_id' => 4,
            'revizy_subject_id' => 14,
            'revizy_unite_id' => 142,
            'revizy_vocab_skill_id' => 1412,
            'revizy_unite_code' => 'FR_N4_P2',
        ]);
    }

    public function test_it_throws_and_does_not_upsert_when_lookup_fails(): void
    {
        Http::fake([
            'https://api.revizy.test/grades/code/N4' => Http::response(['id' => 4, 'name' => 'Niveau 4', 'code' => 'N4'], 200),
            'https://api.revizy.test/subjects/code/FR_N4' => Http::response(['id' => 14, 'name' => 'Francais', 'code' => 'FR_N4'], 200),
            'https://api.revizy.test/unites/code/FR_N4_P2' => Http::response(['id' => 142, 'name' => 'Periode 2', 'code' => 'FR_N4_P2', 'index' => 2], 200),
            'https://api.revizy.test/skills/code/FR_N4_VOC' => Http::response(['message' => 'Not Found'], 404),
        ]);

        $service = app(RevizyCurriculumMappingSyncService::class);

        try {
            $service->syncScope('FR', 'N4', 'P2');
            $this->fail('Expected RaiidaApiException was not thrown.');
        } catch (RaiidaApiException) {
            $this->assertSame(0, RevizyCurriculumMapping::query()->count());
        }
    }
}
