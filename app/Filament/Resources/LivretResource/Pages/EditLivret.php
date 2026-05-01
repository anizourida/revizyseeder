<?php

namespace App\Filament\Resources\LivretResource\Pages;

use App\Filament\Resources\LivretResource;
use App\Jobs\RevizySeederLMStudioOCRJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLivret extends EditRecord
{
    protected static string $resource = LivretResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_olmocr')
                ->label('Show HTML (olmOCR)')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->visible(fn (): bool => filled($this->record->ocr_olmocr_path))
                ->url(fn (): string => route('ocr.view', ['page' => $this->record->id, 'model' => 'olmocr']))
                ->openUrlInNewTab(),

            Actions\Action::make('view_chandra')
                ->label('Show HTML (Chandra)')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->visible(fn (): bool => filled($this->record->ocr_chandra_path))
                ->url(fn (): string => route('ocr.view', ['page' => $this->record->id, 'model' => 'chandra']))
                ->openUrlInNewTab(),

            Actions\Action::make('run_olmocr')
                ->label('Extract Text (olmOCR)')
                ->icon('heroicon-m-sparkles')
                ->color('primary')
                ->requiresConfirmation()
                ->modalDescription('Runs text OCR only for this single page.')
                ->action(function (): void {
                    RevizySeederLMStudioOCRJob::dispatch($this->record->id, 'allenai/olmocr-2-7b', 'text_only');

                    Notification::make()
                        ->title('olmOCR extraction queued')
                        ->body('Text extraction started for this page.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('run_chandra')
                ->label('Extract Text (Chandra)')
                ->icon('heroicon-m-bolt')
                ->color('info')
                ->requiresConfirmation()
                ->modalDescription('Runs Chandra OCR only for this single page.')
                ->action(function (): void {
                    RevizySeederLMStudioOCRJob::dispatch($this->record->id, 'chandra-ocr', 'text_only');

                    Notification::make()
                        ->title('Chandra extraction queued')
                        ->body('Text extraction started for this page.')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
