<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArabicVocabularyResource\Pages;
use App\Jobs\Raiida\ExtractArabicVocabularyJob;
use App\Models\Raiida\ArabicVocabularyItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArabicVocabularyResource extends Resource
{
    protected static ?string $model = ArabicVocabularyItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Arabic Vocabulary';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Arabic Vocabulary Item';

    protected static ?string $pluralModelLabel = 'Arabic Vocabulary';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vocabulary Details')
                ->description('Core Arabic vocabulary and contextual information.')
                ->aside()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('word')
                        ->label('Word (المفردة / الكلمة)')
                        ->required()
                        ->extraInputAttributes(['dir' => 'rtl', 'style' => 'font-size: 1.25rem; font-weight: bold;'])
                        ->maxLength(255),
                    Forms\Components\TextInput::make('raw_word')
                        ->label('Raw Word (بدون تشكيل)')
                        ->extraInputAttributes(['dir' => 'rtl'])
                        ->maxLength(255),
                    Forms\Components\TextInput::make('root')
                        ->label('Root (الجذر)')
                        ->extraInputAttributes(['dir' => 'rtl'])
                        ->maxLength(100),
                    Forms\Components\TextInput::make('strategy')
                        ->label('Strategy (الإستراتيجية)')
                        ->extraInputAttributes(['dir' => 'rtl'])
                        ->placeholder('مثال: المعجم المساعد، الاشتقاق، الصفة المضافة')
                        ->maxLength(100),
                    Forms\Components\Textarea::make('example_sentence')
                        ->label('Example Sentence (جملة السياق)')
                        ->extraInputAttributes(['dir' => 'rtl', 'style' => 'font-size: 1.1rem;'])
                        ->columnSpanFull()
                        ->rows(2),
                ]),

            Forms\Components\Section::make('Scope & Curriculum')
                ->description('Grade, Period, Week, and Lesson identifiers.')
                ->aside()
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('grade')
                        ->options([
                            'N1' => 'N1 (المستوى 1)',
                            'N2' => 'N2 (المستوى 2)',
                            'N3' => 'N3 (المستوى 3)',
                            'N4' => 'N4 (المستوى 4)',
                            'N5' => 'N5 (المستوى 5)',
                            'N6' => 'N6 (المستوى 6)',
                        ])
                        ->required(),
                    Forms\Components\Select::make('period')
                        ->options([
                            'P1' => 'P1 (الفترة 1)',
                            'P2' => 'P2 (الفترة 2)',
                            'P3' => 'P3 (الفترة 3)',
                            'P4' => 'P4 (الفترة 4)',
                            'P5' => 'P5 (الفترة 5)',
                        ])
                        ->required(),
                    Forms\Components\Select::make('week')
                        ->options([
                            'SEM1' => 'SEM1 (الأسبوع 1)',
                            'SEM2' => 'SEM2 (الأسبوع 2)',
                            'SEM3' => 'SEM3 (الأسبوع 3)',
                            'SEM4' => 'SEM4 (الأسبوع 4)',
                            'SEM5' => 'SEM5 (الأسبوع 5)',
                            'SEM6' => 'SEM6 (الأسبوع 6)',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('lesson_id')
                        ->label('Lesson ID')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('slide_index')
                        ->label('Slide Index')
                        ->numeric(),
                ]),

            Forms\Components\Section::make('Media & Integration')
                ->description('Extracted image and Revizy integration identifiers.')
                ->aside()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('image_path')
                        ->label('Image Path')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('audio_path')
                        ->label('Audio Path')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('revizy_image_file_id')
                        ->label('Revizy Image ID')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('revizy_audio_file_id')
                        ->label('Revizy Audio ID')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('revizy_skill_id')
                        ->label('Revizy Skill ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('revizy_unite_id')
                        ->label('Revizy Unité ID')
                        ->numeric(),
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
                    ->orderBy('id', 'asc');
            })
            ->poll('10s')
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => $record->image_path ? asset($record->image_path) : null)
                    ->circular(false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('word')
                    ->label('المفردة / الكلمة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->extraAttributes(['dir' => 'rtl', 'style' => 'font-size: 1.15rem; font-weight: 700;']),
                Tables\Columns\TextColumn::make('raw_word')
                    ->label('بدون تشكيل')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('example_sentence')
                    ->label('جملة السياق')
                    ->searchable()
                    ->limit(50)
                    ->extraAttributes(['dir' => 'rtl'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('strategy')
                    ->label('الإستراتيجية')
                    ->badge()
                    ->color('info')
                    ->extraAttributes(['dir' => 'rtl'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('scope')
                    ->label('Scope')
                    ->getStateUsing(static function (ArabicVocabularyItem $record): string {
                        return implode(' / ', array_filter([$record->grade, $record->period, $record->week]));
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('lesson_id')
                    ->label('Lesson')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('slide_index')
                    ->label('Slide #')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('audio_path')
                    ->label('Audio')
                    ->icon(fn (?string $state): string => $state ? 'heroicon-o-speaker-wave' : 'heroicon-o-speaker-x-mark')
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->url(fn ($record) => $record->audio_path ? "javascript:void(0);" : null)
                    ->extraAttributes(fn ($record) => $record->audio_path ? [
                        'onclick' => "new Audio('" . asset('audios/' . $record->audio_path) . "').play();",
                        'title' => 'Play audio',
                    ] : [])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('revizy_image_file_id')
                    ->label('Revizy Img ID')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('extracted_at')
                    ->label('Extracted')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Grade (المستوى)')
                    ->options([
                        'N1' => 'N1',
                        'N2' => 'N2',
                        'N3' => 'N3',
                        'N4' => 'N4',
                        'N5' => 'N5',
                        'N6' => 'N6',
                    ]),
                Tables\Filters\SelectFilter::make('period')
                    ->label('Period (الفترة)')
                    ->options([
                        'P1' => 'P1',
                        'P2' => 'P2',
                        'P3' => 'P3',
                        'P4' => 'P4',
                        'P5' => 'P5',
                    ]),
                Tables\Filters\SelectFilter::make('week')
                    ->label('Week (الأسبوع)')
                    ->options([
                        'SEM1' => 'SEM1',
                        'SEM2' => 'SEM2',
                        'SEM3' => 'SEM3',
                        'SEM4' => 'SEM4',
                        'SEM5' => 'SEM5',
                        'SEM6' => 'SEM6',
                    ]),
                Tables\Filters\SelectFilter::make('strategy')
                    ->label('Strategy')
                    ->options(fn (): array => ArabicVocabularyItem::query()
                        ->whereNotNull('strategy')
                        ->distinct()
                        ->pluck('strategy', 'strategy')
                        ->all()),
                Tables\Filters\TernaryFilter::make('has_image')
                    ->label('Has Image')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('image_path')->where('image_path', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('image_path')->orWhere('image_path', '')),
                    ),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->headerActions([
                Tables\Actions\Action::make('extract_arabic_vocabulary')
                    ->label('Extract Arabic Vocabulary')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Extract Arabic Vocabulary (استخراج المعجم العربي)')
                    ->modalDescription('Extract vocabulary items and illustrations from Arabic lesson presentations (AR_N1 to AR_N6).')
                    ->form([
                        Forms\Components\TextInput::make('lesson_id')
                            ->label('Lesson ID (optional)')
                            ->placeholder('e.g. AR_N2_P1_SEM1_S1')
                            ->helperText('If specified, only this lesson file will be processed.'),
                        Forms\Components\Select::make('grade')
                            ->label('Grade')
                            ->options([
                                'N1' => 'N1',
                                'N2' => 'N2',
                                'N3' => 'N3',
                                'N4' => 'N4',
                                'N5' => 'N5',
                                'N6' => 'N6',
                            ])
                            ->placeholder('All Grades'),
                        Forms\Components\Select::make('period')
                            ->label('Period')
                            ->options([
                                'P1' => 'P1',
                                'P2' => 'P2',
                                'P3' => 'P3',
                                'P4' => 'P4',
                                'P5' => 'P5',
                            ])
                            ->placeholder('All Periods'),
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
                            ->placeholder('All Weeks'),
                        Forms\Components\TextInput::make('limit')
                            ->label('Limit')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('e.g. 50')
                            ->helperText('Optional limit on the number of lessons processed.'),
                        Forms\Components\Toggle::make('force')
                            ->label('Force Re-extract')
                            ->default(false)
                            ->helperText('Re-extract even if lessons are already extracted in database.'),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        $options = [
                            'lesson_id' => ! empty($data['lesson_id']) ? trim((string) $data['lesson_id']) : null,
                            'grade' => $data['grade'] ?? null,
                            'period' => $data['period'] ?? null,
                            'week' => $data['week'] ?? null,
                            'limit' => (int) ($data['limit'] ?? 0),
                            'force' => (bool) ($data['force'] ?? false),
                        ];

                        ExtractArabicVocabularyJob::dispatch(
                            $options,
                            $user?->id,
                            $user?->email,
                            $user?->role
                        );

                        Notification::make()
                            ->title('Arabic vocabulary extraction queued')
                            ->body('Background extraction started. Refresh table shortly to view extracted items.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListArabicVocabularies::route('/'),
            'edit' => Pages\EditArabicVocabulary::route('/{record}/edit'),
        ];
    }
}
