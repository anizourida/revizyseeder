<?php

namespace Tests\Feature\Raiida;

use App\Jobs\Raiida\DownloadFileAssetJob;
use App\Jobs\Raiida\SyncFilesJob;
use App\Models\Raiida\FileAsset;
use App\Models\Raiida\Grade;
use App\Models\Raiida\Period;
use App\Models\Raiida\Subject;
use App\Models\Raiida\Week;
use App\Services\Raiida\SyncFilesService;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RaiidaSyncFilesPipelineTest extends TestCase
{
    use RefreshDatabase;

    private string $filesRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesRoot = storage_path('app/testing-sync-pipeline');
        File::deleteDirectory($this->filesRoot);
        File::ensureDirectoryExists($this->filesRoot);

        config()->set('cache.default', 'array');
        config()->set('raiida.files_root', $this->filesRoot);
        config()->set('raiida.sync.metadata_url', 'https://meta.test/list');
        config()->set('raiida.sync.file_base_url', 'https://files.test');
        config()->set('raiida.sync.app_key', 'test-app-key');
        config()->set('raiida.sync.lock_key', 'tests:raiida:sync-lock-pipeline');
        config()->set('raiida.sync.lock_seconds', 300);
        config()->set('raiida.sync.download_batch_name', 'tests-fetch-downloads');
        config()->set('raiida.sync.download_batch_chunk_size', 100);
        config()->set('raiida.sync.file_lock_seconds', 300);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->filesRoot);

        parent::tearDown();
    }

    public function test_sync_job_dispatches_batched_download_jobs_after_metadata_sync(): void
    {
        Bus::fake();

        Http::fake([
            'https://meta.test/list' => Http::response([
                [
                    '_id' => 'asset-1',
                    'matiere' => 'FR',
                    'niveau' => 4,
                    'periode' => 1,
                    'semaine' => 2,
                    'file' => 'uploads/pptx/FR_N4_P1_SEM2_S1.pptx',
                    'updatedAt' => '2026-02-26T08:00:00.000Z',
                ],
            ], 200),
        ]);

        $job = new SyncFilesJob('ctx-1', 1, 'admin@example.com', 'admin');
        $job->handle(app(SyncFilesService::class));

        $asset = FileAsset::query()->sole();
        $this->assertSame('FR_N4_P1_SEM2_S1.pptx', $asset->filename);
        $this->assertFalse($asset->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_PENDING, $asset->download_state);

        Http::assertSentCount(1);

        Bus::assertBatched(function (PendingBatch $batch): bool {
            return str_starts_with((string) $batch->name, 'tests-fetch-downloads')
                && $batch->jobs->count() <= 1;
        });
    }

    public function test_download_file_asset_job_downloads_one_file_and_marks_it_downloaded(): void
    {
        Http::fake([
            'https://files.test/*' => Http::response(str_repeat('a', 2048), 200),
        ]);

        $grade = Grade::query()->create(['name' => '4', 'code' => 'N4']);
        $subject = Subject::query()->create(['name' => 'FR', 'code' => 'FR', 'grade_id' => $grade->id]);
        $period = Period::query()->create(['name' => '1', 'code' => '1', 'subject_id' => $subject->id]);
        $week = Week::query()->create(['name' => '2', 'code' => '2', 'period_id' => $period->id]);

        $filename = 'FR_N4_P1_SEM2_S1.pptx';
        $relativePath = 'FR/niveau_4/periode_1/semaine_2/' . $filename;

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
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'download_started_at' => null,
            'download_finished_at' => null,
        ]);

        $job = new DownloadFileAssetJob($asset->id, 'ctx-2', 1, 'admin@example.com', 'admin');
        $job->handle(app(SyncFilesService::class));

        $asset->refresh();
        $this->assertTrue($asset->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_DOWNLOADED, $asset->download_state);
        $this->assertSame(100, $asset->download_progress);
        $this->assertGreaterThan(0, (int) $asset->size_bytes);
        $this->assertFileExists($this->filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
    }

    public function test_download_file_asset_job_does_not_throw_for_permanent_http_404_failure(): void
    {
        Http::fake([
            'https://files.test/*' => Http::response('', 404),
        ]);

        $grade = Grade::query()->create(['name' => '4', 'code' => 'N4']);
        $subject = Subject::query()->create(['name' => 'FR', 'code' => 'FR', 'grade_id' => $grade->id]);
        $period = Period::query()->create(['name' => '1', 'code' => '1', 'subject_id' => $subject->id]);
        $week = Week::query()->create(['name' => '2', 'code' => '2', 'period_id' => $period->id]);

        $filename = 'FR_N4_P2_SEM6_S2.pptx';
        $relativePath = 'FR/niveau_4/periode_2/semaine_6/' . $filename;

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
            'session_id' => 'S2',
            'vocab_count' => 0,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'download_started_at' => null,
            'download_finished_at' => null,
        ]);

        $job = new DownloadFileAssetJob($asset->id, 'ctx-3', 1, 'admin@example.com', 'admin');
        $job->handle(app(SyncFilesService::class));

        $asset->refresh();
        $this->assertFalse($asset->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_FAILED, $asset->download_state);
        $this->assertSame('HTTP 404', $asset->download_error);
    }
}
