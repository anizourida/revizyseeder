<?php

namespace App\Filament\Resources\ArabicVocabularyResource\Pages;

use App\Filament\Resources\ArabicVocabularyResource;
use Filament\Resources\Pages\ListRecords;

class ListArabicVocabularies extends ListRecords
{
    protected static string $resource = ArabicVocabularyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('open_platform')
                ->label('منصة المفردات المصورة (Visual Platform)')
                ->icon('heroicon-o-squares-2x2')
                ->color('success')
                ->url(url('/arabic-vocabulary-platform'))
                ->openUrlInNewTab(),
        ];
    }
}
