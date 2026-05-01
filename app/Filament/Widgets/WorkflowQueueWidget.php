<?php

namespace App\Filament\Widgets;

use App\Jobs\RevizySeederDispatchLMStudioPageNumberBatchJob;
use App\Jobs\RevizySeederDispatchPageNumberOCRBatchJob;
use App\Jobs\RevizySeederExtractPagesJob;
use App\Models\Queue\QueueJob;
use App\Support\RevizySeeder\WorkflowState;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class WorkflowQueueWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $queue = WorkflowState::workflowQueue();
        $enablePython = (bool) config('revizyseeder.dashboard.enable_python_ocr', false);
        $enableLm = (bool) config('revizyseeder.dashboard.enable_lmstudio_ocr', true);

        return $table
            ->heading('Workflow Queue')
            ->description('Visibility and controls for queue: ' . $queue)
            ->query(
                QueueJob::query()
                    ->where('queue', $queue)
                    ->orderByDesc('id')
            )
            ->poll('5s')
            ->headerActions([
                Tables\Actions\Action::make('pause')
                    ->label(fn (): string => WorkflowState::isPaused() ? 'Paused' : 'Pause')
                    ->icon('heroicon-o-pause')
                    ->color(fn (): string => WorkflowState::isPaused() ? 'gray' : 'warning')
                    ->disabled(fn (): bool => WorkflowState::isPaused())
                    ->requiresConfirmation()
                    ->action(function (): void {
                        WorkflowState::pause();
                        Notification::make()->title('Workflows paused')->warning()->send();
                    }),
                Tables\Actions\Action::make('resume')
                    ->label('Resume')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->disabled(fn (): bool => ! WorkflowState::isPaused())
                    ->action(function (): void {
                        WorkflowState::resume();
                        Notification::make()->title('Workflows resumed')->success()->send();
                    }),
                Tables\Actions\Action::make('extract_pages')
                    ->label('Start: Extract Pages')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('primary')
                    ->disabled(fn (): bool => WorkflowState::isPaused())
                    ->action(function (): void {
                        RevizySeederExtractPagesJob::dispatch()->onQueue(WorkflowState::workflowQueue());
                        Notification::make()->title('Queued: Extract Pages')->info()->send();
                    }),
                Tables\Actions\Action::make('start_lmstudio')
                    ->label('Start: OCR (LM Studio)')
                    ->icon('heroicon-o-sparkles')
                    ->visible(fn (): bool => $enableLm)
                    ->color('info')
                    ->disabled(fn (): bool => WorkflowState::isPaused())
                    ->action(function (): void {
                        RevizySeederDispatchLMStudioPageNumberBatchJob::dispatch()->onQueue(WorkflowState::workflowQueue());
                        Notification::make()->title('Queued: LM Studio page-number batch')->info()->send();
                    }),
                Tables\Actions\Action::make('start_python_ocr')
                    ->label('Start: OCR (Python)')
                    ->icon('heroicon-o-hashtag')
                    ->visible(fn (): bool => $enablePython)
                    ->color('gray')
                    ->disabled(fn (): bool => WorkflowState::isPaused())
                    ->action(function (): void {
                        RevizySeederDispatchPageNumberOCRBatchJob::dispatch()->onQueue(WorkflowState::workflowQueue());
                        Notification::make()->title('Queued: Python OCR batch')->info()->send();
                    }),
                Tables\Actions\Action::make('clear_queue')
                    ->label('Stop: Clear Pending')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function () use ($queue): void {
                        WorkflowState::pause();
                        QueueJob::query()->where('queue', $queue)->delete();
                        Notification::make()->title('Cleared pending queue and paused workflows')->danger()->send();
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Job')
                    ->wrap()
                    ->searchable(query: function ($query, string $search) {
                        // Payload is JSON text; allow searching by displayName / class name.
                        $query->where('payload', 'like', '%' . $search . '%');
                    }),
                Tables\Columns\TextColumn::make('attempts')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reserved_at')
                    ->label('Reserved')
                    ->state(fn (QueueJob $record): string => $record->reserved_at ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Yes' ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('available_at')
                    ->label('Available At')
                    ->state(function (QueueJob $record): string {
                        $value = (int) ($record->available_at ?? 0);
                        if ($value <= 0) {
                            return '';
                        }

                        return Carbon::createFromTimestamp($value)->toDateTimeString();
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->state(function (QueueJob $record): string {
                        $value = (int) ($record->created_at ?? 0);
                        if ($value <= 0) {
                            return '';
                        }

                        return Carbon::createFromTimestamp($value)->toDateTimeString();
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('delete')
                    ->label('Remove')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (QueueJob $record) => $record->delete()),
            ])
            ->defaultPaginationPageOption(10);
    }
}
