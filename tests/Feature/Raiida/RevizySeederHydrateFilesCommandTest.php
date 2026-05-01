<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class RevizySeederHydrateFilesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_hydrate_files_command_copies_local_files_and_marks_downloaded(): void
    {
        $targetRoot = storage_path('app/testing-hydrate-target');
        $sourceRoot = storage_path('app/testing-hydrate-source');

        File::deleteDirectory($targetRoot);
        File::deleteDirectory($sourceRoot);
        File::ensureDirectoryExists($targetRoot);
        File::ensureDirectoryExists($sourceRoot);

        config()->set('raiida.files_root', $targetRoot);

        $existingRelativePath = 'FR/niveau_4/periode_1/semaine_1/FR_N4_P1_SEM1_S1.ppsx';
        $existingSourcePath = $sourceRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $existingRelativePath);
        File::ensureDirectoryExists(dirname($existingSourcePath));
        File::put($existingSourcePath, str_repeat('x', 1024 * 1024));

        $missingRelativePath = 'FR/niveau_4/periode_1/semaine_1/FR_N4_P1_SEM1_S2.ppsx';

        $downloadable = FileAsset::query()->create([
            'filename' => 'FR_N4_P1_SEM1_S1.ppsx',
            'local_path' => $existingRelativePath,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $missing = FileAsset::query()->create([
            'filename' => 'FR_N4_P1_SEM1_S2.ppsx',
            'local_path' => $missingRelativePath,
            'is_downloaded' => false,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_PENDING,
            'download_progress' => 0,
            'download_error' => null,
            'size_bytes' => 0,
            'vocab_count' => 0,
        ]);

        $this->artisan('revizyseeder:hydrate-files', [
            '--source' => $sourceRoot,
            '--mode' => 'copy',
        ])->assertExitCode(0);

        $downloadable->refresh();
        $missing->refresh();

        $expectedTargetPath = $targetRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $existingRelativePath);
        $this->assertTrue(is_file($expectedTargetPath));
        $this->assertTrue($downloadable->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_DOWNLOADED, $downloadable->download_state);
        $this->assertSame(100, $downloadable->download_progress);
        $this->assertGreaterThan(0, $downloadable->size_bytes);

        $this->assertFalse($missing->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_PENDING, $missing->download_state);
        $this->assertSame(0, $missing->size_bytes);

        File::deleteDirectory($targetRoot);
        File::deleteDirectory($sourceRoot);
    }
}
