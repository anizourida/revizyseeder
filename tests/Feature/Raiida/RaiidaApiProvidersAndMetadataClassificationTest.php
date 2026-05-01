<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaApiProvidersAndMetadataClassificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'provider-admin@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->operator = User::query()->create([
            'name' => 'Operator',
            'email' => 'provider-operator@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        config()->set('raiida.api_providers.builtins', [
            'deepl' => [
                'provider_type' => 'deepl',
                'display_name' => 'DeepL',
                'api_key' => 'deepl-test:fx',
                'limit_unit' => 'characters',
            ],
            'gemini' => [
                'provider_type' => 'gemini',
                'display_name' => 'Gemini',
                'api_key' => 'gemini-test-key',
                'base_url' => 'https://generativelanguage.googleapis.com',
                'model' => 'gemini-1.5-flash',
                'limit_unit' => 'tokens',
            ],
        ]);
    }

    public function test_new_provider_endpoints_require_authentication(): void
    {
        $this->getJson('/api/api-providers')->assertUnauthorized();
        $this->postJson('/api/api-providers')->assertUnauthorized();
        $this->getJson('/api/api-providers/gemini/usage')->assertUnauthorized();
        $this->postJson('/api/api-providers/gemini/refresh-usage')->assertUnauthorized();
        $this->postJson('/api/vocabulary/classify-metadata')->assertUnauthorized();
    }

    public function test_operator_cannot_access_admin_provider_and_classification_endpoints(): void
    {
        Sanctum::actingAs($this->operator);

        $this->getJson('/api/api-providers')->assertForbidden();
        $this->postJson('/api/api-providers', [
            'slug' => 'x-provider',
            'provider_type' => 'custom',
        ])->assertForbidden();
        $this->postJson('/api/vocabulary/classify-metadata')->assertForbidden();
    }

    public function test_admin_can_upsert_provider_and_read_usage_summary(): void
    {
        Sanctum::actingAs($this->admin);

        $upsert = $this->postJson('/api/api-providers', [
            'slug' => 'gemini-backup',
            'provider_type' => 'gemini',
            'display_name' => 'Gemini Backup',
            'api_key' => 'backup-key',
            'base_url' => 'https://generativelanguage.googleapis.com',
            'model' => 'gemini-1.5-flash',
            'limit_unit' => 'tokens',
            'monthly_limit' => 900000,
            'is_active' => true,
        ])->assertOk()->json();

        $this->assertTrue((bool) ($upsert['success'] ?? false));
        $this->assertSame('gemini-backup', $upsert['item']['provider']['slug']);
        $this->assertSame('tokens', $upsert['item']['provider']['limit_unit']);

        $list = $this->getJson('/api/api-providers')
            ->assertOk()
            ->json();

        $this->assertGreaterThanOrEqual(1, (int) ($list['total'] ?? 0));

        $usage = $this->getJson('/api/api-providers/gemini-backup/usage')
            ->assertOk()
            ->json();

        $this->assertSame('gemini-backup', $usage['provider']['slug']);
        $this->assertArrayHasKey('remaining', $usage['usage']);
    }

    public function test_admin_can_refresh_deepl_remote_usage_and_store_snapshot(): void
    {
        Sanctum::actingAs($this->admin);

        Http::fake([
            'https://api-free.deepl.com/v2/usage' => Http::response([
                'character_count' => 12345,
                'character_limit' => 1250000,
            ], 200),
        ]);

        $response = $this->postJson('/api/api-providers/deepl/refresh-usage')
            ->assertOk()
            ->json();

        $this->assertTrue((bool) ($response['success'] ?? false));
        $this->assertSame('deepl', $response['item']['provider']['slug']);
        $this->assertSame(12345, $response['item']['usage']['remote_used']);
        $this->assertSame(1250000, $response['item']['usage']['remote_limit']);
    }

    public function test_admin_can_classify_vocabulary_metadata_inline_with_gemini(): void
    {
        Sanctum::actingAs($this->admin);

        $item = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'image_path' => 'vocab_assets/chat.png',
            'audio_path' => 'audios/chat.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([[
                                'id' => $item->id,
                                't' => 'nom',
                                'g' => 'masculine',
                                'gr' => 'animal',
                                'sg' => 'animal',
                                'c' => 0.98,
                            ]], JSON_UNESCAPED_UNICODE),
                        ]],
                    ],
                ]],
                'usageMetadata' => [
                    'promptTokenCount' => 120,
                    'candidatesTokenCount' => 25,
                    'totalTokenCount' => 145,
                ],
            ], 200),
        ]);

        $result = $this->postJson('/api/vocabulary/classify-metadata', [
            'limit' => 20,
            'queue' => false,
        ])
            ->assertOk()
            ->json();

        $this->assertSame(1, (int) ($result['updated_total'] ?? 0));
        $this->assertSame(1, (int) ($result['updated_from_ai'] ?? 0));

        $this->assertDatabaseHas('vocabulary_items', [
            'id' => $item->id,
            'lexical_type' => 'nom',
            'gender' => 'masculine',
            'distractor_group' => 'animal',
            'distractor_subgroup' => 'animal',
        ]);

        $this->assertDatabaseHas('api_provider_usages', [
            'period_key' => now()->format('Y-m'),
            'requests_count' => 1,
            'total_tokens_count' => 145,
        ]);
    }
}

