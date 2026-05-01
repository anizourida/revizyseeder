<?php

namespace App\Filament\Pages;

class ConjugaisonPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'conjugaison';

    protected static ?string $title = 'Conjugaison';

    protected static ?string $navigationLabel = 'Conjugaison';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 110;

    protected static function legacyInitialView(): string
    {
        return 'conjugaison';
    }
}
