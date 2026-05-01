<?php

namespace Tests\Feature\Raiida;

use App\Jobs\Raiida\ExtractConjugaisonLessonsJob;
use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\ConjugaisonGrade;
use App\Models\Raiida\ConjugaisonPeriod;
use App\Models\Raiida\ConjugaisonWeek;
use App\Models\Raiida\FileAsset;
use App\Services\Raiida\ConjugaisonExtractionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ConjugaisonExtractionPipelineTest extends TestCase
{
    use RefreshDatabase;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('app/testing-conjugaison-extract');
        File::deleteDirectory($this->root);
        File::ensureDirectoryExists($this->root);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_service_extracts_best_conjugaison_candidate_per_week_and_seeds_references(): void
    {
        $lessonPrimary = 'FR_N4_P3_SEM3_S2';
        $lessonSecondary = 'FR_N4_P3_SEM3_S3';

        $jsonPrimary = $this->writePresentationJson($lessonPrimary, [
            [
                'id' => 6,
                'elements' => [
                    ['type' => 'text', 'content' => 'Écrivez sur vos ardoises la bonne forme du verbe aimer au présent.'],
                    ['type' => 'text', 'content' => 'Utiliser le verbe aimer au présent.'],
                    ['type' => 'text', 'content' => 'Nous aimons la lecture en classe.'],
                ],
            ],
        ]);

        $jsonSecondary = $this->writePresentationJson($lessonSecondary, [
            [
                'id' => 18,
                'elements' => [
                    ['type' => 'text', 'content' => 'Qui veut compléter la phrase avec la bonne forme du verbe aimer au présent ? Levez la main.'],
                    ['type' => 'text', 'content' => 'Nous aimons la musique à la maison.'],
                ],
            ],
        ]);

        FileAsset::query()->create([
            'filename' => $lessonPrimary . '.pptx',
            'presentation_json_path' => $jsonPrimary,
            'is_presentation_data_extracted' => true,
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
            'download_progress' => 100,
            'size_bytes' => 123,
            'vocab_count' => 0,
        ]);

        FileAsset::query()->create([
            'filename' => $lessonSecondary . '.pptx',
            'presentation_json_path' => $jsonSecondary,
            'is_presentation_data_extracted' => true,
            'is_downloaded' => true,
            'is_integrity_checked' => false,
            'is_corrupt' => false,
            'is_vocab_extracted' => false,
            'download_state' => FileAsset::DOWNLOAD_STATE_DOWNLOADED,
            'download_progress' => 100,
            'size_bytes' => 123,
            'vocab_count' => 0,
        ]);

        $summary = app(ConjugaisonExtractionService::class)->run(true);

        $this->assertSame(2, $summary['assets_scanned']);
        $this->assertSame(1, $summary['persisted']);
        $this->assertSame(180, $summary['coverage_total']);
        $this->assertSame(180, $summary['placeholders_created']);
        $this->assertSame(180, Conjugaison::query()->count());

        $this->assertSame(6, ConjugaisonGrade::query()->count());
        $this->assertSame(5, ConjugaisonPeriod::query()->count());
        $this->assertSame(6, ConjugaisonWeek::query()->count());

        $row = Conjugaison::query()->where('n', 'N4')->where('p', 'P3')->where('sem', 'SEM3')->first();
        $this->assertNotNull($row);
        $this->assertSame('Utiliser le verbe aimer au présent.', $row->name);
        $this->assertSame('Écrivez sur vos ardoises la bonne forme du verbe aimer au présent.', $row->question);
        $this->assertSame('aimer', $row->verbe);
        $this->assertSame('présent', $row->tense);
        $this->assertSame($lessonPrimary, $row->source_lesson_id);
        $this->assertNotNull($row->grade_id);
        $this->assertNotNull($row->period_id);
        $this->assertNotNull($row->week_id);
        $this->assertStringContainsString('Nous aimons la lecture en classe.', (string) $row->raw_data);

        $relatedRawData = json_decode((string) $row->related_raw_data, true);
        $this->assertIsArray($relatedRawData);
        $topExamples = is_array($relatedRawData['top_examples'] ?? null) ? $relatedRawData['top_examples'] : [];
        $sentences = array_values(array_filter(array_map(static fn (array $item): string => (string) ($item['sentence'] ?? ''), $topExamples)));
        $this->assertContains('Nous aimons la lecture en classe.', $sentences);
        $this->assertNotContains('Nous aimons la musique à la maison.', $sentences);
        $firstExample = is_array($topExamples[0] ?? null) ? $topExamples[0] : [];
        $this->assertStringContainsString('/admin/files/preview/', (string) ($firstExample['source_preview_url'] ?? ''));
        $this->assertStringContainsString('#slide-', (string) ($firstExample['source_slide_preview_url'] ?? ''));

        $placeholder = Conjugaison::query()->where('n', 'N1')->where('p', 'P1')->where('sem', 'SEM1')->first();
        $this->assertNotNull($placeholder);
        $this->assertSame('', $placeholder->name);
        $this->assertSame('', $placeholder->question);
    }

    public function test_command_can_dispatch_extraction_job_to_queue(): void
    {
        Queue::fake();

        $this->artisan('revizyseeder:extract-conjugaison', [
            '--queue' => true,
        ])->assertExitCode(0);

        Queue::assertPushed(ExtractConjugaisonLessonsJob::class, 1);
    }

    private function writePresentationJson(string $lessonId, array $slides): string
    {
        $lessonDir = $this->root . DIRECTORY_SEPARATOR . $lessonId;
        File::ensureDirectoryExists($lessonDir . DIRECTORY_SEPARATOR . 'assets');

        $jsonPath = $lessonDir . DIRECTORY_SEPARATOR . 'data.json';
        File::put($jsonPath, json_encode([
            'file_name' => $lessonId . '.pptx',
            'lesson_id' => $lessonId,
            'metadata' => [
                'total_slides' => count($slides),
                'slide_width_emu' => 12192000,
                'slide_height_emu' => 6858000,
            ],
            'slides' => $slides,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $jsonPath;
    }
}
