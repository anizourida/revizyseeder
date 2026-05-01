<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_olmocr')
                ->label('Show HTML (olmocr)')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->visible(fn () => !empty($this->record->ocr_olmocr_path))
                ->url(fn () => route('ocr.view', ['page' => $this->record->id, 'model' => 'olmocr']))
                ->openUrlInNewTab(),

            Actions\Action::make('view_chandra')
                ->label('Show HTML (chandra)')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->visible(fn () => !empty($this->record->ocr_chandra_path))
                ->url(fn () => route('ocr.view', ['page' => $this->record->id, 'model' => 'chandra']))
                ->openUrlInNewTab(),

            Actions\Action::make('get_page_num')
                ->label('Get Page Number')
                ->icon('heroicon-m-hashtag')
                ->color('warning')
                ->action(function () {
                    \App\Jobs\RevizySeederLMStudioOCRJob::dispatch($this->record->id, 'allenai/olmocr-2-7b', 'page_only');
                    \Filament\Notifications\Notification::make()->title('Page number extraction started')->info()->send();
                }),

            Actions\Action::make('run_olmocr')
                ->label('Text (olmocr)')
                ->icon('heroicon-m-sparkles')
                ->color('primary')
                ->action(function () {
                    \App\Jobs\RevizySeederLMStudioOCRJob::dispatch($this->record->id, 'allenai/olmocr-2-7b', 'text_only');
                    \Filament\Notifications\Notification::make()->title('Text extraction (olmocr) started')->info()->send();
                }),

            Actions\Action::make('run_chandra')
                ->label('Text (chandra)')
                ->icon('heroicon-m-bolt')
                ->color('info')
                ->action(function () {
                    \App\Jobs\RevizySeederLMStudioOCRJob::dispatch($this->record->id, 'chandra-ocr', 'text_only');
                    \Filament\Notifications\Notification::make()->title('Text extraction (chandra) started')->info()->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
