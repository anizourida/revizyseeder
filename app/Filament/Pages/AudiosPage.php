<?php

namespace App\Filament\Pages;

class AudiosPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'audios';

    protected static ?string $title = 'Audios';

    protected static ?string $navigationLabel = 'Audios';

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?int $navigationSort = 60;

    protected static function legacyInitialView(): string
    {
        return 'audios';
    }
}
