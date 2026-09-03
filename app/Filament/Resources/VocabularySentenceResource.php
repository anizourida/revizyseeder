<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VocabularySentenceResource\Pages;
use App\Models\Raiida\VocabularySentence;
use App\Services\Raiida\VocabularySentenceExtractionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VocabularySentenceResource extends Resource
{
    protected static ?string $model = VocabularySentence::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Vocabulary Sentences';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Vocabulary Sentence';

    protected static ?string $pluralModelLabel = 'Vocabulary Sentences';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vocabulary & Sentence')
                ->description('Vocabulary word and extracted contextual sentence.')
                ->aside()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('word')
                        ->label('Vocabulary Word')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('base_word')
                        ->label('Base Word (no article)')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('sentence')
                        ->label('French Sentence')
                        ->columnSpanFull()
                        ->rows(3)
                        ->placeholder('Exemple: Le garçon donne un cadeau à son ami.')
                        ->helperText('Contextual French sentence containing the vocabulary word.'),
                    Forms\Components\Textarea::make('sentence_ar')
                        ->label('Arabic Translation of Sentence')
                        ->columnSpanFull()
                        ->rows(2)
                        ->extraInputAttributes(['dir' => 'rtl']),
                ]),

            Forms\Components\Section::make('Curriculum & Scope')
                ->description('Grade, period, week and lesson tracking.')
                ->aside()
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('grade')
                        ->options([
                            'N1' => 'N1 (Niveau 1)',
                            'N2' => 'N2 (Niveau 2)',
                            'N3' => 'N3 (Niveau 3)',
                            'N4' => 'N4 (Niveau 4)',
                            'N5' => 'N5 (Niveau 5)',
                            'N6' => 'N6 (Niveau 6)',
                        ])
                        ->required(),
                    Forms\Components\Select::make('period')
                        ->options([
                            'P1' => 'P1 (Période 1)',
                            'P2' => 'P2 (Période 2)',
                            'P3' => 'P3 (Période 3)',
                            'P4' => 'P4 (Période 4)',
                            'P5' => 'P5 (Période 5)',
                        ])
                        ->required(),
                    Forms\Components\Select::make('week')
                        ->options([
                            'SEM1' => 'SEM1 (Semaine 1)',
                            'SEM2' => 'SEM2 (Semaine 2)',
                            'SEM3' => 'SEM3 (Semaine 3)',
                            'SEM4' => 'SEM4 (Semaine 4)',
                            'SEM5' => 'SEM5 (Semaine 5)',
                            'SEM6' => 'SEM6 (Semaine 6)',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('lesson_id')
                        ->label('Lesson ID')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('source_session')
                        ->label('Source Session (e.g. S1, S5)')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('source_slide')
                        ->label('Source Slide #')
                        ->numeric(),
                    Forms\Components\Select::make('source_type')
                        ->options([
                            'slide' => 'Slide Presentation',
                            'ocr' => 'OCR Book/Page',
                            'manual' => 'Manual Input',
                        ])
                        ->default('slide')
                        ->required(),
                ]),

            Forms\Components\Section::make('Media')
                ->description('Image and audio assets.')
                ->aside()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('image_path')
                        ->label('Image Path')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('audio_path')
                        ->label('Audio Path')
                        ->maxLength(500),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(static function (Builder $query): Builder {
                return $query
                    ->orderByRaw("CASE grade WHEN 'N1' THEN 1 WHEN 'N2' THEN 2 WHEN 'N3' THEN 3 WHEN 'N4' THEN 4 WHEN 'N5' THEN 5 WHEN 'N6' THEN 6 ELSE 7 END ASC")
                    ->orderByRaw("CAST(SUBSTR(period, 2) AS INTEGER) ASC")
                    ->orderByRaw("CAST(SUBSTR(week, 4) AS INTEGER) ASC")
                    ->orderBy('word', 'asc')
                    ->orderBy('source_slide', 'asc');
            })
            ->poll('10s')
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => $record->image_path ? (str_starts_with($record->image_path, 'http') ? $record->image_path : asset($record->image_path)) : null)
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('word')
                    ->label('Vocabulary Word')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sentence')
                    ->label('Contextual Sentence')
                    ->searchable()
                    ->wrap()
                    ->placeholder('— Aucun phrase trouvée —')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($state)) {
                            return '⚠️ Aucun phrase trouvée';
                        }
                        return $state;
                    })
                    ->color(fn ($record) => empty($record->sentence) ? 'warning' : 'primary'),
                Tables\Columns\BadgeColumn::make('grade')
                    ->label('Grade')
                    ->colors([
                        'primary' => 'N1',
                        'success' => 'N2',
                        'warning' => 'N3',
                        'danger' => 'N4',
                        'info' => 'N5',
                        'secondary' => 'N6',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->sortable(),
                Tables\Columns\TextColumn::make('week')
                    ->label('Week')
                    ->sortable(),
                Tables\Columns\TextColumn::make('source_info')
                    ->label('Source')
                    ->getStateUsing(function ($record) {
                        if ($record->source_session || $record->source_slide) {
                            return ($record->source_session ?: '') . ($record->source_slide ? ' (Slide ' . $record->source_slide . ')' : '');
                        }
                        return $record->source_type ?: '—';
                    })
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('lesson_id')
                    ->label('Lesson')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade')
                    ->options([
                        'N1' => 'N1 (Niveau 1)',
                        'N2' => 'N2 (Niveau 2)',
                        'N3' => 'N3 (Niveau 3)',
                        'N4' => 'N4 (Niveau 4)',
                        'N5' => 'N5 (Niveau 5)',
                        'N6' => 'N6 (Niveau 6)',
                    ]),
                Tables\Filters\SelectFilter::make('period')
                    ->options([
                        'P1' => 'P1',
                        'P2' => 'P2',
                        'P3' => 'P3',
                        'P4' => 'P4',
                        'P5' => 'P5',
                    ]),
                Tables\Filters\SelectFilter::make('week')
                    ->options([
                        'SEM1' => 'SEM1',
                        'SEM2' => 'SEM2',
                        'SEM3' => 'SEM3',
                        'SEM4' => 'SEM4',
                        'SEM5' => 'SEM5',
                        'SEM6' => 'SEM6',
                    ]),
                Tables\Filters\TernaryFilter::make('has_sentence')
                    ->label('Sentence Availability')
                    ->placeholder('All Items')
                    ->trueLabel('Has Sentences')
                    ->falseLabel('Missing Sentences')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('sentence')->where('sentence', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn ($q) => $q->whereNull('sentence')->orWhere('sentence', '')),
                    ),
                Tables\Filters\SelectFilter::make('source_type')
                    ->options([
                        'slide' => 'Slide Presentation',
                        'ocr' => 'OCR Data',
                        'manual' => 'Manual Input',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVocabularySentences::route('/'),
            'create' => Pages\CreateVocabularySentence::route('/create'),
            'edit' => Pages\EditVocabularySentence::route('/{record}/edit'),
        ];
    }
}
