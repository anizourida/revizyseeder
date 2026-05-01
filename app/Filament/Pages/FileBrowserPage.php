<?php

namespace App\Filament\Pages;

class FileBrowserPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'browser';

    protected static ?string $title = 'Navigateur';

    protected static ?string $navigationLabel = 'File Browser';

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?int $navigationSort = 40;

    protected static function legacyInitialView(): string
    {
        return 'browser';
    }
}
