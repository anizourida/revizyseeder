<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaiidaFilesResourceMemorySmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_files_resource_page_renders_within_128m_memory_limit(): void
    {
        ini_set('memory_limit', '128M');

        $records = [];

        for ($i = 1; $i <= 1200; $i++) {
            $records[] = [
                'filename' => sprintf('FR_N4_P1_SEM1_S%d.pptx', $i),
                'local_path' => null,
                'week_id' => null,
                'size_bytes' => 0,
                'vocab_count' => 0,
                'is_downloaded' => false,
                'is_integrity_checked' => false,
                'is_corrupt' => false,
                'is_vocab_extracted' => false,
                'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
                'download_progress' => 0,
                'download_error' => null,
                'downloaded_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($records, 300) as $chunk) {
            FileAsset::query()->insert($chunk);
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'filament-memory-smoke@example.com'],
            [
                'name' => 'Memory Smoke',
                'password' => bcrypt('Secret123!'),
                'role' => User::ROLE_OPERATOR,
            ]
        );

        for ($i = 0; $i < 20; $i++) {
            $this->actingAs($user)
                ->get('/admin/files')
                ->assertOk();
        }
    }
}
