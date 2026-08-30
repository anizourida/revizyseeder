<?php

namespace App\Filament\Resources\ArabicVocabularyResource\Pages;

use App\Filament\Resources\ArabicVocabularyResource;
use Filament\Resources\Pages\ListRecords;

class ListArabicVocabularies extends ListRecords
{
    protected static string $resource = ArabicVocabularyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
