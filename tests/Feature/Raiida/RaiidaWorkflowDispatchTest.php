<?php

namespace Tests\Feature\Raiida;

use App\Jobs\Raiida\ExtractVocabularyJob;
use App\Jobs\Raiida\InspectFilesJob;
use App\Jobs\Raiida\SyncFilesJob;
use App\Models\Raiida\Audio;
use App\Models\Raiida\FileAsset;
use App\Models\Raiida\VocabularyItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RaiidaWorkflowDispatchTest extends TestCase
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
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_workflow_write_endpoints_require_authentication(): void
    {
        $this->postJson('/api/sync')->assertUnauthorized();
        $this->postJson('/api/inspect')->assertUnauthorized();
        $this->postJson('/api/extract-vocabulary')->assertUnauthorized();
        $this->postJson('/api/extract-vocabulary/1')->assertUnauthorized();
        $this->postJson('/api/sync-assets')->assertUnauthorized();
    }

    public function test_sync_endpoint_dispatches_sync_job(): void
    {
        Queue::fake();
        Sanctum::actingAs($this->user);

        $this->postJson('/api/sync')
            ->assertOk()
            ->assertJson(['message' => 'Sync started in background']);

        Queue::assertPushed(SyncFilesJob::class);
    }

    public function test_inspect_endpoint_dispatches_inspection_job(): void
    {
        Queue::fake();
        Sanctum::actingAs($this->user);

        $this->postJson('/api/inspect')
            ->assertOk()
            ->assertJson(['message' => 'Inspection started in background']);

        Queue::assertPushed(InspectFilesJob::class);
    }

    public function test_extract_vocabulary_endpoint_dispatches_job(): void
    {
        Queue::fake();
        Sanctum::actingAs($this->user);

        $this->postJson('/api/extract-vocabulary')
            ->assertOk()
            ->assertJson(['message' => 'Vocabulary extraction started in background']);

        Queue::assertPushed(ExtractVocabularyJob::class);
    }

    public function test_extract_single_file_returns_parity_shape(): void
    {
        Sanctum::actingAs($this->user);

        $fileAsset = FileAsset::query()->create([
            'id' => 1,
            'filename' => 'FR_N1_P1_SEM1_S1.pptx',
            'local_path' => 'nonexistent/path/file.pptx',
            'original_url' => 'https://example.com/file.pptx',
            'size_bytes' => 0,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'vocab_count' => 0,
        ]);

        $this->postJson('/api/extract-vocabulary/' . $fileAsset->id)
            ->assertOk()
            ->assertJson([
                'count' => 0,
                'lesson' => 'FR_N1_P1_SEM1_S1',
            ]);

        $this->postJson('/api/extract-vocabulary/999999')
            ->assertOk()
            ->assertJson([
                'error' => 'File not found',
            ]);
    }

    public function test_sync_assets_updates_vocabulary_audio_paths(): void
    {
        Sanctum::actingAs($this->user);

        $item = VocabularyItem::query()->create([
            'word' => 'Le chat',
            'image_path' => 'vocab_assets/chat.png',
            'audio_path' => null,
            'grade' => 'N1',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N1_P1_SEM1_S1',
            'ar_translation' => 'قط',
        ]);

        Audio::query()->create([
            'vocabulary_item_id' => $item->id,
            'text' => 'Le chat',
            'file_path' => 'chat.mp3',
        ]);

        $this->postJson('/api/sync-assets')
            ->assertOk()
            ->assertJson([
                'updated' => 1,
                'total' => 1,
            ]);

        $item->refresh();
        $this->assertSame('chat.mp3', $item->audio_path);
    }
}
