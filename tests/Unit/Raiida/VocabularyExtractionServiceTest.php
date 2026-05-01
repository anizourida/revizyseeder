<?php

namespace Tests\Unit\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\VocabularyExtractionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;
use ZipArchive;

class VocabularyExtractionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_extract_slide_texts_reconstructs_split_text_runs(): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'vocab-extract-');
        $this->assertIsString($zipPath);

        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
       xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main">
  <p:cSld>
    <p:spTree>
      <p:sp>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p><a:r><a:t>Qui veut répéter ?</a:t></a:r></a:p>
        </p:txBody>
      </p:sp>
      <p:sp>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p>
            <a:r><a:t>Un</a:t></a:r>
            <a:r><a:t>e étoile</a:t></a:r>
          </a:p>
        </p:txBody>
      </p:sp>
      <p:sp>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p>
            <a:r><a:t>L</a:t></a:r>
            <a:r><a:t>’oncle</a:t></a:r>
          </a:p>
        </p:txBody>
      </p:sp>
      <p:sp>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p>
            <a:r><a:t>La</a:t></a:r>
            <a:r><a:t> 1</a:t></a:r>
            <a:r><a:t>re</a:t></a:r>
            <a:r><a:t> année</a:t></a:r>
          </a:p>
        </p:txBody>
      </p:sp>
      <p:sp>
        <p:txBody>
          <a:bodyPr/>
          <a:lstStyle/>
          <a:p>
            <a:r><a:t>Le</a:t></a:r>
            <a:r><a:t> sucre</a:t></a:r>
          </a:p>
        </p:txBody>
      </p:sp>
    </p:spTree>
  </p:cSld>
</p:sld>
XML;

        $writer = new ZipArchive();
        $this->assertTrue($writer->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $this->assertTrue($writer->addFromString('ppt/slides/slide1.xml', $xml));
        $this->assertTrue($writer->close());

        $reader = new ZipArchive();
        $this->assertTrue($reader->open($zipPath));

        $service = $this->app->make(VocabularyExtractionService::class);
        $method = new ReflectionMethod($service, 'extractSlideTexts');
        $method->setAccessible(true);

        /** @var array<int,string> $texts */
        $texts = $method->invoke($service, $reader, 'ppt/slides/slide1.xml');
        $reader->close();

        @unlink($zipPath);

        $this->assertContains('Qui veut répéter ?', $texts);
        $this->assertContains('Une étoile', $texts);
        $this->assertContains("L'oncle", $texts);
        $this->assertContains('La 1re année', $texts);
        $this->assertContains('Le sucre', $texts);
        $this->assertNotContains('Un e étoile', $texts);
        $this->assertNotContains('’oncle', $texts);
    }

    public function test_cleanup_deletes_only_stale_unlinked_rows(): void
    {
        VocabularyItem::query()->create([
            'word' => 'Le chat',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
            'concept_id' => 'C-CHAT',
        ]);

        VocabularyItem::query()->create([
            'word' => 'La porte',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
            'revizy_image_file_id' => 'img-porte',
        ]);

        VocabularyItem::query()->create([
            'word' => 'Le parc',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
        ]);

        VocabularyItem::query()->create([
            'word' => 'Le livre',
            'grade' => 'N4',
            'subject' => 'FR',
            'period' => 'P1',
            'week' => 'SEM1',
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
        ]);

        $service = $this->app->make(VocabularyExtractionService::class);
        $method = new ReflectionMethod($service, 'purgeStaleUnlinkedVocabularyRows');
        $method->setAccessible(true);
        $method->invoke($service, 'FR_N4_P1_SEM1_S1', 'N4', ['Le livre']);

        $this->assertDatabaseHas('vocabulary_items', [
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
            'grade' => 'N4',
            'word' => 'Le chat',
        ]);

        $this->assertDatabaseHas('vocabulary_items', [
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
            'grade' => 'N4',
            'word' => 'La porte',
        ]);

        $this->assertDatabaseHas('vocabulary_items', [
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
            'grade' => 'N4',
            'word' => 'Le livre',
        ]);

        $this->assertDatabaseMissing('vocabulary_items', [
            'lesson_id' => 'FR_N4_P1_SEM1_S1',
            'grade' => 'N4',
            'word' => 'Le parc',
        ]);
    }
}
