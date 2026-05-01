<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasRaiidaLegacyModuleShell;
use Filament\Pages\Page;

abstract class RaiidaLegacyPage extends Page
{
    use HasRaiidaLegacyModuleShell;

    protected static string $view = 'filament.pages.raiida-module';

    protected static ?string $navigationGroup = 'Raiida';
}
