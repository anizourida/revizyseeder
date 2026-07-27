<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\VocabularyItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RevizySeederVocabularySyncToRevizyCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('raiida.revizy.base_url', 'https://api.revizy.test');
        config()->set('raiida.revizy.api_key', 'test-key');
    }

    public function test_dry_run_does_not_send_requests(): void
    {
        $this->createVocabularyItem();
        Http::fake();

        $this->artisan('revizyseeder:vocabulary:sync-to-revizy', [
            '--grade' => 'N4',
            '--dry-run' => true,
        ])->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_it_sends_expected_payload_and_reports_warnings(): void
    {
        $item = $this->createVocabularyItem();

        Http::fake([
            'https://api.revizy.test/vocabulary' => Http::response([
                'id' => 999,
                'created' => true,
                'warnings' => ['Image file secret img-secret was not found.'],
                'data' => ['id' => 999],
            ], 201),
        ]);

        $this->artisan('revizyseeder:vocabulary:sync-to-revizy', [
            '--grade' => 'N4',
            '--period' => 'P2',
            '--week' => 'SEM3',
            '--only-missing' => true,
        ])->assertSuccessful();

        Http::assertSent(function ($request) use ($item): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.revizy.test/vocabulary'
                && $request->method() === 'POST'
                && ($payload['source'] ?? null) === 'seeder'
                && (int) ($payload['source_vocabulary_item_id'] ?? 0) === (int) $item->id
                && ($payload['word'] ?? null) === 'La porte'
                && ($payload['base_word'] ?? null) === 'porte'
                && ($payload['ar_translation'] ?? null) === 'الباب'
                && ($payload['grade_code'] ?? null) === 'N4'
                && ($payload['subject_code'] ?? null) === 'FR'
                && ($payload['period_code'] ?? null) === 'P2'
                && ($payload['week_code'] ?? null) === 'SEM3'
                && ($payload['concept_id'] ?? null) === '123'
                && (int) ($payload['revizy_skill_id'] ?? 0) === 456
                && (int) ($payload['revizy_unite_id'] ?? 0) === 789
                && ($payload['revizy_image_file_id'] ?? null) === 'img-secret'
                && ($payload['revizy_audio_file_id'] ?? null) === 'aud-secret'
                && ($payload['status'] ?? null) === 'published'
                && ($payload['only_missing'] ?? null) === true;
        });
    }

    private function createVocabularyItem(): VocabularyItem
    {
        return VocabularyItem::query()->create([
            'word' => 'La porte',
            'base_word' => 'porte',
            'image_path' => 'vocab_assets/porte.png',
            'audio_path' => 'audios/porte.mp3',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P2',
            'week' => 'SEM3',
            'lesson_id' => 'FR_N4_P2_SEM3_S1',
            'ar_translation' => 'الباب',
            'lexical_type' => 'noun',
            'gender' => 'feminine',
            'distractor_group' => 'objects',
            'distractor_subgroup' => 'classroom',
            'revizy_image_file_id' => 'img-secret',
            'revizy_audio_file_id' => 'aud-secret',
            'concept_id' => '123',
            'revizy_skill_id' => 456,
            'revizy_unite_id' => 789,
            'extracted_at' => now(),
        ]);
    }
}
