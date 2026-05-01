<?php

namespace App\Filament\Pages;

class FlashcardsUploaderPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'flashcards-uploader';

    protected static ?string $title = 'Flashcards Uploader';

    protected static ?string $navigationLabel = 'Flashcards Uploader';

    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static ?int $navigationSort = 80;

    protected static function legacyInitialView(): string
    {
        return 'flashcards-uploader';
    }
}
