<?php

namespace Tests\Unit\Raiida;

use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\VocabularySentenceExtractionService;
use Tests\TestCase;

class VocabularySentenceExtractionServiceTest extends TestCase
{
    protected VocabularySentenceExtractionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(VocabularySentenceExtractionService::class);
    }

    public function test_it_splits_sentences_and_cleans_glued_ocr_dots(): void
    {
        $raw = "Je m’appelle Akram Allali.Mon prénom est Akram.Mon nom est Allali.";
        $sentences = $this->service->splitIntoSentences($raw);

        $this->assertCount(3, $sentences);
        $this->assertSame('Je m’appelle Akram Allali.', $sentences[0]);
        $this->assertSame('Mon prénom est Akram.', $sentences[1]);
        $this->assertSame('Mon nom est Allali.', $sentences[2]);
    }

    public function test_it_strips_meta_prefixes(): void
    {
        $raw = "Voici une phrase correcte : Mon ami va à l’école en bus.";
        $sentences = $this->service->splitIntoSentences($raw);

        $this->assertCount(1, $sentences);
        $this->assertSame('Mon ami va à l’école en bus.', $sentences[0]);
    }

    public function test_it_expands_verb_conjugations_and_elisions(): void
    {
        $vocab = new VocabularyItem([
            'word' => 'S’appeler',
            'base_word' => "s'appeler",
        ]);

        $terms = $this->service->buildSearchTerms($vocab);
        $this->assertContains("m'appelle", $terms);
        $this->assertContains("s’appelle", $terms);
        $this->assertContains("t'appelles", $terms);

        $vocabWater = new VocabularyItem([
            'word' => 'L’eau',
            'base_word' => 'eau',
        ]);
        $termsWater = $this->service->buildSearchTerms($vocabWater);
        $this->assertContains('eau', $termsWater);
        $this->assertContains("d'eau", $termsWater);
    }

    public function test_it_filters_teacher_instructions_and_questions(): void
    {
        $item = new VocabularyItem(['word' => 'Un cadeau', 'base_word' => 'cadeau']);
        $terms = $this->service->buildSearchTerms($item);

        // Good sentence
        $this->assertTrue($this->service->isValidSentenceForVocab('Mariam a un beau cadeau.', $terms, 'Un cadeau', 'cadeau'));
        $this->assertTrue($this->service->isValidSentenceForVocab('Le garçon donne un cadeau à son ami.', $terms, 'Un cadeau', 'cadeau'));

        // Questions rejected
        $this->assertFalse($this->service->isValidSentenceForVocab('Est-ce que Lina a un cadeau ?', $terms, 'Un cadeau', 'cadeau'));

        // Teacher instructions rejected
        $this->assertFalse($this->service->isValidSentenceForVocab('Prenez vos cahiers et écrivez le mot cadeau.', $terms, 'Un cadeau', 'cadeau'));
        $this->assertFalse($this->service->isValidSentenceForVocab('Qui veut répéter ? Un cadeau', $terms, 'Un cadeau', 'cadeau'));
        $this->assertFalse($this->service->isValidSentenceForVocab('S’appeler – Le nom – Le prénom – Un cadeau - Un ami – Jaune.', $terms, 'Un cadeau', 'cadeau'));

        // Fill in blanks rejected
        $this->assertFalse($this->service->isValidSentenceForVocab('Karim a un …………………', $terms, 'Un cadeau', 'cadeau'));
    }
}
