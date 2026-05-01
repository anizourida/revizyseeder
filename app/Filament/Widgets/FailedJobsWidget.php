<?php

namespace App\Filament\Widgets;

use App\Models\Queue\FailedQueueJob;
use App\Support\RevizySeeder\WorkflowState;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Artisan;

class FailedJobsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $queue = WorkflowState::workflowQueue();

        return $table
            ->heading('Failed Jobs')
            ->description('Latest failures for queue: ' . $queue)
            ->query(
                FailedQueueJob::query()
                    ->where('queue', $queue)
                    ->orderByDesc('id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payload')
                    ->label('Job')
                    ->state(function (FailedQueueJob $record): string {
                        $payload = (string) ($record->payload ?? '');
                        if ($payload === '') {
                            return 'Unknown';
                        }
                        // Keep it short but informative.
                        $payload = preg_replace('/\\s+/', ' ', $payload) ?? $payload;
                        return mb_substr($payload, 0, 160) . (mb_strlen($payload) > 160 ? '…' : '');
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('exception')
                    ->label('Error')
                    ->state(function (FailedQueueJob $record): string {
                        $exception = (string) ($record->exception ?? '');
                        $exception = preg_replace('/\\s+/', ' ', $exception) ?? $exception;
                        return mb_substr($exception, 0, 180) . (mb_strlen($exception) > 180 ? '…' : '');
                    })
                    ->wrap(),
            ])
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (FailedQueueJob $record): void {
                        Artisan::call('queue:retry', [(string) $record->id]);
                        Notification::make()->title("Queued retry for failed job {$record->id}")->info()->send();
                    }),
                Tables\Actions\Action::make('forget')
                    ->label('Forget')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (FailedQueueJob $record): void {
                        Artisan::call('queue:forget', [(string) $record->id]);
                        Notification::make()->title("Forgot failed job {$record->id}")->success()->send();
                    }),
            ])
            ->defaultPaginationPageOption(5);
    }
}

