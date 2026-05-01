<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasRaiidaLegacyModuleShell;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasRaiidaLegacyModuleShell;

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Raiida';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.raiida-module';

    protected static function legacyInitialView(): string
    {
        return 'dashboard';
    }
}
