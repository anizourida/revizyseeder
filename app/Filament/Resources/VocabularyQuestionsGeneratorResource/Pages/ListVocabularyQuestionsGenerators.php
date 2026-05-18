<?php

namespace App\Filament\Resources\VocabularyQuestionsGeneratorResource\Pages;

use App\Filament\Resources\VocabularyQuestionsGeneratorResource;
use App\Jobs\Raiida\GenerateStandardVocabularyQuestionsJob;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListVocabularyQuestionsGenerators extends ListRecords
{
    protected static string $resource = VocabularyQuestionsGeneratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_standard_for_scope')
                ->label('Generate Standard (Scope)')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->modalHeading('Generate Standard Questions for Scope')
                ->modalDescription('Queues standard question generation (no fill_text) for vocabulary concepts that have 0 published questions in the selected scope.')
                ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                ->form([
                    Forms\Components\Select::make('period')
                        ->label('Period')
                        ->options([
                            'P1' => 'P1',
                            'P2' => 'P2',
                            'P3' => 'P3',
                            'P4' => 'P4',
                            'P5' => 'P5',
                        ])
                        ->default('P5')
                        ->required(),
                    Forms\Components\Select::make('week')
                        ->label('Week')
                        ->options([
                            'SEM1' => 'SEM1',
                            'SEM2' => 'SEM2',
                            'SEM3' => 'SEM3',
                            'SEM4' => 'SEM4',
                            'SEM5' => 'SEM5',
                            'SEM6' => 'SEM6',
                        ])
                        ->default('SEM2')
                        ->required(),
                    Forms\Components\Select::make('grade')
                        ->label('Grade (optional)')
                        ->options([
                            'N1' => 'N1',
                            'N2' => 'N2',
                            'N3' => 'N3',
                            'N4' => 'N4',
                            'N5' => 'N5',
                            'N6' => 'N6',
                        ])
                        ->placeholder('All grades'),
                    Forms\Components\TextInput::make('limit')
                        ->label('Limit')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50000)
                        ->default(5000),
                ])
                ->action(function (array $data): void {
                    $user = auth()->user();

                    $options = [
                        'limit' => (int) ($data['limit'] ?? 5000),
                        'grade' => $data['grade'] ?? null,
                        'period' => $data['period'] ?? null,
                        'week' => $data['week'] ?? null,
                        'verbose' => false,
                    ];
                    $options = array_filter($options, static fn ($value) => $value !== null);

                    GenerateStandardVocabularyQuestionsJob::dispatch(
                        $options,
                        null,
                        $user?->id,
                        $user?->email,
                        $user?->role
                    );

                    Notification::make()
                        ->title('Standard generation queued')
                        ->body('Background job started. Check logs: raiida.admin_mutation.job.generate_vocab_standard_questions.completed')
                        ->success()
                        ->send();
                }),
        ];
    }
}
