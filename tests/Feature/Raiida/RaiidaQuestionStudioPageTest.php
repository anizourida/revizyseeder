<?php

namespace Tests\Feature\Raiida;

use Tests\TestCase;

class RaiidaQuestionStudioPageTest extends TestCase
{
    public function test_raiida_frontend_pages_load_and_routes_are_reachable(): void
    {
        $this->get('/raiida/question-studio')
            ->assertRedirect('/raiida/questions-studio');

        $modules = [
            'dashboard',
            'files',
            'browser',
            'vocabulary',
            'audios',
            'assets',
            'flashcards-uploader',
            'concept-creator',
            'questions-studio',
            'conjugaison',
            'grammaire',
            'roadmap',
        ];

        foreach ($modules as $module) {
            $this->get('/raiida/' . $module)->assertOk();
        }

        $this->get('/raiida/questions-studio')
            ->assertOk()
            ->assertSee('Questions Studio')
            ->assertSee('Flashcards Uploader')
            ->assertSee('Concept creator');

        $this->get('/raiida/roadmap')
            ->assertOk()
            ->assertSee('Roadmap Pédagogique');

        $this->get('/raiida/grammaire')
            ->assertOk()
            ->assertSee('Leçons de Grammaire');
    }
}
