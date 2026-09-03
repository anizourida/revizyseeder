<?php

namespace App\Filament\Resources\VocabularySentenceResource\Pages;

use App\Filament\Resources\VocabularySentenceResource;
use App\Models\Raiida\VocabularySentence;
use App\Services\Raiida\VocabularySentenceExtractionService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Throwable;

class ListVocabularySentences extends ListRecords
{
    protected static string $resource = VocabularySentenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Sentence'),

            Actions\Action::make('extractSentences')
                ->label('Scan & Extract Sentences')
                ->icon('heroicon-o-arrow-path')
                ->color(Color::Emerald)
                ->form([
                    Forms\Components\Select::make('grade')
                        ->label('Filter by Grade (optional)')
                        ->options([
                            '' => 'All Grades (N1 to N6)',
                            'N1' => 'N1',
                            'N2' => 'N2',
                            'N3' => 'N3',
                            'N4' => 'N4',
                            'N5' => 'N5',
                            'N6' => 'N6',
                        ]),
                    Forms\Components\Select::make('period')
                        ->label('Filter by Period (optional)')
                        ->options([
                            '' => 'All Periods',
                            'P1' => 'P1',
                            'P2' => 'P2',
                            'P3' => 'P3',
                            'P4' => 'P4',
                            'P5' => 'P5',
                        ]),
                    Forms\Components\Select::make('week')
                        ->label('Filter by Week (optional)')
                        ->options([
                            '' => 'All Weeks',
                            'SEM1' => 'SEM1',
                            'SEM2' => 'SEM2',
                            'SEM3' => 'SEM3',
                            'SEM4' => 'SEM4',
                            'SEM5' => 'SEM5',
                            'SEM6' => 'SEM6',
                        ]),
                    Forms\Components\Toggle::make('force')
                        ->label('Force re-extraction (refresh existing sentences)')
                        ->default(false),
                ])
                ->action(function (array $data, VocabularySentenceExtractionService $service) {
                    try {
                        $stats = $service->extractSentences([
                            'grade' => $data['grade'] ?? '',
                            'period' => $data['period'] ?? '',
                            'week' => $data['week'] ?? '',
                            'force' => (bool) ($data['force'] ?? false),
                        ]);

                        Notification::make()
                            ->title('Extraction Completed')
                            ->body("Processed {$stats['total_vocabs']} vocabularies. Found sentences for {$stats['vocabs_with_sentences']} words ({$stats['sentences_created']} total sentences created). {$stats['vocabs_without_sentences']} words have no sentences.")
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Extraction Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
