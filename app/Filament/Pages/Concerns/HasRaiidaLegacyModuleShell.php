<?php

namespace App\Filament\Pages\Concerns;

use Filament\Support\Enums\MaxWidth;

trait HasRaiidaLegacyModuleShell
{
    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::Full;
    }

    public function getInitialView(): string
    {
        return static::legacyInitialView();
    }

    public function getApiBase(): string
    {
        return '/api';
    }

    abstract protected static function legacyInitialView(): string;
}
