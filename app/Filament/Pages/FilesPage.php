<?php

namespace App\Filament\Pages;

class FilesPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'files';

    protected static ?string $title = 'Fichiers';

    protected static ?string $navigationLabel = 'Files';

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 30;

    protected static function legacyInitialView(): string
    {
        return 'files';
    }
}
