<?php

namespace Tests\Feature\Raiida;

use App\Jobs\Raiida\DownloadFileAssetJob;
use App\Jobs\Raiida\ExtractPresentationDataJob;
use App\Models\Raiida\FileAsset;
use App\Services\Raiida\PresentationDataExtractionService;
use App\Services\Raiida\SyncFilesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class RevizySeederExtractPresentationDataCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $filesRoot;

    private string $outputRoot;

    private string $fakeExtractorScriptPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesRoot = storage_path('app/testing-presentation-files');
        $this->outputRoot = storage_path('app/testing-presentation-data');
        $this->fakeExtractorScriptPath = storage_path('app/testing-presentation-extractor.php');

        File::deleteDirectory($this->filesRoot);
        File::deleteDirectory($this->outputRoot);
        File::delete($this->fakeExtractorScriptPath);

        File::ensureDirectoryExists($this->filesRoot);
        File::ensureDirectoryExists($this->outputRoot);

        config()->set('raiida.files_root', $this->filesRoot);
        config()->set('raiida.presentation_data.python_bin', PHP_BINARY);
        config()->set('raiida.presentation_data.script_path', $this->fakeExtractorScriptPath);
        config()->set('raiida.presentation_data.output_root', $this->outputRoot);
        config()->set('raiida.presentation_data.process_timeout_seconds', 60);
        config()->set('raiida.presentation_data.queue', 'revizyseeder-workflows');

        $this->writeFakeExtractorScript();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->filesRoot);
        File::deleteDirectory($this->outputRoot);
        File::delete($this->fakeExtractorScriptPath);

        parent::tearDown();
    }

    public function test_extract_presentation_data_command_processes_downloaded_assets_inline(): void
    {
        $asset = $this->createDownloadedAsset('FR_N4_P1_SEM2_S1.pptx');

        $this->artisan('revizyseeder:extract-presentation-data', [
            '--id' => [$asset->id],
        ])->assertExitCode(0);

        $asset->refresh();

        $this->assertTrue($asset->is_presentation_data_extracted);
        $this->assertSame(2, $asset->presentation_slide_count);
        $this->assertNotNull($asset->presentation_json_path);
        $this->assertNotNull($asset->presentation_assets_dir);
        $this->assertNotNull($asset->presentation_extracted_at);

        $jsonPath = base_path((string) $asset->presentation_json_path);
        $this->assertFileExists($jsonPath);

        $payload = json_decode((string) file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(pathinfo($asset->filename, PATHINFO_FILENAME), $payload['lesson_id']);
        $this->assertCount(2, $payload['slides']);
    }

    public function test_extract_presentation_data_command_can_dispatch_queue_jobs(): void
    {
        Queue::fake();

        $this->createDownloadedAsset('FR_N4_P1_SEM3_S1.pptx');
        $this->createDownloadedAsset('FR_N4_P1_SEM4_S1.pptx');

        $this->artisan('revizyseeder:extract-presentation-data', [
            '--queue' => true,
            '--limit' => 1,
        ])->assertExitCode(0);

        Queue::assertPushed(ExtractPresentationDataJob::class, 1);
    }

    public function test_extract_presentation_data_command_skips_legacy_ppt_files(): void
    {
        Queue::fake();

        $this->createDownloadedAsset('FR_N4_P1_SEM3_S1.pptx');
        $this->createDownloadedAsset('FR_N4_P1_SEM4_S1.ppt');

        $this->artisan('revizyseeder:extract-presentation-data', [
            '--queue' => true,
        ])->assertExitCode(0);

        Queue::assertPushed(ExtractPresentationDataJob::class, 1);
    }

    public function test_download_job_dispatches_presentation_extraction_after_successful_download(): void
    {
        Queue::fake();

        config()->set('raiida.presentation_data.auto_extract_after_download', true);
        config()->set('raiida.sync.app_key', 'test-app-key');

        Http::fake([
            'https://files.test/*' => Http::response(str_repeat('x', 4096), 200),
        ]);

        $asset = FileAsset::query()->create([
            'filename' => 'FR_N4_P1_SEM5_S1.pptx',
            'local_path' => 'FR/niveau_4/periode_1/semaine_5/FR_N4_P1_SEM5_S1.pptx',
            'original_url' => 'https://files.test/FR_N4_P1_SEM5_S1.pptx',
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

        $job = new DownloadFileAssetJob($asset->id, 'ctx-1', 1, 'admin@example.com', 'admin');
        $job->handle(app(SyncFilesService::class));

        $asset->refresh();
        $this->assertTrue($asset->is_downloaded);
        $this->assertSame(FileAsset::DOWNLOAD_STATE_DOWNLOADED, $asset->download_state);

        Queue::assertPushed(ExtractPresentationDataJob::class, function (ExtractPresentationDataJob $queuedJob) use ($asset): bool {
            return $queuedJob->fileAssetId === $asset->id
                && $queuedJob->force === false
                && $queuedJob->workflowContextId === 'ctx-1';
        });
    }

    public function test_extract_presentation_job_does_not_throw_for_permanent_file_errors(): void
    {
        $asset = $this->createDownloadedAsset('FR_N4_P1_SEM6_S1.pptx');

        $service = \Mockery::mock(PresentationDataExtractionService::class);
        $service->shouldReceive('extractFromFileAsset')
            ->once()
            ->withArgs(function (FileAsset $inputAsset, bool $force): bool {
                return $inputAsset->id > 0 && $force === false;
            })
            ->andThrow(new RuntimeException(
                "Extraction failed: Could not open presentation: Package not found at '/tmp/temp_processing.pptx'"
            ));

        $job = new ExtractPresentationDataJob($asset->id, false);

        $job->handle($service);

        $this->assertTrue(true);
    }

    private function createDownloadedAsset(string $filename): FileAsset
    {
        $relativePath = 'FR/niveau_4/periode_1/semaine_2/' . $filename;
        $absolutePath = $this->filesRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, str_repeat('a', 2048));

        return FileAsset::query()->create([
            'filename' => $filename,
            'local_path' => $relativePath,
            'original_url' => 'https://files.test/' . $filename,
            'size_bytes' => (int) filesize($absolutePath),
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'session_id' => 'S1',
            'vocab_count' => 0,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
            'download_progress' => 100,
            'download_error' => null,
            'download_started_at' => now(),
            'download_finished_at' => now(),
            'downloaded_at' => now(),
        ]);
    }

    private function writeFakeExtractorScript(): void
    {
        $script = <<<'PHP'
<?php

$options = getopt('', ['input:', 'lesson-id:', 'output-root:']);
$input = (string) ($options['input'] ?? '');
$lessonId = (string) ($options['lesson-id'] ?? pathinfo($input, PATHINFO_FILENAME));
$outputRoot = rtrim((string) ($options['output-root'] ?? ''), DIRECTORY_SEPARATOR);

if ($input === '' || ! is_file($input)) {
    fwrite(STDERR, "Input file not found\n");
    exit(2);
}

if ($lessonId === '' || $outputRoot === '') {
    fwrite(STDERR, "Missing required args\n");
    exit(3);
}

$lessonDir = $outputRoot . DIRECTORY_SEPARATOR . $lessonId;
$assetsDir = $lessonDir . DIRECTORY_SEPARATOR . 'assets';
if (! is_dir($assetsDir) && ! mkdir($assetsDir, 0777, true) && ! is_dir($assetsDir)) {
    fwrite(STDERR, "Cannot create assets dir\n");
    exit(4);
}

file_put_contents($assetsDir . DIRECTORY_SEPARATOR . 'slide_1_image_1.png', 'image-bytes');
file_put_contents($assetsDir . DIRECTORY_SEPARATOR . 'slide_2_video_1.mp4', 'video-bytes');

$data = [
    'file_name' => basename($input),
    'lesson_id' => $lessonId,
    'metadata' => ['total_slides' => 2],
    'slides' => [
        [
            'id' => 1,
            'elements' => [
                ['type' => 'text', 'content' => 'Bonjour', 'bbox' => [0, 0, 100, 40]],
                ['type' => 'image', 'file_path' => 'assets/slide_1_image_1.png', 'bbox' => [10, 20, 120, 90], 'description' => 'Image 1'],
            ],
        ],
        [
            'id' => 2,
            'elements' => [
                ['type' => 'video', 'file_path' => 'assets/slide_2_video_1.mp4', 'bbox' => [15, 25, 140, 100], 'description' => 'Video 1'],
            ],
        ],
    ],
];

file_put_contents($lessonDir . DIRECTORY_SEPARATOR . 'data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$summary = [
    'lesson_id' => $lessonId,
    'json_path' => $lessonDir . DIRECTORY_SEPARATOR . 'data.json',
    'assets_dir' => $assetsDir,
    'total_slides' => 2,
    'images' => 1,
    'videos' => 1,
];

echo json_encode($summary, JSON_UNESCAPED_UNICODE) . PHP_EOL;

PHP;

        File::put($this->fakeExtractorScriptPath, $script);
    }
}
