<?php

namespace App\Filament\Widgets;

use App\Jobs\ExtractPageNumberJob;
use App\Jobs\RevizySeederLMStudioOCRJob;
use App\Models\Raiida\Page;
use App\Support\RevizySeeder\WorkflowState;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PagesNeedingExtractionWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $enablePython = (bool) config('revizyseeder.dashboard.enable_python_ocr', false);

        return $table
            ->heading('Pages Needing Attention')
            ->description('Missing page numbers, OCR failures, or extraction errors.')
            ->query(
                Page::query()
                    ->where(function ($q) {
                        $q->whereNull('page_number')
                            ->orWhere('page_number', '<', 1)
                            ->orWhere('page_number_extraction_method', 'ocr_failed')
                            ->orWhereNotNull('page_number_extraction_error');
                    })
                    ->orderByDesc('updated_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('grade.name')
                    ->label('Grade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('n_p_sem')
                    ->label('Lesson')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('page_number')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('page_number_extraction_method')
                    ->label('Method')
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('page_number_extraction_error')
                    ->label('Error')
                    ->badge()
                    ->color('danger')
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('python_ocr')
                    ->label('Python OCR')
                    ->icon('heroicon-o-hashtag')
                    ->visible(fn (): bool => $enablePython)
                    ->color('gray')
                    ->disabled(fn (): bool => WorkflowState::isPaused())
                    ->action(function (Page $record): void {
                        ExtractPageNumberJob::dispatch($record->id)->onQueue(WorkflowState::workflowQueue());
                        Notification::make()->title("Queued Python OCR for page {$record->id}")->info()->send();
                    }),
                Tables\Actions\Action::make('lmstudio_page')
                    ->label('LM Studio')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->disabled(fn (): bool => WorkflowState::isPaused())
                    ->action(function (Page $record): void {
                        RevizySeederLMStudioOCRJob::dispatch($record->id, 'allenai/olmocr-2-7b', 'page_only')
                            ->onQueue(WorkflowState::workflowQueue());
                        Notification::make()->title("Queued LM Studio page-number for page {$record->id}")->info()->send();
                    }),
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Page $record): string => route('filament.admin.resources.pages.edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
