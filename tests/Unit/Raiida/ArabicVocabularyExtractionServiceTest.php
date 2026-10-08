<?php

namespace Tests\Unit\Raiida;

use App\Services\Raiida\ArabicVocabularyExtractionService;
use Tests\TestCase;

class ArabicVocabularyExtractionServiceTest extends TestCase
{
    protected ArabicVocabularyExtractionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ArabicVocabularyExtractionService::class);
    }

    public function test_it_strips_tatweel_while_preserving_all_diacritics(): void
    {
        $input = "تَــعاوَنَ";
        $cleaned = $this->service->stripTatweel($input);
        $this->assertSame("تَعاوَنَ", $cleaned);
    }

    public function test_it_cleans_arabic_boundary_and_ensures_proper_wasla_spacing(): void
    {
        // Protected method reflection
        $ref = new \ReflectionMethod($this->service, 'cleanArabicBoundary');
        $ref->setAccessible(true);

        $input = "لِسَماعِﭐلْحِكايَةِ.";
        $cleaned = $ref->invoke($this->service, $input);
        $this->assertSame("لِسَماعِ ﭐلْحِكايَةِ", $cleaned);
    }

    public function test_it_extracts_n2_p1_sem1_preview_with_full_tashkeel(): void
    {
        $filePath = base_path('files/AR/niveau_2/periode_1/semaine_1/AR_N2_P1_SEM1_S1.ppsx');
        if (! is_file($filePath)) {
            $this->markTestSkipped('PPSX file not present on this machine.');
        }

        $items = $this->service->previewLesson($filePath);
        $this->assertCount(8, $items);

        $words = array_column($items, 'word');
        $this->assertContains('تَعاوَنَ', $words);
        $this->assertContains('رافَقَتْ', $words);
        $this->assertContains('أَجَّلَ', $words);
        $this->assertContains('تَأَجَّلَتْ', $words);
        $this->assertContains('مُتَحَمِّسَةٌ', $words);
        $this->assertContains('مُغامَرَةٌ', $words);
        $this->assertContains('مُتَشَوِّقَةٌ', $words);
        $this->assertContains('رافقَ', $words);

        // Verify that every single example sentence and linked sentence has full Tashkeel
        foreach ($items as $item) {
            $this->assertNotEmpty($item['example_sentence'], "Missing example sentence for {$item['word']}");
            $diacriticCount = preg_match_all('/[\x{064B}-\x{065F}\x{0670}]/u', $item['example_sentence']);
            $this->assertGreaterThanOrEqual(8, $diacriticCount, "Example sentence lacks sufficient Tashkeel: {$item['example_sentence']}");

            foreach ($item['linked_sentences'] ?? [] as $linked) {
                $linkedDiacritics = preg_match_all('/[\x{064B}-\x{065F}\x{0670}]/u', $linked);
                $this->assertGreaterThanOrEqual(8, $linkedDiacritics, "Linked sentence lacks sufficient Tashkeel: {$linked}");
            }
        }
    }
}
