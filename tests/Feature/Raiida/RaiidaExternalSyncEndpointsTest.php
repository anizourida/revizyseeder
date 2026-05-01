<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaExternalSyncEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::query()->create([
            'name' => 'Operator',
            'email' => 'external-sync@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_OPERATOR,
        ]);

        config()->set('raiida.revizy.base_url', 'https://api.revizy.test');
        config()->set('raiida.revizy.api_key', 'test-revizy-key');
        config()->set('raiida.walidio.base_url', 'https://walidio.test');
        config()->set('raiida.walidio.public_key', 'test-walidio-key');
    }

    public function test_external_sync_endpoints_require_authentication(): void
    {
        $this->postJson('/api/vocabulary-assets/1/upload-image')->assertUnauthorized();
        $this->postJson('/api/vocabulary-assets/1/upload-audio')->assertUnauthorized();
        $this->postJson('/api/vocabulary-assets/1/upload-walidio')->assertUnauthorized();
        $this->postJson('/api/vocabulary-assets/1/upload-flashcard?flashcard_category_id=3')->assertUnauthorized();
        $this->postJson('/api/vocabulary-assets/1/create-concept', [])->assertUnauthorized();
        $this->postJson('/api/concepts', [])->assertUnauthorized();
        $this->getJson('/api/proxy/skills/1')->assertUnauthorized();
    }

    public function test_upload_image_audio_and_walidio_endpoints_persist_external_ids(): void
    {
        Sanctum::actingAs($this->operator);

        $fixturesDir = storage_path('app/testing-media');
        File::ensureDirectoryExists($fixturesDir);
        File::put($fixturesDir . '/test-image.png', 'png-data');
        File::put($fixturesDir . '/test-audio.mp3', 'mp3-data');

        $item = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'image_path' => $fixturesDir . '/test-image.png',
            'audio_path' => $fixturesDir . '/test-audio.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
            'ar_translation' => 'قط',
        ]);

        Http::fake([
            'https://api.revizy.test/files' => Http::sequence()
                ->push(['secret_id' => 'img-secret-1'], 201)
                ->push(['secret_id' => 'aud-secret-1'], 201),
            'https://walidio.test/images' => Http::response(['data' => ['id' => 909]], 201),
        ]);

        $image = $this->postJson('/api/vocabulary-assets/' . $item->id . '/upload-image')
            ->assertOk()
            ->json();
        $this->assertSame('img-secret-1', $image['revizy_image_file_id']);

        $audio = $this->postJson('/api/vocabulary-assets/' . $item->id . '/upload-audio')
            ->assertOk()
            ->json();
        $this->assertSame('aud-secret-1', $audio['revizy_audio_file_id']);

        $walidio = $this->postJson('/api/vocabulary-assets/' . $item->id . '/upload-walidio')
            ->assertOk()
            ->json();
        $this->assertSame('909', $walidio['walidio_image_id']);

        $this->assertDatabaseHas('vocabulary_items', [
            'id' => $item->id,
            'revizy_image_file_id' => 'img-secret-1',
            'revizy_audio_file_id' => 'aud-secret-1',
            'walidio_image_id' => '909',
        ]);
    }

    public function test_proxy_flashcard_and_concept_endpoints_match_contract(): void
    {
        Sanctum::actingAs($this->operator);

        $item = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'image_path' => 'vocab_assets/test-image.png',
            'audio_path' => 'test-audio.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
            'ar_translation' => 'قط',
            'revizy_image_file_id' => 'img-123',
            'revizy_audio_file_id' => 'aud-123',
        ]);

        Http::fake([
            'https://api.revizy.test/skills/12' => Http::response(['data' => ['id' => 12, 'name' => 'Skill FR']], 200),
            'https://api.revizy.test/flashcards' => Http::response(['data' => ['id' => 450]], 201),
            'https://api.revizy.test/concepts' => Http::sequence()
                ->push(['data' => ['id' => 551]], 201)
                ->push(['data' => ['id' => 552]], 201),
        ]);

        $this->getJson('/api/proxy/skills/12')
            ->assertOk()
            ->assertJsonPath('data.id', 12);

        $flashcard = $this->postJson('/api/vocabulary-assets/' . $item->id . '/upload-flashcard?flashcard_category_id=77')
            ->assertOk()
            ->json();

        $this->assertTrue($flashcard['success']);
        $this->assertSame('450', $flashcard['flashcard_id']);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.revizy.test/flashcards'
                && ($request['front_text'] ?? null) === '[BLUE]Le[/BLUE] chat';
        });

        $createForVocab = $this->postJson('/api/vocabulary-assets/' . $item->id . '/create-concept', [
            'skill_id' => 12,
            'unite_id' => 99,
            'name' => 'Le chat',
            'description' => 'Le mot de vocabulaire Le chat',
            'week' => 1,
            'status' => 'published',
            'is_active' => true,
        ])->assertOk()->json();

        $this->assertTrue($createForVocab['success']);
        $this->assertSame('551', $createForVocab['concept_id']);

        $generic = $this->postJson('/api/concepts', [
            'skill_id' => 12,
            'unite_id' => 99,
            'name' => 'Concept générique',
            'description' => 'Concept test',
            'week' => 2,
            'status' => 'published',
            'is_active' => true,
        ])->assertOk()->json();

        $this->assertTrue($generic['success']);
        $this->assertSame('552', $generic['concept_id']);

        $this->assertDatabaseHas('vocabulary_items', [
            'id' => $item->id,
            'flashcard_id' => '450',
            'concept_id' => '551',
            'revizy_skill_id' => 12,
            'revizy_unite_id' => 99,
        ]);
    }
}
