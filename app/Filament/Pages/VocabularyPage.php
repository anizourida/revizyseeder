<?php

namespace App\Filament\Pages;

class VocabularyPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'vocabulary';

    protected static ?string $title = 'Vocabulaire';

    protected static ?string $navigationLabel = 'Vocabulary';

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?int $navigationSort = 50;

    protected static function legacyInitialView(): string
    {
        return 'vocab';
    }
}
