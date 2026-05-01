<?php

namespace App\Filament\Pages;

class RoadmapPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'roadmap';

    protected static ?string $title = 'Roadmap';

    protected static ?string $navigationLabel = 'Roadmap';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 20;

    protected static function legacyInitialView(): string
    {
        return 'roadmap';
    }
}
