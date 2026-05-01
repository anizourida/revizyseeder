<?php

namespace App\Filament\Pages;

class ConceptCreatorPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'concept-creator';

    protected static ?string $title = 'Concept Creator';

    protected static ?string $navigationLabel = 'Concept Creator';

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?int $navigationSort = 90;

    protected static function legacyInitialView(): string
    {
        return 'concept-creator';
    }
}
