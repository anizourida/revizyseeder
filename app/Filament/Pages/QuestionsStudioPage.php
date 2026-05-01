<?php

namespace App\Filament\Pages;

class QuestionsStudioPage extends RaiidaLegacyPage
{
    protected static ?string $slug = 'questions-studio';

    protected static ?string $title = 'Questions Studio';

    protected static ?string $navigationLabel = 'Questions Studio';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?int $navigationSort = 100;

    protected static function legacyInitialView(): string
    {
        return 'questions-studio';
    }
}
