<?php

namespace Tests\Feature\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\VocabularyItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class RevizySeederRepairP4VocabularyCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_exports_reports_without_mutating_rows(): void
    {
        $lessonId = 'FR_N4_P4_SEM2_S1';
        $root = sys_get_temp_dir() . '/vocab-repair-root-' . uniqid('', true);
        @mkdir($root . '/files', 0777, true);

        $relative = 'files/' . $lessonId . '.pptx';
        $absolute = $root . '/' . $relative;
        $this->createMinimalPptx($absolute);

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
        ]);

        $exportBase = sys_get_temp_dir() . '/seeder-vocab-repair-' . uniqid('', true);

        $this->artisan('revizyseeder:vocab:repair-p4', [
            '--dry-run' => true,
            '--export' => $exportBase,
        ])->assertExitCode(0);

        $item->refresh();
        $this->assertSame('Un bouquet', $item->word);

        $this->assertFileExists($exportBase . '.json');
        $this->assertFileExists($exportBase . '.csv');
    }

    private function createMinimalPptx(string $path): void
    {
        $directory = dirname($path);
        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $slideXml = <<<'XML'
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
          <a:p><a:r><a:t>Un bouquet</a:t></a:r></a:p>
          <a:p><a:r><a:t>de fleurs</a:t></a:r></a:p>
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

        $relsXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image42.png"/>
</Relationships>
XML;

        $zip = new ZipArchive();
        $opened = $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $this->assertTrue($opened === true);
        $this->assertTrue($zip->addFromString('ppt/slides/slide1.xml', $slideXml));
        $this->assertTrue($zip->addFromString('ppt/slides/_rels/slide1.xml.rels', $relsXml));
        $this->assertTrue($zip->addFromString('ppt/media/image42.png', 'fake-image-bytes'));
        $this->assertTrue($zip->close());
    }
}
