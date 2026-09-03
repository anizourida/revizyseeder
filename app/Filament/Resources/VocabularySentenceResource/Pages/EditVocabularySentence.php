<?php

namespace App\Filament\Resources\VocabularySentenceResource\Pages;

use App\Filament\Resources\VocabularySentenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVocabularySentence extends EditRecord
{
    protected static string $resource = VocabularySentenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
