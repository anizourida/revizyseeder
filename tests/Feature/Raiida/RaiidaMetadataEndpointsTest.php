<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\Audio;
use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\Grammaire;
use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaMetadataEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $vocabOne = VocabularyItem::query()->create([
            'id' => 1,
            'word' => 'Le chat',
            'image_path' => 'vocab_assets/chat.png',
            'audio_path' => 'chat.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
            'ar_translation' => 'قط',
            'concept_id' => 'C-CHAT',
            'revizy_image_file_id' => 'img-secret-chat',
            'revizy_audio_file_id' => 'aud-secret-chat',
            'extracted_at' => now(),
        ]);

        $vocabTwo = VocabularyItem::query()->create([
            'id' => 2,
            'word' => 'La porte',
            'image_path' => 'vocab_assets/porte.png',
            'audio_path' => 'porte.mp3',
            'grade' => 'N2',
            'subject' => 'FR',
            'period' => 'P2',
            'week' => 'SEM2',
            'lesson_id' => 'FR_N2_P2_SEM2_S1',
            'ar_translation' => 'باب',
            'concept_id' => 'C-PORTE',
            'revizy_image_file_id' => 'img-secret-porte',
            'revizy_audio_file_id' => 'aud-secret-porte',
            'extracted_at' => now(),
        ]);

        Audio::query()->create([
            'id' => 1,
            'vocabulary_item_id' => $vocabOne->id,
            'text' => 'Le chat',
            'file_path' => 'chat.mp3',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        Audio::query()->create([
            'id' => 2,
            'vocabulary_item_id' => $vocabTwo->id,
            'text' => 'La porte',
            'file_path' => 'porte.mp3',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Conjugaison::query()->create([
            'id' => 1,
            'n' => 'N1',
            'p' => 'P1',
            'sem' => 'SEM1',
            'verbe' => 'Avoir',
            'tense' => 'Présent',
            'raw_data' => 'Raw A',
            'concept_id' => 'C1',
            'week' => 1,
            'revizy_skill_id' => 11,
            'revizy_unite_id' => 22,
        ]);

        Conjugaison::query()->create([
            'id' => 2,
            'n' => 'N2',
            'p' => 'P2',
            'sem' => 'SEM2',
            'verbe' => null,
            'tense' => null,
            'raw_data' => 'Raw B fallback text that is long enough to test substring behavior',
            'concept_id' => null,
            'week' => null,
            'revizy_skill_id' => null,
            'revizy_unite_id' => null,
        ]);

        Grammaire::query()->create([
            'id' => 1,
            'n' => 'N1',
            'p' => 'P1',
            'sem' => 'SEM1',
            'objectif' => 'Objectif N1',
            'lesson_title' => 'Lesson N1',
            'raw_data' => 'G Raw A',
        ]);

        Grammaire::query()->create([
            'id' => 2,
            'n' => 'N3',
            'p' => 'P3',
            'sem' => 'SEM3',
            'objectif' => 'Objectif N3',
            'lesson_title' => 'Lesson N3',
            'raw_data' => 'G Raw B',
        ]);
    }

    public function test_metadata_endpoints_require_authentication(): void
    {
        $this->getJson('/api/audios')->assertUnauthorized();
        $this->getJson('/api/conjugaison')->assertUnauthorized();
        $this->getJson('/api/grammaire')->assertUnauthorized();
        $this->getJson('/api/roadmap')->assertUnauthorized();
        $this->getJson('/api/vocabulary-assets/search-concept/C-CHAT')->assertUnauthorized();
        $this->getJson('/api/vocabulary-assets/by-secret-id/img-secret-chat')->assertUnauthorized();
    }

    public function test_audios_endpoint_matches_expected_shape_and_order(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/audios')->assertOk()->json();

        $this->assertCount(2, $response);
        $this->assertSame(2, $response[0]['id']);
        $this->assertSame(2, $response[0]['vocabulary_id']);
        $this->assertSame('La porte', $response[0]['word']);
        $this->assertSame('vocab_assets/porte.png', $response[0]['image']);
        $this->assertSame('porte.mp3', $response[0]['audio_file']);
    }

    public function test_conjugaison_endpoint_supports_filters_and_shape(): void
    {
        Sanctum::actingAs($this->user);

        $all = $this->getJson('/api/conjugaison')->assertOk()->json();
        $this->assertCount(2, $all);
        $this->assertSame('Avoir', $all[0]['verbe']);

        $filtered = $this->getJson('/api/conjugaison?n=N1&p=P1&sem=SEM1')->assertOk()->json();
        $this->assertCount(1, $filtered);
        $this->assertSame('C1', $filtered[0]['concept_id']);
        $this->assertSame(11, $filtered[0]['revizy_skill_id']);
    }

    public function test_grammaire_endpoint_supports_filters(): void
    {
        Sanctum::actingAs($this->user);

        $all = $this->getJson('/api/grammaire')->assertOk()->json();
        $this->assertCount(2, $all);

        $filtered = $this->getJson('/api/grammaire?n=N3&p=P3&sem=SEM3')->assertOk()->json();
        $this->assertCount(1, $filtered);
        $this->assertSame('Objectif N3', $filtered[0]['objectif']);
    }

    public function test_roadmap_endpoint_builds_merged_overview_payload(): void
    {
        Sanctum::actingAs($this->user);

        $roadmap = $this->getJson('/api/roadmap')->assertOk()->json();

        $n1 = collect($roadmap)->firstWhere('n', 'N1');
        $this->assertNotNull($n1);
        $this->assertSame('Avoir (Présent)', $n1['conjugaison']);
        $this->assertSame('Objectif N1', $n1['grammaire']);
        $this->assertSame(1, $n1['vocab_count']);

        $n2 = collect($roadmap)->first(function (array $row): bool {
            return $row['n'] === 'N2' && $row['p'] === 'P2' && $row['sem'] === 'SEM2';
        });
        $this->assertNotNull($n2);
        $this->assertSame('Raw B fallback text that is long enough to test su', $n2['conjugaison']);
        $this->assertSame('-', $n2['grammaire']);
        $this->assertSame(1, $n2['vocab_count']);

        $n3 = collect($roadmap)->first(function (array $row): bool {
            return $row['n'] === 'N3' && $row['p'] === 'P3' && $row['sem'] === 'SEM3';
        });
        $this->assertNotNull($n3);
        $this->assertSame('-', $n3['conjugaison']);
        $this->assertSame('Objectif N3', $n3['grammaire']);
        $this->assertSame(0, $n3['vocab_count']);
    }

    public function test_vocabulary_assets_search_concept_endpoint_matches_parity_shape(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/vocabulary-assets/search-concept/C-CHAT')
            ->assertOk()
            ->json();

        $this->assertSame(1, $response['id']);
        $this->assertSame(1, $response['vocabulary_id']);
        $this->assertSame('Le chat', $response['name']);
        $this->assertSame('قط', $response['name_ar']);
        $this->assertSame('C-CHAT', $response['concept_id']);
        $this->assertSame('FR_N1_P1_SEM1_S1', $response['vocabulary']['lesson_id']);
        $this->assertSame('vocab_assets/chat.png', $response['vocabulary']['image_path']);

        $this->getJson('/api/vocabulary-assets/search-concept/unknown-concept')
            ->assertStatus(404)
            ->assertJson([
                'detail' => 'Concept not found in local vocabulary assets',
            ]);
    }

    public function test_vocabulary_assets_by_secret_id_endpoint_matches_parity_shape(): void
    {
        Sanctum::actingAs($this->user);

        $imageMatch = $this->getJson('/api/vocabulary-assets/by-secret-id/img-secret-chat')
            ->assertOk()
            ->json();

        $this->assertSame(1, $imageMatch['id']);
        $this->assertSame('Le chat', $imageMatch['name']);
        $this->assertSame('vocab_assets/chat.png', $imageMatch['image']);
        $this->assertSame('chat.mp3', $imageMatch['audio']);
        $this->assertSame('img-secret-chat', $imageMatch['revizy_image_file_id']);
        $this->assertSame('aud-secret-chat', $imageMatch['revizy_audio_file_id']);

        $audioMatch = $this->getJson('/api/vocabulary-assets/by-secret-id/aud-secret-porte')
            ->assertOk()
            ->json();
        $this->assertSame(2, $audioMatch['id']);
        $this->assertSame('La porte', $audioMatch['name']);

        $this->getJson('/api/vocabulary-assets/by-secret-id/unknown-secret')
            ->assertStatus(404)
            ->assertJson([
                'detail' => 'Asset not found with this secret ID',
            ]);
    }
}
