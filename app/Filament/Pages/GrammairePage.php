<?php

namespace App\Filament\Pages;

class GrammairePage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'grammaire';

    protected static ?string $title = 'Grammaire';

    protected static ?string $navigationLabel = 'Grammaire';

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?int $navigationSort = 120;

    protected static function legacyInitialView(): string
    {
        return 'grammaire';
    }
}
