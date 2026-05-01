<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaQuestionEngineTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'Operator',
            'email' => 'question-engine@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        config()->set('raiida.revizy.api_key', 'test-revizy-key');
        config()->set('raiida.revizy.base_url', 'https://admin.revizyapp.com/api/system');
    }

    public function test_question_engine_write_endpoints_require_authentication(): void
    {
        $this->getJson('/api/generate-questions/1')->assertUnauthorized();
        $this->postJson('/api/batch-generate-publish')->assertUnauthorized();
        $this->postJson('/api/questions/check-duplicates', ['questions' => []])->assertUnauthorized();
        $this->postJson('/api/questions/0/publish', [])->assertUnauthorized();
        $this->postJson('/api/questions/0/unaccept', [])->assertUnauthorized();
        $this->getJson('/api/questions/publish-attempts')->assertUnauthorized();
        $this->deleteJson('/api/questions/1')->assertUnauthorized();
    }

    public function test_generate_questions_endpoint_returns_parity_errors_and_success_payload(): void
    {
        Sanctum::actingAs($this->user);

        $missingConcept = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
            'lexical_type' => 'nom',
            'revizy_image_file_id' => 'img-1',
        ]);

        $this->getJson('/api/generate-questions/' . $missingConcept->id)
            ->assertStatus(400)
            ->assertJson(['detail' => 'Item has no concept_id. Create a concept first.']);

        $target = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S2',
            'lexical_type' => 'nom',
            'gender' => 'masculine',
            'distractor_group' => 'animals',
            'concept_id' => '101',
            'revizy_image_file_id' => 'img-chat',
            'revizy_audio_file_id' => 'aud-chat',
        ]);

        VocabularyItem::query()->create([
            'word' => 'La porte',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S3',
            'lexical_type' => 'nom',
            'gender' => 'feminine',
            'distractor_group' => 'animals',
            'concept_id' => '102',
            'revizy_image_file_id' => 'img-porte',
            'revizy_audio_file_id' => 'aud-porte',
        ]);

        $response = $this->getJson('/api/generate-questions/' . $target->id)
            ->assertOk()
            ->json();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertSame('101', (string) $response[0]['concept_id']);
        $this->assertArrayHasKey('name', $response[0]);
        $this->assertArrayHasKey('type', $response[0]);
        $this->assertArrayHasKey('data', $response[0]);
    }

    public function test_check_duplicates_endpoint_matches_published_question_data(): void
    {
        Sanctum::actingAs($this->user);

        QuestionPublishAttempt::query()->create([
            'local_question_id' => 0,
            'concept_id' => '500',
            'name' => 'Existing',
            'question_data' => json_encode([
                'instruction' => 'أختار الكلمة المناسبة.',
                'body' => null,
                'answers' => [
                    ['body' => 'Le chat', 'is_correct' => true],
                ],
            ]),
            'status' => 'published',
            'revizy_question_id' => 'rvz-500',
        ]);

        $response = $this->postJson('/api/questions/check-duplicates', [
            'questions' => [
                [
                    'index' => 0,
                    'concept_id' => '500',
                    'data' => [
                        'body' => null,
                        'answers' => [
                            ['is_correct' => true, 'body' => 'Le chat'],
                        ],
                        'instruction' => 'أختار الكلمة المناسبة.',
                    ],
                ],
                [
                    'index' => 1,
                    'concept_id' => '999',
                    'data' => ['instruction' => 'no match'],
                ],
            ],
        ])->assertOk()->json();

        $this->assertCount(1, $response['duplicates']);
        $this->assertSame(0, $response['duplicates'][0]['index']);
        $this->assertTrue($response['duplicates'][0]['is_published']);
        $this->assertSame('rvz-500', $response['duplicates'][0]['revizy_question_id']);
    }

    public function test_publish_endpoint_reuses_duplicate_without_external_call(): void
    {
        Sanctum::actingAs($this->user);

        QuestionPublishAttempt::query()->create([
            'local_question_id' => 88,
            'concept_id' => '601',
            'name' => 'Existing Question',
            'question_data' => json_encode(['instruction' => 'match', 'answers' => [['body' => 'A']]]),
            'status' => 'published',
            'revizy_question_id' => 'rvz-existing-601',
        ]);

        Http::fake();

        $payload = [
            'local_question_id' => 0,
            'concept_id' => '601',
            'name' => 'New Duplicate',
            'type' => 'universal',
            'status' => 'published',
            'data' => ['answers' => [['body' => 'A']], 'instruction' => 'match'],
        ];

        $response = $this->postJson('/api/questions/0/publish', $payload)
            ->assertOk()
            ->json();

        $this->assertTrue($response['success']);
        $this->assertTrue($response['is_duplicate']);
        $this->assertSame('rvz-existing-601', $response['revizy_question_id']);
        Http::assertNothingSent();

        $this->assertDatabaseHas('question_publish_attempts', [
            'id' => $response['attempt_id'],
            'status' => 'published',
            'error_message' => 'Duplicate of internal question',
            'revizy_question_id' => 'rvz-existing-601',
        ]);
    }

    public function test_publish_endpoint_calls_revizy_and_persists_success(): void
    {
        Sanctum::actingAs($this->user);

        Http::fake([
            'https://admin.revizyapp.com/api/system/questions' => Http::response(['id' => 'rvz-new-1'], 201),
        ]);

        $response = $this->postJson('/api/questions/1/publish', [
            'local_question_id' => 1,
            'concept_id' => '700',
            'name' => 'Question 700',
            'type' => 'universal',
            'status' => 'published',
            'data' => ['instruction' => 'test', 'answers' => []],
        ])->assertOk()->json();

        $this->assertTrue($response['success']);
        $this->assertSame('rvz-new-1', $response['revizy_question_id']);

        $this->assertDatabaseHas('question_publish_attempts', [
            'id' => $response['attempt_id'],
            'local_question_id' => 1,
            'concept_id' => '700',
            'status' => 'published',
            'revizy_question_id' => 'rvz-new-1',
        ]);
    }

    public function test_publish_endpoint_marks_failed_when_revizy_returns_error(): void
    {
        Sanctum::actingAs($this->user);

        Http::fake([
            'https://admin.revizyapp.com/api/system/questions' => Http::response('Bad payload', 422),
        ]);

        $response = $this->postJson('/api/questions/2/publish', [
            'local_question_id' => 2,
            'concept_id' => '701',
            'name' => 'Question 701',
            'type' => 'universal',
            'status' => 'published',
            'data' => ['instruction' => 'test', 'answers' => []],
        ])->assertStatus(500)->json();

        $this->assertStringContainsString('Failed to publish', $response['detail']);
        $this->assertDatabaseHas('question_publish_attempts', [
            'local_question_id' => 2,
            'concept_id' => '701',
            'status' => 'failed',
        ]);
    }

    public function test_unaccept_publish_attempts_and_delete_endpoints_match_contract(): void
    {
        Sanctum::actingAs($this->user);

        $unaccepted = $this->postJson('/api/questions/3/unaccept', [
            'local_question_id' => 3,
            'concept_id' => '800',
            'name' => 'Question 800',
            'data' => ['instruction' => 'x', 'answers' => []],
        ])->assertOk()->json();

        $this->assertTrue($unaccepted['success']);
        $this->assertSame('unaccepted', $unaccepted['status']);

        $attempts = $this->getJson('/api/questions/publish-attempts?status=unaccepted')
            ->assertOk()
            ->json();
        $this->assertCount(1, $attempts);
        $this->assertSame($unaccepted['attempt_id'], $attempts[0]['id']);

        $this->deleteJson('/api/questions/' . $unaccepted['attempt_id'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->deleteJson('/api/questions/' . $unaccepted['attempt_id'])
            ->assertStatus(404)
            ->assertJson(['detail' => 'Question not found']);
    }

    public function test_batch_generate_publish_returns_summary_and_persists_attempts(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'batch-admin@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => User::ROLE_ADMIN,
        ]);

        Sanctum::actingAs($admin);

        QuestionPublishAttempt::query()->create([
            'local_question_id' => 0,
            'concept_id' => '900',
            'name' => 'Already published',
            'question_data' => '{}',
            'status' => 'published',
            'revizy_question_id' => 'rvz-900',
        ]);

        VocabularyItem::query()->create([
            'id' => 1,
            'word' => 'Le chat',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S11',
            'lexical_type' => 'nom',
            'gender' => 'masculine',
            'distractor_group' => 'animals',
            'concept_id' => '100',
            'revizy_image_file_id' => 'img-100',
            'revizy_audio_file_id' => 'aud-100',
        ]);

        VocabularyItem::query()->create([
            'id' => 2,
            'word' => 'La porte',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S12',
            'lexical_type' => 'nom',
            'gender' => 'feminine',
            'distractor_group' => 'animals',
            'concept_id' => '900',
            'revizy_image_file_id' => 'img-900',
            'revizy_audio_file_id' => 'aud-900',
        ]);

        Http::fake([
            'https://admin.revizyapp.com/api/system/questions' => Http::response(['id' => 'rvz-any'], 201),
        ]);

        $response = $this->postJson('/api/batch-generate-publish')
            ->assertOk()
            ->json();

        $this->assertTrue($response['success']);
        $this->assertSame(1, $response['total']);
        $this->assertGreaterThan(0, $response['generated']);
        $this->assertSame($response['generated'], $response['published']);
        $this->assertSame(0, $response['failed']);
        $this->assertSame(0, $response['skipped']);
        $this->assertSame('done', $response['details'][0]['status']);

        $this->assertDatabaseHas('question_publish_attempts', [
            'concept_id' => '100',
            'status' => 'published',
        ]);
    }
}
