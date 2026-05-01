<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\RevizyCurriculumMapping;
use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaConceptRecoveryEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('raiida.revizy.base_url', 'https://api.revizy.test');
        config()->set('raiida.revizy.api_key', 'test-revizy-key');
    }

    public function test_recover_missing_endpoint_requires_authentication(): void
    {
        $this->postJson('/api/concepts/recover-missing')->assertUnauthorized();
    }

    public function test_recover_missing_endpoint_processes_filtered_scope_and_returns_summary(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('Secret123!'),
        ]);
        Sanctum::actingAs($user);

        $target = VocabularyItem::query()->create([
            'word' => 'parc',
            'grade' => 'N5',
            'subject' => 'FR',
            'period' => 'P3',
            'week' => 'SEM4',
            'lesson_id' => 'FR_N5_P3_SEM4_S1',
            'ar_translation' => 'حديقة',
        ]);

        $outsideScope = VocabularyItem::query()->create([
            'word' => 'porte',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P2',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N4_P2_SEM1_S1',
            'ar_translation' => 'باب',
        ]);

        RevizyCurriculumMapping::query()->create([
            'subject_code' => 'FR',
            'grade_code' => 'N5',
            'period_code' => 'P3',
            'grade_index' => 5,
            'period_index' => 3,
            'revizy_unite_id' => 503,
            'revizy_vocab_skill_id' => 551,
        ]);

        Http::fake([
            'https://api.revizy.test/concepts/search*' => Http::response([
                'count' => 1,
                'data' => [
                    ['id' => 12001, 'name' => 'parc'],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/concepts/recover-missing', [
            'grade' => 'N5',
            'period' => 'P3',
            'week' => 'SEM4',
            'limit' => 100,
            'queue' => false,
            'wait_ms' => 0,
        ])->assertOk()->json();

        $this->assertSame(1, (int) ($response['targeted'] ?? 0));
        $this->assertSame(1, (int) ($response['linked_existing'] ?? 0));
        $this->assertSame(0, (int) ($response['created_total'] ?? 0));
        $this->assertSame(0, (int) ($response['failed_total'] ?? 0));
        $this->assertSame(0, (int) ($response['mapping_synced_total'] ?? 0));
        $this->assertSame(0, (int) ($response['remaining_missing_in_scope'] ?? 0));

        $target->refresh();
        $outsideScope->refresh();

        $this->assertSame('12001', (string) $target->concept_id);
        $this->assertNull($outsideScope->concept_id);
    }
}
