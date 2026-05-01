<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Period;
use App\Models\Raiida\Subject;
use App\Models\Raiida\Week;
use App\Services\Raiida\SyncFilesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RaiidaSyncFilesServiceTest extends TestCase
{
    use RefreshDatabase;

    private string $filesRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesRoot = storage_path('app/testing-sync-files');
        File::deleteDirectory($this->filesRoot);
        File::ensureDirectoryExists($this->filesRoot);

        config()->set('cache.default', 'array');
        config()->set('raiida.files_root', $this->filesRoot);
        config()->set('raiida.sync.metadata_url', 'https://meta.test/list');
        config()->set('raiida.sync.file_base_url', 'https://files.test');
        config()->set('raiida.sync.app_key', 'test-app-key');
        config()->set('raiida.sync.lock_key', 'tests:raiida:sync-lock');
        config()->set('raiida.sync.lock_seconds', 300);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->filesRoot);

        parent::tearDown();
    }

    public function test_run_deduplicates_metadata_and_keeps_ppt_filename(): void
    {
        $filename = 'legacy_slide.ppt';
        $relativePath = 'AMAZIGHYA/niveau_1/periode_2/semaine_1/' . $filename;
        $absolutePath = $this->filesRoot . DIRECTORY_SEPARATOR . $relativePath;

        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('x', 1024));

        Http::fake([
            'https://meta.test/list' => Http::response([
                [
                    '_id' => 'legacy-1',
                    'matiere' => 'AMAZIGHYA',
                    'niveau' => 1,
                    'periode' => 2,
                    'semaine' => 1,
                    'file' => 'uploads/pptx/' . $filename,
                    'updatedAt' => '2026-02-20T10:00:00.000Z',
                ],
                [
                    '_id' => 'legacy-2',
                    'matiere' => 'AMAZIGHYA',
                    'niveau' => 1,
                    'periode' => 2,
                    'semaine' => 1,
                    'file' => 'uploads/pptx/' . $filename,
                    'updatedAt' => '2026-02-20T10:05:00.000Z',
                ],
            ], 200),
        ]);

        $summary = app(SyncFilesService::class)->run();

        $this->assertSame(2, $summary['raw_total']);
        $this->assertSame(1, $summary['total']);
        $this->assertSame(1, $summary['duplicates']);
        $this->assertSame(1, $summary['processed']);
        $this->assertSame(1, $summary['skipped']);
        $this->assertFalse((bool) $summary['locked']);

        $asset = FileAsset::query()->sole();

        $this->assertSame($filename, $asset->filename);
        $this->assertSame($relativePath, $asset->local_path);
        $this->assertSame('https://files.test/' . $filename, $asset->original_url);
        $this->assertTrue($asset->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_DOWNLOADED, $asset->download_state);
    }

    public function test_run_recovers_stale_downloading_asset_when_partial_file_exists_on_disk(): void
    {
        Http::fake([
            'https://meta.test/list' => Http::response([], 200),
        ]);

        $grade = Grade::query()->create(['name' => '5', 'code' => 'N5']);
        $subject = Subject::query()->create(['name' => 'AR', 'code' => 'AR', 'grade_id' => $grade->id]);
        $period = Period::query()->create(['name' => '1', 'code' => '1', 'subject_id' => $subject->id]);
        $week = Week::query()->create(['name' => '1', 'code' => '1', 'period_id' => $period->id]);

        $filename = 'AR_N5_P1_SEM1_S6.pptx';
        $relativePath = 'AR/niveau_5/periode_1/semaine_1/' . $filename;
        $absolutePath = $this->filesRoot . DIRECTORY_SEPARATOR . $relativePath;
        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('x', 2048));

        $asset = FileAsset::query()->create([
            'week_id' => $week->id,
            'filename' => $filename,
            'local_path' => $relativePath,
            'original_url' => 'https://files.test/' . $filename,
            'size_bytes' => 4096,
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'session_id' => 'S6',
            'vocab_count' => 0,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADING,
            'download_progress' => 35,
            'download_error' => null,
            'download_started_at' => now()->subMinutes(10),
            'download_finished_at' => null,
        ]);

        $summary = app(SyncFilesService::class)->run();
        $asset->refresh();

        $this->assertSame(0, $summary['raw_total']);
        $this->assertSame(0, $summary['total']);
        $this->assertSame(0, $summary['processed']);
        $this->assertSame(0, $summary['failed']);
        $this->assertFalse($asset->is_downloaded);
        $this->assertSame(0, (int) $asset->size_bytes);
        $this->assertNull($asset->downloaded_at);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_PENDING, $asset->download_state);
        $this->assertSame(35, $asset->download_progress);
        $this->assertSame('Recovered interrupted download. Will continue on next fetch.', $asset->download_error);
    }

    public function test_download_existing_asset_restarts_interrupted_partial_file(): void
    {
        Http::fake([
            'https://files.test/*' => Http::response(str_repeat('z', 4096), 200),
        ]);

        $grade = Grade::query()->create(['name' => '4', 'code' => 'N4']);
        $subject = Subject::query()->create(['name' => 'FR', 'code' => 'FR', 'grade_id' => $grade->id]);
        $period = Period::query()->create(['name' => '1', 'code' => '1', 'subject_id' => $subject->id]);
        $week = Week::query()->create(['name' => '1', 'code' => '1', 'period_id' => $period->id]);

        $filename = 'FR_N4_P1_SEM1_S1.pptx';
        $relativePath = 'FR/niveau_4/periode_1/semaine_1/' . $filename;
        $absolutePath = $this->filesRoot . DIRECTORY_SEPARATOR . $relativePath;
        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('x', 1024));

        $asset = FileAsset::query()->create([
            'week_id' => $week->id,
            'filename' => $filename,
            'local_path' => $relativePath,
            'original_url' => 'https://files.test/' . $filename,
            'size_bytes' => 0,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'session_id' => 'S1',
            'vocab_count' => 0,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADING,
            'download_progress' => 50,
            'download_error' => null,
            'download_started_at' => now()->subMinutes(2),
            'download_finished_at' => null,
        ]);

        $status = app(SyncFilesService::class)->downloadExistingAsset($asset);
        $asset->refresh();

        $this->assertSame('downloaded', $status);
        $this->assertTrue($asset->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_DOWNLOADED, $asset->download_state);
        $this->assertSame(100, $asset->download_progress);
        $this->assertSame(4096, (int) $asset->size_bytes);
        $this->assertFileExists($absolutePath);
        $this->assertSame(4096, filesize($absolutePath));
    }

    public function test_download_existing_asset_marks_failed_and_clears_downloaded_flags_when_restart_fails(): void
    {
        Http::fake([
            'https://files.test/*' => Http::response('nope', 500),
        ]);

        $grade = Grade::query()->create(['name' => '4', 'code' => 'N4']);
        $subject = Subject::query()->create(['name' => 'FR', 'code' => 'FR', 'grade_id' => $grade->id]);
        $period = Period::query()->create(['name' => '1', 'code' => '1', 'subject_id' => $subject->id]);
        $week = Week::query()->create(['name' => '1', 'code' => '1', 'period_id' => $period->id]);

        $filename = 'FR_N4_P1_SEM1_S2.pptx';
        $relativePath = 'FR/niveau_4/periode_1/semaine_1/' . $filename;
        $absolutePath = $this->filesRoot . DIRECTORY_SEPARATOR . $relativePath;
        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('y', 1024));

        $asset = FileAsset::query()->create([
            'week_id' => $week->id,
            'filename' => $filename,
            'local_path' => $relativePath,
            'original_url' => 'https://files.test/' . $filename,
            'size_bytes' => 9999,
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'session_id' => 'S2',
            'vocab_count' => 0,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADING,
            'download_progress' => 73,
            'download_error' => null,
            'download_started_at' => now()->subMinutes(2),
            'download_finished_at' => null,
            'downloaded_at' => now()->subDay(),
        ]);

        $status = app(SyncFilesService::class)->downloadExistingAsset($asset);
        $asset->refresh();

        $this->assertSame('failed', $status);
        $this->assertFalse($asset->is_downloaded);
        $this->assertSame(0, (int) $asset->size_bytes);
        $this->assertNull($asset->downloaded_at);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_FAILED, $asset->download_state);
        $this->assertSame('HTTP 500', $asset->download_error);
        $this->assertFileDoesNotExist($absolutePath);
    }
}
