<?php

namespace App\Filament\Resources\ArabicVocabularyResource\Pages;

use App\Filament\Resources\ArabicVocabularyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArabicVocabulary extends EditRecord
{
    protected static string $resource = ArabicVocabularyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
