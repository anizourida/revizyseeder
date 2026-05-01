<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Period;
use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\Subject;
use App\Models\Raiida\VocabularyItem;
use App\Models\Raiida\Week;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaReadEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'api-user',
            'email' => 'api-user@example.com',
            'password' => bcrypt('password'),
        ]);

        $gradeOne = Grade::query()->create(['id' => 1, 'code' => 'N1', 'name' => '1']);
        $gradeSeven = Grade::query()->create(['id' => 7, 'code' => 'N7', 'name' => '7']);

        $subjectFrOne = Subject::query()->create([
            'id' => 1,
            'grade_id' => $gradeOne->id,
            'code' => 'FR',
            'name' => 'FR',
        ]);

        $subjectFrSeven = Subject::query()->create([
            'id' => 2,
            'grade_id' => $gradeSeven->id,
            'code' => 'FR',
            'name' => 'FR',
        ]);

        $periodOne = Period::query()->create([
            'id' => 1,
            'subject_id' => $subjectFrOne->id,
            'code' => 'P1',
            'name' => 'P1',
        ]);

        $periodSeven = Period::query()->create([
            'id' => 2,
            'subject_id' => $subjectFrSeven->id,
            'code' => 'P1',
            'name' => 'P1',
        ]);

        $weekOne = Week::query()->create([
            'id' => 1,
            'period_id' => $periodOne->id,
            'code' => 'SEM1',
            'name' => 'SEM1',
        ]);

        $weekSeven = Week::query()->create([
            'id' => 2,
            'period_id' => $periodSeven->id,
            'code' => 'SEM1',
            'name' => 'SEM1',
        ]);

        FileAsset::query()->create([
            'id' => 1,
            'week_id' => $weekOne->id,
            'filename' => 'FR_N1_file.pdf',
            'local_path' => 'files/FR_N1_file.pdf',
            'original_url' => 'https://example.com/1',
            'size_bytes' => 1024,
            'is_downloaded' => true,
            'is_integrity_checked' => true,
            'is_corrupt' => false,
            'is_vocab_extracted' => true,
            'session_id' => 'sess-1',
            'vocab_count' => 3,
            'downloaded_at' => now(),
        ]);

        FileAsset::query()->create([
            'id' => 2,
            'week_id' => $weekSeven->id,
            'filename' => 'FR_N7_file.pdf',
            'local_path' => 'files/FR_N7_file.pdf',
            'original_url' => 'https://example.com/2',
            'size_bytes' => 2048,
            'is_downloaded' => false,
            'is_integrity_checked' => true,
            'is_corrupt' => true,
            'is_vocab_extracted' => false,
            'session_id' => 'sess-2',
            'vocab_count' => 0,
            'downloaded_at' => null,
        ]);

        VocabularyItem::query()->create([
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
            'extracted_at' => now()->subMinute(),
        ]);

        VocabularyItem::query()->create([
            'id' => 2,
            'word' => 'La porte',
            'image_path' => 'vocab_assets/porte.png',
            'audio_path' => 'porte.mp3',
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S2',
            'ar_translation' => 'باب',
            'extracted_at' => now(),
        ]);

        QuestionPublishAttempt::query()->create([
            'id' => 1,
            'local_question_id' => 0,
            'concept_id' => 'C1',
            'name' => 'Q1',
            'question_data' => '{}',
            'status' => 'published',
        ]);

        QuestionPublishAttempt::query()->create([
            'id' => 2,
            'local_question_id' => 1,
            'concept_id' => 'C1',
            'name' => 'Q2',
            'question_data' => '{}',
            'status' => 'failed',
        ]);

        QuestionPublishAttempt::query()->create([
            'id' => 3,
            'local_question_id' => 2,
            'concept_id' => 'C2',
            'name' => 'Q3',
            'question_data' => '{}',
            'status' => 'published',
        ]);
    }

    public function test_endpoints_require_authentication(): void
    {
        $this->getJson('/api/stats')->assertUnauthorized();
    }

    public function test_stats_files_and_tree_match_expected_shape(): void
    {
        Sanctum::actingAs($this->user);

        $stats = $this->getJson('/api/stats')
            ->assertOk()
            ->assertJsonStructure([
                'total_files',
                'downloaded_files',
                'corrupt_files',
                'total_size_gb',
                'completion_percentage',
            ])
            ->json();

        $this->assertSame(2, $stats['total_files']);
        $this->assertSame(1, $stats['downloaded_files']);
        $this->assertSame(1, $stats['corrupt_files']);

        $files = $this->getJson('/api/files')->assertOk()->json();
        $this->assertCount(1, $files);
        $this->assertSame('FR_N1_file.pdf', $files[0]['filename']);
        $this->assertSame('1', $files[0]['grade']);

        $tree = $this->getJson('/api/tree')->assertOk()->json();
        $this->assertCount(1, $tree);
        $this->assertSame('1', $tree[0]['name']);
        $this->assertSame('grade', $tree[0]['type']);
    }

    public function test_vocabulary_and_assets_endpoints_match_expected_shape(): void
    {
        Sanctum::actingAs($this->user);

        $vocabulary = $this->getJson('/api/vocabulary?grade=N1&period=P1&week=SEM1')
            ->assertOk()
            ->json();

        $this->assertCount(2, $vocabulary);
        $this->assertSame('La porte', $vocabulary[0]['word']);

        $assets = $this->getJson('/api/vocabulary-assets?grade=N1&period=P1&week=SEM1')
            ->assertOk()
            ->assertJsonStructure([
                'items',
                'total',
            ])
            ->json();

        $this->assertSame(2, $assets['total']);
        $this->assertSame('vocab_assets/chat.png', $assets['items'][0]['image']);
        $this->assertSame('chat.mp3', $assets['items'][0]['audio']);
        $this->assertSame('Le chat', $assets['items'][0]['name']);
        $this->assertSame('قط', $assets['items'][0]['name_ar']);
    }

    public function test_questions_counts_support_all_published_and_both_modes(): void
    {
        Sanctum::actingAs($this->user);

        $all = $this->getJson('/api/questions/counts')->assertOk()->json();
        $this->assertSame(2, $all['C1']);
        $this->assertSame(1, $all['C2']);

        $published = $this->getJson('/api/questions/counts?status=published')->assertOk()->json();
        $this->assertSame(1, $published['C1']);
        $this->assertSame(1, $published['C2']);

        $both = $this->getJson('/api/questions/counts?view=both')->assertOk()->json();
        $this->assertSame(2, $both['all']['C1']);
        $this->assertSame(1, $both['published']['C1']);

        $questions = $this->getJson('/api/questions')->assertOk()->json();
        $this->assertCount(3, $questions);
    }
}
