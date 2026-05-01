<?php

namespace Tests\Unit\Raiida;

use App\Services\Raiida\ConjugaisonTextAnalyzer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConjugaisonTextAnalyzerTest extends TestCase
{
    #[DataProvider('validLessonProvider')]
    public function test_it_extracts_verbe_and_tense_from_lesson_text(
        string $text,
        ?string $expectedVerbe,
        ?string $expectedTense
    ): void {
        $analyzer = new ConjugaisonTextAnalyzer();

        $result = $analyzer->analyze($text);

        $this->assertNotNull($result);
        $this->assertSame($expectedVerbe, $result['verbe']);
        $this->assertSame($expectedTense, $result['tense']);
        $this->assertGreaterThan(0, $result['score']);
    }

    public static function validLessonProvider(): array
    {
        return [
            'simple utilizer' => [
                'Utiliser le verbe aimer au présent.',
                'aimer',
                'présent',
            ],
            'quoted vouloir' => [
                'Utiliser le verbe « vouloir » au conditionnel présent.',
                'vouloir',
                'conditionnel présent',
            ],
            'group pattern' => [
                'Utiliser les verbes du 1er groupe au passé composé.',
                '1er groupe',
                'passé composé',
            ],
            'conjugaison prefix' => [
                'Conjugaison : Les verbes du 1er groupe au conditionnel présent.',
                '1er groupe',
                'conditionnel présent',
            ],
            'pronominal verb' => [
                'Le verbe « se sentir » au présent.',
                'se sentir',
                'présent',
            ],
            'conjuguer pattern' => [
                'Conjuguer le verbe « prendre » au présent.',
                'prendre',
                'présent',
            ],
            'present continu' => [
                'Conjugaison : Le verbe au présent continu.',
                null,
                'présent progressif',
            ],
        ];
    }

    public function test_it_rejects_non_conjugaison_content(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $result = $analyzer->analyze('Prendre la parole pour se présenter et parler de ses affaires.');

        $this->assertNull($result);
    }

    public function test_it_penalizes_exercise_instructions_against_lesson_objective(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $objective = $analyzer->analyze('Utiliser le verbe aimer au présent.');
        $exercise = $analyzer->analyze('Écrivez sur vos ardoises la bonne forme du verbe aimer au présent.');

        $this->assertNotNull($objective);
        $this->assertNotNull($exercise);
        $this->assertGreaterThan($exercise['score'], $objective['score']);
    }

    public function test_it_extracts_conjugaison_question_prompts(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $result = $analyzer->analyzeQuestion(
            'Écrivez sur vos ardoises la bonne forme du verbe aimer au présent.',
            'aimer',
            'présent'
        );

        $this->assertNotNull($result);
        $this->assertSame('Écrivez sur vos ardoises la bonne forme du verbe aimer au présent.', $result['question']);
        $this->assertGreaterThanOrEqual(5, $result['score']);
    }

    public function test_it_rejects_non_conjugaison_questions(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $result = $analyzer->analyzeQuestion('Quelle est la capitale du Maroc ?', 'aimer', 'présent');

        $this->assertNull($result);
    }

    public function test_it_extracts_conjugated_example_sentences_from_answer_lines(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $result = $analyzer->analyzeExampleSentence(
            "J'apprends le français. Qui veut expliquer cette phrase ?",
            'apprendre'
        );

        $this->assertNotNull($result);
        $this->assertSame("J'apprends le français.", $result['sentence']);
        $this->assertGreaterThanOrEqual(6, $result['score']);
    }

    public function test_it_extracts_pronoun_conjugated_sentence_for_expected_verb(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $result = $analyzer->analyzeExampleSentence(
            'Nous apprenons à faire des additions.',
            'apprendre'
        );

        $this->assertNotNull($result);
        $this->assertSame('Nous apprenons à faire des additions.', $result['sentence']);
    }

    public function test_it_rejects_generic_pedagogical_indications_as_examples(): void
    {
        $analyzer = new ConjugaisonTextAnalyzer();

        $indications = [
            "Aujourd’hui, nous allons apprendre à poser une question avec Majd et Nada.",
            "Aujourd’hui, on va apprendre à parler de nos activités à l’école.",
            "Aujourd’hui, nous allons apprendre un dialogue en français avec Majd et Nada.",
            "Il convient de lancer le mode diaporama pour la diffusion de la leçon en classe",
            "On continue à répéter ensemble le dialogue.",
            "Chacun réfléchit silencieusement à ce qu’il va dire dans le dialogue.",
            "Chacun réfléchit à la conjugaison qu’il va dire dans le dialogue.",
            "Je vais vous expliquer la situation.",
        ];

        foreach ($indications as $line) {
            $result = $analyzer->analyzeExampleSentence($line, 'apprendre');
            $this->assertNull($result, 'Line should be rejected as pedagogical indication: ' . $line);
        }
    }
}
