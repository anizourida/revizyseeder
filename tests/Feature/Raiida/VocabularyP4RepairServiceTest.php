<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\VocabularyP4RepairService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class VocabularyP4RepairServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_map_reconstructs_multiline_word_from_source_pptx(): void
    {
        $lessonId = 'FR_N4_P4_SEM2_S1';
        $root = sys_get_temp_dir() . '/vocab-repair-root-' . uniqid('', true);
        @mkdir($root . '/files', 0777, true);

        $relative = 'files/' . $lessonId . '.pptx';
        $absolute = $root . '/' . $relative;
        $this->createMinimalPptx($absolute, 'Un bouquet', 'de fleurs', 'image42.png');

        config()->set('raiida.files_root', $root);

        FileAsset::query()->create([
            'filename' => $lessonId . '.pptx',
            'local_path' => $relative,
            'is_downloaded' => true,
            'session_id' => 'S1',
        ]);

        $item = VocabularyItem::query()->create([
            'word' => 'Un bouquet',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P4',
            'week' => 'SEM2',
            'lesson_id' => $lessonId,
            'image_path' => 'vocab_assets/' . $lessonId . '/image42.png',
            'concept_id' => '976',
            'flashcard_id' => '1215',
            'revizy_audio_file_id' => 'aud-secret-1',
        ]);

        $map = app(VocabularyP4RepairService::class)->buildCorrectionMap();

        $target = collect($map['rows'])
            ->firstWhere('vocabulary_item_id', $item->id);

        $this->assertIsArray($target);
        $this->assertSame('Un bouquet', $target['old_word']);
        $this->assertSame('Un bouquet de fleurs', $target['new_word']);
        $this->assertTrue((bool) $target['changed']);
        $this->assertSame([], $target['ambiguity_flags']);
    }

    public function test_apply_map_merges_collision_and_preserves_external_ids(): void
    {
        $lessonId = 'FR_N4_P4_SEM2_S1';

        $losingDuplicate = VocabularyItem::query()->create([
            'word' => 'Un bouquet de fleurs',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P4',
            'week' => 'SEM2',
            'lesson_id' => $lessonId,
            'image_path' => 'vocab_assets/' . $lessonId . '/image42.png',
            'audio_path' => 'legacy.wav',
        ]);

        $item = VocabularyItem::query()->create([
            'word' => 'Un bouquet',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P4',
            'week' => 'SEM2',
            'lesson_id' => $lessonId,
            'image_path' => 'vocab_assets/' . $lessonId . '/image42.png',
            'concept_id' => '976',
            'flashcard_id' => '1215',
            'revizy_audio_file_id' => 'aud-secret-1',
        ]);

        $summary = app(VocabularyP4RepairService::class)->applyCorrectionMap([
            [
                'lesson_id' => $lessonId,
                'vocabulary_item_id' => $item->id,
                'old_word' => 'Un bouquet',
                'new_word' => 'Un bouquet de fleurs',
                'changed' => true,
                'ambiguity_flags' => [],
            ],
        ], [
            'sync_audio' => false,
            'queue_translations' => false,
        ]);

        $this->assertSame(1, (int) $summary['applied_rows']);
        $this->assertSame(1, (int) $summary['collision_merges']);
        $this->assertSame(1, (int) $summary['deleted_duplicate_rows']);

        $item->refresh();
        $this->assertSame('Un bouquet de fleurs', (string) $item->word);
        $this->assertSame('976', (string) $item->concept_id);
        $this->assertSame('1215', (string) $item->flashcard_id);
        $this->assertSame('aud-secret-1', (string) $item->revizy_audio_file_id);

        $this->assertDatabaseMissing('vocabulary_items', [
            'id' => $losingDuplicate->id,
        ]);
    }

    public function test_build_map_is_deterministic_for_same_dataset(): void
    {
        $lessonId = 'FR_N4_P4_SEM2_S1';
        $root = sys_get_temp_dir() . '/vocab-repair-root-' . uniqid('', true);
        @mkdir($root . '/files', 0777, true);

        $relative = 'files/' . $lessonId . '.pptx';
        $absolute = $root . '/' . $relative;
        $this->createMinimalPptx($absolute, 'Un bouquet', 'de fleurs', 'image42.png');

        config()->set('raiida.files_root', $root);

        FileAsset::query()->create([
            'filename' => $lessonId . '.pptx',
            'local_path' => $relative,
            'is_downloaded' => true,
            'session_id' => 'S1',
        ]);

        VocabularyItem::query()->create([
            'word' => 'Un bouquet',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P4',
            'week' => 'SEM2',
            'lesson_id' => $lessonId,
            'image_path' => 'vocab_assets/' . $lessonId . '/image42.png',
        ]);

        $service = app(VocabularyP4RepairService::class);
        $first = $service->buildCorrectionMap();
        $second = $service->buildCorrectionMap();

        $this->assertSame($first['summary'], $second['summary']);
        $this->assertSame($first['rows'], $second['rows']);
        $this->assertSame($first['lessons'], $second['lessons']);
    }

    private function createMinimalPptx(string $path, string $lineOne, string $lineTwo, string $imageName): void
    {
        $directory = dirname($path);
        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $slideXml = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
       xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"
       xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <p:cSld>
    <p:spTree>
      <p:sp>
        <p:nvSpPr><p:cNvPr id="1" name="Marker"/></p:nvSpPr>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p><a:r><a:t>Qui veut répéter ?</a:t></a:r></a:p>
        </p:txBody>
      </p:sp>
      <p:sp>
        <p:nvSpPr><p:cNvPr id="2" name="Word"/></p:nvSpPr>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p><a:r><a:t>{$lineOne}</a:t></a:r></a:p>
          <a:p><a:r><a:t>{$lineTwo}</a:t></a:r></a:p>
        </p:txBody>
      </p:sp>
      <p:pic>
        <p:nvPicPr>
          <p:cNvPr id="3" name="Picture"/>
          <p:cNvPicPr/>
          <p:nvPr/>
        </p:nvPicPr>
        <p:blipFill>
          <a:blip r:embed="rId1"/>
        </p:blipFill>
        <p:spPr>
          <a:xfrm>
            <a:off x="0" y="0"/>
            <a:ext cx="9144000" cy="6858000"/>
          </a:xfrm>
        </p:spPr>
      </p:pic>
    </p:spTree>
  </p:cSld>
</p:sld>
XML;

        $relsXml = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/{$imageName}"/>
</Relationships>
XML;

        $zip = new ZipArchive();
        $opened = $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $this->assertTrue($opened === true);
        $this->assertTrue($zip->addFromString('ppt/slides/slide1.xml', $slideXml));
        $this->assertTrue($zip->addFromString('ppt/slides/_rels/slide1.xml.rels', $relsXml));
        $this->assertTrue($zip->addFromString('ppt/media/' . $imageName, 'fake-image-bytes'));
        $this->assertTrue($zip->close());
    }
}
