<?php

namespace App\Filament\Pages;

class AssetsPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'assets';

    protected static ?string $title = 'Assets';

    protected static ?string $navigationLabel = 'Assets';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 70;

    protected static function legacyInitialView(): string
    {
        return 'assets';
    }
}
