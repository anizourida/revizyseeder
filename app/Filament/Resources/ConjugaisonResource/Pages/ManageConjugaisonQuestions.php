<?php

namespace App\Filament\Resources\ConjugaisonResource\Pages;

use App\Filament\Resources\ConjugaisonResource;
use App\Models\Raiida\Conjugaison;
use Filament\Resources\Pages\Page;

class ManageConjugaisonQuestions extends Page
{
    protected static string $resource = ConjugaisonResource::class;

    protected static string $view = 'filament.pages.conjugaison-questions';

    protected static ?string $title = 'Conjugaison Questions';

    public ?Conjugaison $conjugaison = null;

    public function mount(int $record): void
    {
        $this->conjugaison = Conjugaison::findOrFail($record);
    }

    public function getHeading(): string
    {
        $c = $this->conjugaison;

        return "Questions — {$c->n} / {$c->p} / {$c->sem}" . ($c->verbe ? " ({$c->verbe})" : '');
    }
}
