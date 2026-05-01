<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaQuestionStudioSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'Smoke Operator',
            'email' => 'smoke-operator@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        config()->set('raiida.revizy.api_key', 'test-revizy-key');
        config()->set('raiida.revizy.base_url', 'https://admin.revizyapp.com/api/system');
    }

    public function test_question_studio_smoke_flow(): void
    {
        Sanctum::actingAs($this->user);

        $target = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'image_path' => 'vocab_assets/chat.png',
            'audio_path' => 'chat.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
            'ar_translation' => 'قط',
            'lexical_type' => 'nom',
            'gender' => 'masculine',
            'distractor_group' => 'animals',
            'concept_id' => '1100',
            'revizy_image_file_id' => 'img-chat',
            'revizy_audio_file_id' => 'aud-chat',
        ]);

        $distractor = VocabularyItem::query()->create([
            'word' => 'La porte',
            'image_path' => 'vocab_assets/porte.png',
            'audio_path' => 'porte.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S2',
            'ar_translation' => 'باب',
            'lexical_type' => 'nom',
            'gender' => 'feminine',
            'distractor_group' => 'animals',
            'concept_id' => '1200',
            'revizy_image_file_id' => 'img-porte',
            'revizy_audio_file_id' => 'aud-porte',
        ]);

        // 1) Generate questions for selected asset (modal open action in UI)
        $generated = $this->getJson('/api/generate-questions/' . $target->id)
            ->assertOk()
            ->json();

        $this->assertIsArray($generated);
        $this->assertNotEmpty($generated);
        $this->assertSame('1100', (string) $generated[0]['concept_id']);

        // 2) Concept lookup endpoint used by the studio
        $conceptLookup = $this->getJson('/api/vocabulary-assets/search-concept/1100')
            ->assertOk()
            ->json();
        $this->assertSame($target->id, $conceptLookup['id']);
        $this->assertSame('Le chat', $conceptLookup['name']);

        // 3) Secret-id lookup endpoint used for media rendering in preview
        $secretId = $this->findFirstSecretId($generated);
        $this->assertNotNull($secretId);

        $secretLookup = $this->getJson('/api/vocabulary-assets/by-secret-id/' . $secretId)
            ->assertOk()
            ->json();
        $this->assertContains($secretLookup['id'], [$target->id, $distractor->id]);

        // 4) Duplicate check before publishing should be empty
        $checkPayload = $this->toDuplicatePayload($generated);

        $initialDuplicates = $this->postJson('/api/questions/check-duplicates', [
            'questions' => $checkPayload,
        ])->assertOk()->json();

        $this->assertSame([], $initialDuplicates['duplicates']);

        // 5) Publish first question (same as Publish button action)
        $first = $generated[0];

        Http::fake([
            'https://admin.revizyapp.com/api/system/questions' => Http::response(['id' => 'rvz-smoke-1'], 201),
        ]);

        $publish = $this->postJson('/api/questions/0/publish', [
            'local_question_id' => 0,
            'concept_id' => (string) ($first['concept_id'] ?? ''),
            'name' => (string) ($first['name'] ?? 'Question'),
            'type' => (string) ($first['type'] ?? 'universal'),
            'status' => 'published',
            'data' => $first['data'],
        ])->assertOk()->json();

        $this->assertTrue($publish['success']);
        $this->assertSame('rvz-smoke-1', $publish['revizy_question_id']);

        // 6) Duplicate check now flags the published question
        $afterPublishDuplicates = $this->postJson('/api/questions/check-duplicates', [
            'questions' => $checkPayload,
        ])->assertOk()->json();

        $duplicateIndexes = collect($afterPublishDuplicates['duplicates'])->pluck('index')->all();
        $this->assertContains(0, $duplicateIndexes);

        // 7) Publish attempts list includes the published attempt
        $publishedAttempts = $this->getJson('/api/questions/publish-attempts?status=published')
            ->assertOk()
            ->json();
        $this->assertGreaterThanOrEqual(1, count($publishedAttempts));

        // 8) Unaccept another question if available (same as Unaccept button)
        $unacceptIndex = count($generated) > 1 ? 1 : 0;
        $unacceptQuestion = $generated[$unacceptIndex];

        $unaccept = $this->postJson('/api/questions/' . $unacceptIndex . '/unaccept', [
            'local_question_id' => $unacceptIndex,
            'concept_id' => (string) ($unacceptQuestion['concept_id'] ?? ''),
            'name' => (string) ($unacceptQuestion['name'] ?? 'Question'),
            'data' => $unacceptQuestion['data'],
        ])->assertOk()->json();

        $this->assertTrue($unaccept['success']);
        $this->assertSame('unaccepted', $unaccept['status']);

        $this->assertDatabaseHas('question_publish_attempts', [
            'id' => $unaccept['attempt_id'],
            'status' => 'unaccepted',
        ]);

        // 9) Questions list and counts endpoints still respond with expected data
        $questionsList = $this->getJson('/api/questions')->assertOk()->json();
        $this->assertNotEmpty($questionsList);

        $counts = $this->getJson('/api/questions/counts')->assertOk()->json();
        $this->assertArrayHasKey('1100', $counts);

        // 10) Delete unaccepted attempt from table UI action
        $this->deleteJson('/api/questions/' . $unaccept['attempt_id'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('question_publish_attempts', [
            'id' => $unaccept['attempt_id'],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @return array<int, array{index:int,concept_id:string,data:array<string,mixed>}>
     */
    private function toDuplicatePayload(array $questions): array
    {
        return collect($questions)->map(
            static function (array $question, int $index): array {
                return [
                    'index' => $index,
                    'concept_id' => (string) ($question['concept_id'] ?? ''),
                    'data' => is_array($question['data'] ?? null) ? $question['data'] : [],
                ];
            }
        )->values()->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function findFirstSecretId(array $questions): ?string
    {
        foreach ($questions as $question) {
            $data = $question['data'] ?? [];
            if (! is_array($data)) {
                continue;
            }

            $media = $data['media'] ?? [];
            if (is_array($media)) {
                $image = $media['image'] ?? null;
                if (is_string($image) && $image !== '') {
                    return $image;
                }

                $audio = $media['audio'] ?? null;
                if (is_string($audio) && $audio !== '') {
                    return $audio;
                }
            }

            $answers = $data['answers'] ?? [];
            if (! is_array($answers)) {
                continue;
            }

            foreach ($answers as $answer) {
                if (! is_array($answer)) {
                    continue;
                }

                $answerMedia = $answer['media'] ?? [];
                if (! is_array($answerMedia)) {
                    continue;
                }

                $image = $answerMedia['image'] ?? null;
                if (is_string($image) && $image !== '') {
                    return $image;
                }

                $audio = $answerMedia['audio'] ?? null;
                if (is_string($audio) && $audio !== '') {
                    return $audio;
                }
            }
        }

        return null;
    }
}
