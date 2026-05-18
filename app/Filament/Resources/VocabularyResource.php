<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VocabularyResource\Pages;
use App\Jobs\Raiida\ExtractVocabularyJob;
use App\Jobs\Raiida\GenerateVocabularyAudiosJob;
use App\Jobs\Raiida\GenerateVocabularyConceptsJob;
use App\Jobs\Raiida\SyncVocabularyExternalAssetsJob;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\External\RevizySystemClient;
use App\Services\Raiida\External\WalidioClient;
use App\Services\Raiida\MediaFileLocator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class VocabularyResource extends Resource
{
    protected static ?string $model = VocabularyItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Vocabulary';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Vocabulary Item';

    protected static ?string $pluralModelLabel = 'Vocabulary Items';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Word Info')
                ->description('Core vocabulary text fields.')
                ->aside()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('word')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('base_word')
                        ->label('Base Word (no article)')
                        ->maxLength(255)
                        ->helperText("Word without articles like le/la/les/des/un/une/l'."),
                    Forms\Components\TextInput::make('ar_translation')
                        ->label('Arabic Translation')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('lexical_type')
                        ->label('Lexical Type')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('gender')
                        ->maxLength(20),
                ]),

            Forms\Components\Section::make('Classification')
                ->description('Scope and lexical classification fields.')
                ->aside()
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('grade')
                        ->required()
                        ->maxLength(10),
                    Forms\Components\TextInput::make('subject')
                        ->default('FR')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('period')
                        ->required()
                        ->maxLength(10),
                    Forms\Components\TextInput::make('week')
                        ->required()
                        ->maxLength(10),
                    Forms\Components\TextInput::make('lesson_id')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('distractor_group')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('distractor_subgroup')
                        ->maxLength(50),
                ]),

            Forms\Components\Section::make('Media & Integration')
                ->description('Asset paths and external integration IDs.')
                ->aside()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('image_path')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('audio_path')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('base_word_audio_path')
                        ->label('Base Word Audio Path')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('revizy_image_file_id')
                        ->label('Revizy Image ID')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('revizy_audio_file_id')
                        ->label('Revizy Audio ID')
                        ->maxLength(100),
                    Forms\Components\Placeholder::make('base_word_audio_revizy_id')
                        ->label('Base Word Audio Revizy ID')
                        ->content(static fn (?VocabularyItem $record): string => $record?->baseWordAudio?->revizy_file_id ?: '—'),
                    Forms\Components\TextInput::make('walidio_image_id')
                        ->label('Walidio Image ID')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('flashcard_id')
                        ->label('Flashcard ID')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('concept_id')
                        ->label('Concept ID')
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
                    ->with('baseWordAudio')
                    ->orderByRaw("CASE grade WHEN 'N1' THEN 1 WHEN 'N2' THEN 2 WHEN 'N3' THEN 3 WHEN 'N4' THEN 4 WHEN 'N5' THEN 5 WHEN 'N6' THEN 6 ELSE 7 END ASC")
                    ->orderByRaw("CAST(SUBSTR(period, 2) AS INTEGER) ASC")
                    ->orderByRaw("CAST(SUBSTR(week, 4) AS INTEGER) ASC")
                    ->orderBy('word', 'asc');
            })
            ->poll('5s')
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => \Illuminate\Support\Str::startsWith($record->image_path, ['http://', 'https://']) ? $record->image_path : asset($record->image_path))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('word')
                    ->label('Word')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('base_word')
                    ->label('Base')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('lesson_id')
                    ->label('Lesson')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ar_translation')
                    ->label('Arabic')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('scope')
                    ->label('Scope')
                    ->getStateUsing(static function (VocabularyItem $record): string {
                        $grade = trim((string) $record->grade);
                        $period = trim((string) $record->period);
                        $week = trim((string) $record->week);

                        return implode(' / ', array_values(array_filter([$grade, $period, $week])));
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('week')
                    ->label('Week')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('classification')
                    ->label('Type / Gender / Group')
                    ->getStateUsing(static function (VocabularyItem $record): string {
                        $type = trim((string) $record->lexical_type);
                        $gender = trim((string) $record->gender);
                        $group = trim((string) $record->distractor_group);

                        return implode(' / ', array_values(array_filter([$type, $gender, $group])));
                    })
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('lexical_type')
                    ->label('Type')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'masculine' => 'info',
                        'feminine' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('distractor_group')
                    ->label('Group')
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('distractor_subgroup')
                    ->label('Subgroup')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('concept_id')
                    ->label('Concept')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (?string $state): string => $state ? '#' . $state : 'None'),
                Tables\Columns\ViewColumn::make('sync_status')
                    ->label('Sync Status')
                    ->view('filament.tables.columns.vocabulary-sync-status'),
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
                Tables\Columns\IconColumn::make('base_word_audio_path')
                    ->label('Base Audio')
                    ->icon(fn (?string $state): string => $state ? 'heroicon-o-speaker-wave' : 'heroicon-o-speaker-x-mark')
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->url(fn ($record) => $record->base_word_audio_path ? "javascript:void(0);" : null)
                    ->extraAttributes(fn ($record) => $record->base_word_audio_path ? [
                        'onclick' => "new Audio('" . asset('audios/' . $record->base_word_audio_path) . "').play();",
                        'title' => 'Play base-word audio',
                    ] : [])
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('revizy_image_file_id')
                    ->label('Img ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('revizy_audio_file_id')
                    ->label('Audio ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('baseWordAudio.revizy_file_id')
                    ->label('Base Audio ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('flashcard_id')
                    ->label('Flashcard')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('lesson_id')
                    ->label('Lesson')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('extracted_at')
                    ->label('Extracted')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('lesson_id')
                    ->form([
                        Forms\Components\TextInput::make('lesson_id')
                            ->label('Lesson code')
                            ->placeholder('FR_N6_P5_SEM2_S1'),
                    ])
                    ->query(static function (Builder $query, array $data): Builder {
                        $lessonId = trim((string) ($data['lesson_id'] ?? ''));
                        if ($lessonId === '') {
                            return $query;
                        }

                        return $query->where('lesson_id', 'like', '%' . $lessonId . '%');
                    }),
                Tables\Filters\SelectFilter::make('grade')
                    ->label('Grade')
                    ->options([
                        'N1' => 'N1',
                        'N2' => 'N2',
                        'N3' => 'N3',
                        'N4' => 'N4',
                        'N5' => 'N5',
                        'N6' => 'N6',
                    ]),
                Tables\Filters\SelectFilter::make('period')
                    ->label('Period')
                    ->options([
                        'P1' => 'P1',
                        'P2' => 'P2',
                        'P3' => 'P3',
                        'P4' => 'P4',
                        'P5' => 'P5',
                    ]),
                Tables\Filters\SelectFilter::make('week')
                    ->label('Week')
                    ->options([
                        'SEM1' => 'SEM1',
                        'SEM2' => 'SEM2',
                        'SEM3' => 'SEM3',
                        'SEM4' => 'SEM4',
                        'SEM5' => 'SEM5',
                        'SEM6' => 'SEM6',
                    ]),
                Tables\Filters\SelectFilter::make('lexical_type')
                    ->label('Lexical Type')
                    ->options(fn (): array => VocabularyItem::query()
                        ->whereNotNull('lexical_type')
                        ->distinct()
                        ->pluck('lexical_type', 'lexical_type')
                        ->all()),
                Tables\Filters\SelectFilter::make('distractor_group')
                    ->label('Group')
                    ->options(fn (): array => VocabularyItem::query()
                        ->whereNotNull('distractor_group')
                        ->distinct()
                        ->pluck('distractor_group', 'distractor_group')
                        ->all()),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Gender')
                    ->options([
                        'masculine' => 'Masculine',
                        'feminine' => 'Feminine',
                    ]),
                Tables\Filters\SelectFilter::make('sync_status')
                    ->label('Sync Status')
                    ->options([
                        'fully_synced' => 'Fully Synced (RI + RA + WI)',
                        'missing_any' => 'Missing Any ID',
                        'missing_revizy_image' => 'Missing Revizy Image ID',
                        'missing_revizy_audio' => 'Missing Revizy Audio ID',
                        'missing_walidio' => 'Missing Walidio Image ID',
                        'ready_for_walidio' => 'Ready for Walidio (RI present, WI missing)',
                    ])
                    ->query(static function (Builder $query, array $data): Builder {
                        $value = (string) ($data['value'] ?? '');

                        return match ($value) {
                            'fully_synced' => $query
                                ->whereNotNull('revizy_image_file_id')->where('revizy_image_file_id', '!=', '')
                                ->whereNotNull('revizy_audio_file_id')->where('revizy_audio_file_id', '!=', '')
                                ->whereNotNull('walidio_image_id')->where('walidio_image_id', '!=', ''),
                            'missing_any' => $query->where(static function (Builder $q): void {
                                $q->whereNull('revizy_image_file_id')->orWhere('revizy_image_file_id', '')
                                    ->orWhereNull('revizy_audio_file_id')->orWhere('revizy_audio_file_id', '')
                                    ->orWhereNull('walidio_image_id')->orWhere('walidio_image_id', '');
                            }),
                            'missing_revizy_image' => $query->where(static function (Builder $q): void {
                                $q->whereNull('revizy_image_file_id')->orWhere('revizy_image_file_id', '');
                            }),
                            'missing_revizy_audio' => $query->where(static function (Builder $q): void {
                                $q->whereNull('revizy_audio_file_id')->orWhere('revizy_audio_file_id', '');
                            }),
                            'missing_walidio' => $query->where(static function (Builder $q): void {
                                $q->whereNull('walidio_image_id')->orWhere('walidio_image_id', '');
                            }),
                            'ready_for_walidio' => $query
                                ->whereNotNull('revizy_image_file_id')->where('revizy_image_file_id', '!=', '')
                                ->where(static function (Builder $q): void {
                                    $q->whereNull('walidio_image_id')->orWhere('walidio_image_id', '');
                                }),
                            default => $query,
                        };
                    }),
                Tables\Filters\TernaryFilter::make('has_image')
                    ->label('Has Image')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('revizy_image_file_id')->where('revizy_image_file_id', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('revizy_image_file_id')->orWhere('revizy_image_file_id', '')),
                    ),
                Tables\Filters\TernaryFilter::make('has_audio')
                    ->label('Has Audio')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('audio_path')->where('audio_path', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('audio_path')->orWhere('audio_path', '')),
                    ),
                Tables\Filters\TernaryFilter::make('has_concept')
                    ->label('Has Concept')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('concept_id')->where('concept_id', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn (Builder $q) => $q->whereNull('concept_id')->orWhere('concept_id', '')),
                    ),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('extract_vocabulary')
                    ->label('Extract Vocabulary')
                    ->icon('heroicon-o-sparkles')
                    ->requiresConfirmation()
                    ->modalHeading('Extract Vocabulary')
                    ->modalDescription('Scan downloaded S1 French presentation files and extract vocabulary items. By default, it only processes lessons not yet extracted. Enable Force to re-extract already-processed lessons.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->form([
                        Forms\Components\TextInput::make('lesson_id')
                            ->label('Lesson ID')
                            ->placeholder('FR_N6_P3_SEM1_S1')
                            ->helperText('Optional. If set, only this lesson will be processed (tries .pptx then .ppsx).'),
                        Forms\Components\TextInput::make('limit')
                            ->label('Limit')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('e.g. 50')
                            ->helperText('Optional. Limits how many files are processed in this run.'),
                        Forms\Components\Toggle::make('force')
                            ->label('Force Re-Extract')
                            ->helperText('Re-process lessons even if they are already marked as extracted.'),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        $options = [
                            'lesson_id' => trim((string) ($data['lesson_id'] ?? '')),
                            'limit' => (int) ($data['limit'] ?? 0),
                            'force' => (bool) ($data['force'] ?? false),
                        ];

                        ExtractVocabularyJob::dispatch(
                            $options,
                            null,
                            $user?->id,
                            $user?->email,
                            $user?->role
                        );

                        Notification::make()
                            ->title('Vocabulary extraction queued')
                            ->body('Background extraction started from downloaded presentation files.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('sync_external_assets')
                    ->label('Sync External Assets')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->modalHeading('Sync External Assets')
                    ->modalDescription('Queue one background job to sync Revizy image/audio and Walidio image IDs for all matching vocabulary rows.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->form([
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
                            ->placeholder('All grades'),
                        Forms\Components\Select::make('period')
                            ->label('Period')
                            ->options([
                                'P1' => 'P1',
                                'P2' => 'P2',
                                'P3' => 'P3',
                                'P4' => 'P4',
                                'P5' => 'P5',
                            ])
                            ->placeholder('All periods'),
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
                            ->placeholder('All weeks'),
                        Forms\Components\TextInput::make('limit')
                            ->label('Limit')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50000)
                            ->default(5000)
                            ->helperText('Maximum rows this job will process.'),
                        Forms\Components\Toggle::make('sync_image_revizy')
                            ->label('Sync Image -> Revizy')
                            ->default(true),
                        Forms\Components\Toggle::make('sync_audio_revizy')
                            ->label('Sync Audio -> Revizy')
                            ->default(true),
                        Forms\Components\Toggle::make('sync_base_word_audio_revizy')
                            ->label('Sync Base Word Audio -> Revizy')
                            ->default(true),
                        Forms\Components\Toggle::make('sync_image_walidio')
                            ->label('Sync Image -> Walidio')
                            ->helperText('Requires Revizy Image ID and WALIDIO_PUBLIC_KEY.')
                            ->default(true),
                        Forms\Components\Toggle::make('only_missing')
                            ->label('Only missing IDs')
                            ->default(true),
                        Forms\Components\TextInput::make('wait_ms')
                            ->label('Delay Between Items (ms)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5000)
                            ->default(0)
                            ->helperText('Optional small delay between items.'),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        $options = [
                            'limit' => (int) ($data['limit'] ?? 5000),
                            'grade' => $data['grade'] ?? null,
                            'period' => $data['period'] ?? null,
                            'week' => $data['week'] ?? null,
                            'sync_image_revizy' => (bool) ($data['sync_image_revizy'] ?? true),
                            'sync_audio_revizy' => (bool) ($data['sync_audio_revizy'] ?? true),
                            'sync_base_word_audio_revizy' => (bool) ($data['sync_base_word_audio_revizy'] ?? true),
                            'sync_image_walidio' => (bool) ($data['sync_image_walidio'] ?? true),
                            'only_missing' => (bool) ($data['only_missing'] ?? true),
                            'wait_ms' => (int) ($data['wait_ms'] ?? 0),
                        ];
                        $options = array_filter($options, static fn ($value) => $value !== null);

                        SyncVocabularyExternalAssetsJob::dispatch(
                            $options,
                            null,
                            $user?->id,
                            $user?->email,
                            $user?->role
                        );

                        Notification::make()
                            ->title('External asset sync queued')
                            ->body('Background job started. Check logs for summary: sync_vocab_external_assets.completed')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('generate_audios')
                    ->label('Generate Audios')
                    ->icon('heroicon-o-speaker-wave')
                    ->color('success')
                    ->modalHeading('Generate Vocabulary Audios')
                    ->modalDescription('Generate missing audio files using Typecast credentials configured in Admin > Audio Credentials.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->form([
                        Forms\Components\TextInput::make('item_id')
                            ->label('Vocabulary Item ID (optional)')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('If set, only this vocabulary item will be processed (useful for debugging a specific missing audio).'),
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
                            ->placeholder('All grades'),
                        Forms\Components\Select::make('period')
                            ->label('Period')
                            ->options([
                                'P1' => 'P1',
                                'P2' => 'P2',
                                'P3' => 'P3',
                                'P4' => 'P4',
                                'P5' => 'P5',
                            ])
                            ->placeholder('All periods'),
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
                            ->placeholder('All weeks'),
                        Forms\Components\TextInput::make('limit')
                            ->label('Limit')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(20)
                            ->helperText('Max vocabulary items to process in this run.'),
                        Forms\Components\Toggle::make('queue')
                            ->label('Run in background queue')
                            ->default(true),
                        Forms\Components\Toggle::make('force')
                            ->label('Regenerate even if audio exists')
                            ->default(false),
                        Forms\Components\Toggle::make('verbose')
                            ->label('Verbose logs (per item)')
                            ->helperText('Write a log line for each item processed. Check storage/logs/laravel.log to see item IDs and errors.')
                            ->default(false),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        $options = [
                            'item_id' => ! empty($data['item_id']) ? (int) $data['item_id'] : null,
                            'limit' => (int) ($data['limit'] ?? 20),
                            'grade' => $data['grade'] ?? null,
                            'period' => $data['period'] ?? null,
                            'week' => $data['week'] ?? null,
                            'force' => (bool) ($data['force'] ?? false),
                            'verbose' => (bool) ($data['verbose'] ?? false),
                        ];

                        if ((bool) ($data['queue'] ?? true)) {
                            GenerateVocabularyAudiosJob::dispatch(
                                $options,
                                null,
                                $user?->id,
                                $user?->email,
                                $user?->role
                            );

                            Notification::make()
                                ->title('Audio generation queued')
                                ->body('Background audio generation started.')
                                ->success()
                                ->send();

                            return;
                        }

                        try {
                            $summary = app(\App\Services\Raiida\AudioGenerationService::class)->generateBatch($options);
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Audio generation failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        $generated = (int) ($summary['generated_total'] ?? 0);
                        $failed = (int) ($summary['failed_total'] ?? 0);
                        $remaining = (int) ($summary['remaining_missing_in_scope'] ?? 0);

                        $notification = Notification::make()
                            ->title('Audio generation completed')
                            ->body("Generated {$generated}, failed {$failed}, remaining missing {$remaining}.");

                        if ($failed > 0) {
                            $notification->warning();
                        } else {
                            $notification->success();
                        }

                        $notification->send();
                    }),
                Tables\Actions\Action::make('classify_metadata')
                    ->label('Classify Type/Gender/Group')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->modalHeading('Classify Vocabulary Metadata')
                    ->modalDescription('Generate lexical type, gender, and distractor groups using Gemini. Use dry run first to estimate impact.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->form([
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
                            ->placeholder('All grades'),
                        Forms\Components\Select::make('period')
                            ->label('Period')
                            ->options([
                                'P1' => 'P1',
                                'P2' => 'P2',
                                'P3' => 'P3',
                                'P4' => 'P4',
                                'P5' => 'P5',
                            ])
                            ->placeholder('All periods'),
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
                            ->placeholder('All weeks'),
                        Forms\Components\TextInput::make('limit')
                            ->label('Limit')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(200)
                            ->helperText('Max words processed per run (1-500).'),
                        Forms\Components\Toggle::make('queue')
                            ->label('Run in background queue')
                            ->default(true),
                        Forms\Components\Toggle::make('dry_run')
                            ->label('Dry run (no DB write)')
                            ->default(false),
                        Forms\Components\Toggle::make('force')
                            ->label('Force reclassify existing metadata')
                            ->default(false),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        $options = [
                            'limit' => (int) ($data['limit'] ?? 200),
                            'grade' => $data['grade'] ?? null,
                            'period' => $data['period'] ?? null,
                            'week' => $data['week'] ?? null,
                            'dry_run' => (bool) ($data['dry_run'] ?? false),
                            'force' => (bool) ($data['force'] ?? false),
                        ];

                        if ((bool) ($data['queue'] ?? true)) {
                            \App\Jobs\Raiida\ClassifyVocabularyMetadataJob::dispatch(
                                $options,
                                null,
                                $user?->id,
                                $user?->email,
                                $user?->role
                            );

                            Notification::make()
                                ->title('Metadata classification queued')
                                ->body('Background job started. Refresh after completion to see updated Type/Gender/Group.')
                                ->success()
                                ->send();

                            return;
                        }

                        try {
                            $summary = app(\App\Services\Raiida\VocabularyMetadataClassificationService::class)->classify($options);
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Classification failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        $updated = (int) ($summary['updated_total'] ?? 0);
                        $fromAi = (int) ($summary['updated_from_ai'] ?? 0);
                        $fromCache = (int) ($summary['updated_from_cache'] ?? 0);
                        $remaining = (int) ($summary['remaining_missing_in_scope'] ?? 0);
                        $failedBatches = (int) ($summary['ai_failed_batches'] ?? 0);

                        $notification = Notification::make()
                            ->title('Metadata classification completed')
                            ->body("Updated {$updated} items (AI: {$fromAi}, cache: {$fromCache}). Remaining missing: {$remaining}.");

                        if ($failedBatches > 0) {
                            $notification
                                ->warning()
                                ->body("Updated {$updated} items (AI: {$fromAi}, cache: {$fromCache}). Failed AI batches: {$failedBatches}. Remaining missing: {$remaining}.");
                        } else {
                            $notification->success();
                        }

                        $notification->send();
                    }),
                Tables\Actions\Action::make('generate_concepts')
                    ->label('Generate Concepts')
                    ->icon('heroicon-o-light-bulb')
                    ->color('primary')
                    ->modalHeading('Recover Missing Concept IDs')
                    ->modalDescription('Missing-only find-or-create flow: search concepts by N/P/SEM + name, link if found, create if missing, and auto-sync Revizy mappings by code when required.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->form([
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
                            ->placeholder('All grades'),
                        Forms\Components\Select::make('period')
                            ->label('Period')
                            ->options([
                                'P1' => 'P1',
                                'P2' => 'P2',
                                'P3' => 'P3',
                                'P4' => 'P4',
                                'P5' => 'P5',
                            ])
                            ->placeholder('All periods'),
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
                            ->placeholder('All weeks'),
                        Forms\Components\TextInput::make('limit')
                            ->label('Limit')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(120)
                            ->helperText('Maximum vocabulary items to process in this run.'),
                        Forms\Components\TextInput::make('description_template')
                            ->label('Description Template')
                            ->default('Le mot de vocabulaire :word')
                            ->helperText('Placeholders: :word, :grade, :period, :week'),
                        Forms\Components\Select::make('status')
                            ->label('Concept Status')
                            ->options([
                                'published' => 'published',
                                'draft' => 'draft',
                            ])
                            ->default('published'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Set concepts active')
                            ->default(true),
                        Forms\Components\TextInput::make('wait_ms')
                            ->label('Delay Between Requests (ms)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5000)
                            ->default((int) config('raiida.concept_generator.wait_ms_between_items', 200))
                            ->helperText('Adds a short delay between concept API calls to avoid bursts.'),
                        Forms\Components\Toggle::make('debug_search')
                            ->label('Debug search logs (temporary)')
                            ->default(false)
                            ->helperText('Logs code prefix + search hit counts per item for this run.'),
                        Forms\Components\Toggle::make('queue')
                            ->label('Run in background queue')
                            ->default(true),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        $options = [
                            'limit' => (int) ($data['limit'] ?? 120),
                            'grade' => $data['grade'] ?? null,
                            'period' => $data['period'] ?? null,
                            'week' => $data['week'] ?? null,
                            'description_template' => $data['description_template'] ?? 'Le mot de vocabulaire :word',
                            'status' => $data['status'] ?? 'published',
                            'is_active' => (bool) ($data['is_active'] ?? true),
                            'wait_ms' => (int) ($data['wait_ms'] ?? (int) config('raiida.concept_generator.wait_ms_between_items', 200)),
                            'debug_search' => (bool) ($data['debug_search'] ?? false),
                        ];

                        if ((bool) ($data['queue'] ?? true)) {
                            GenerateVocabularyConceptsJob::dispatch(
                                $options,
                                null,
                                $user?->id,
                                $user?->email,
                                $user?->role
                            );

                            Notification::make()
                                ->title('Concept recovery queued')
                                ->body('Background missing-only find-or-create run started.')
                                ->success()
                                ->send();

                            return;
                        }

                        try {
                            $summary = app(\App\Services\Raiida\VocabularyConceptGenerationService::class)->generateBatch($options);
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Concept generation failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        $targeted = (int) ($summary['targeted'] ?? 0);
                        $linked = (int) ($summary['linked_existing'] ?? 0);
                        $created = (int) ($summary['created_total'] ?? 0);
                        $failed = (int) ($summary['failed_total'] ?? 0);
                        $synced = (int) ($summary['mapping_synced_total'] ?? 0);
                        $remaining = (int) ($summary['remaining_missing_in_scope'] ?? 0);

                        $notification = Notification::make()
                            ->title('Concept recovery completed')
                            ->body("Processed {$targeted} items. Linked existing {$linked}, created {$created}, mapping synced {$synced}, failed {$failed}, remaining missing {$remaining}.");

                        if ($failed > 0) {
                            $notification->warning();
                        } else {
                            $notification->success();
                        }

                        $notification->send();
                    }),
                Tables\Actions\Action::make('import_from_source')
                    ->label('Import from Source DB')
                    ->icon('heroicon-o-circle-stack')
                    ->requiresConfirmation()
                    ->modalHeading('Import from Source SQLite')
                    ->modalDescription('This will import vocabulary items from the Python source database (raiida.db). Existing items with the same word+lesson_id+grade are updated.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->action(function (): void {
                        $service = app(\App\Services\Raiida\VocabularySourceImporter::class);
                        $summary = $service->importFromSource();

                        Notification::make()
                            ->title('Source import completed')
                            ->body("Imported: {$summary['imported']}, Updated: {$summary['updated']}, Skipped: {$summary['skipped']}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('translate_untranslated')
                    ->label('Translate Arabic')
                    ->icon('heroicon-o-language')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Translate Missing Arabic Vocabulary')
                    ->modalDescription('Find all vocabulary items missing Arabic translation and queue them for Auto-translation via DeepL.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->action(function (): void {
                        $missingIds = VocabularyItem::query()
                            ->whereNull('ar_translation')
                            ->orWhere('ar_translation', '')
                            ->pluck('id')
                            ->toArray();

                        if (empty($missingIds)) {
                            Notification::make()
                                ->title('Everything is translated')
                                ->success()
                                ->send();
                            return;
                        }

                        $user = auth()->user();
                        \App\Jobs\Raiida\TranslateVocabularyJob::dispatch($missingIds, $user?->id);

                        Notification::make()
                            ->title('Translation job queued')
                            ->body('Queued ' . count($missingIds) . ' items for translation via DeepL.')
                            ->success()
                            ->send();
                    }),
                ])
                    ->label('Vocabulary Tools')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->button()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('sync_image_revizy')
                        ->label('Sync Image -> Revizy')
                        ->icon('heroicon-o-photo')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                        ->action(function (VocabularyItem $record): void {
                            try {
                                $synced = self::syncImageToRevizy($record, true);

                                Notification::make()
                                    ->title($synced ? 'Revizy image synced' : 'Revizy image already synced')
                                    ->body($synced ? 'Image uploaded and Revizy Image ID saved.' : 'This item already has Revizy Image ID.')
                                    ->success()
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()
                                    ->title('Revizy image sync failed')
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('sync_audio_revizy')
                        ->label('Sync Audio -> Revizy')
                        ->icon('heroicon-o-speaker-wave')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                        ->action(function (VocabularyItem $record): void {
                            try {
                                $synced = self::syncAudioToRevizy($record, true);

                                Notification::make()
                                    ->title($synced ? 'Revizy audio synced' : 'Revizy audio already synced')
                                    ->body($synced ? 'Audio uploaded and Revizy Audio ID saved.' : 'This item already has Revizy Audio ID.')
                                    ->success()
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()
                                    ->title('Revizy audio sync failed')
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('sync_image_walidio')
                        ->label('Sync Image -> Walidio')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                        ->disabled(fn (VocabularyItem $record): bool => (! self::isWalidioConfigured())
                            || trim((string) $record->revizy_image_file_id) === '')
                        ->tooltip(fn (VocabularyItem $record): ?string => ! self::isWalidioConfigured()
                            ? 'WALIDIO_PUBLIC_KEY is not configured.'
                            : (trim((string) $record->revizy_image_file_id) === '' ? 'Sync image to Revizy first.' : null))
                        ->action(function (VocabularyItem $record): void {
                            try {
                                $synced = self::syncImageToWalidio($record, true);

                                Notification::make()
                                    ->title($synced ? 'Walidio image synced' : 'Walidio image already synced')
                                    ->body($synced ? 'Image uploaded and Walidio Image ID saved.' : 'This item already has Walidio Image ID.')
                                    ->success()
                                    ->send();
                            } catch (Throwable $exception) {
                                Notification::make()
                                    ->title('Walidio image sync failed')
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                    ->iconButton()
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Sync Assets')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('sync_assets_selected')
                        ->label('Sync Assets (Selected)')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Toggle::make('sync_image_revizy')
                                ->label('Sync Image -> Revizy')
                                ->default(true),
                            Forms\Components\Toggle::make('sync_audio_revizy')
                                ->label('Sync Audio -> Revizy')
                                ->default(true),
                            Forms\Components\Toggle::make('sync_image_walidio')
                                ->label('Sync Image -> Walidio')
                                ->default(true)
                                ->helperText('Requires Revizy Image ID and WALIDIO_PUBLIC_KEY configuration.'),
                            Forms\Components\Toggle::make('only_missing')
                                ->label('Only missing IDs')
                                ->default(true),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $syncImageRevizy = (bool) ($data['sync_image_revizy'] ?? true);
                            $syncAudioRevizy = (bool) ($data['sync_audio_revizy'] ?? true);
                            $syncWalidio = (bool) ($data['sync_image_walidio'] ?? true);
                            $onlyMissing = (bool) ($data['only_missing'] ?? true);

                            $processed = 0;
                            $failed = 0;
                            $revizyImageSynced = 0;
                            $revizyAudioSynced = 0;
                            $walidioSynced = 0;
                            $walidioBlocked = 0;
                            $walidioSkippedConfig = 0;
                            $errors = [];

                            foreach ($records as $record) {
                                if (! $record instanceof VocabularyItem) {
                                    continue;
                                }

                                $processed++;

                                try {
                                    if ($syncImageRevizy && self::syncImageToRevizy($record, $onlyMissing)) {
                                        $revizyImageSynced++;
                                    }

                                    if ($syncAudioRevizy && self::syncAudioToRevizy($record, $onlyMissing)) {
                                        $revizyAudioSynced++;
                                    }

                                    if ($syncWalidio) {
                                        if (! self::isWalidioConfigured()) {
                                            $walidioSkippedConfig++;
                                        } elseif (trim((string) $record->revizy_image_file_id) === '') {
                                            $walidioBlocked++;
                                        } elseif (self::syncImageToWalidio($record, $onlyMissing)) {
                                            $walidioSynced++;
                                        }
                                    }
                                } catch (Throwable $exception) {
                                    $failed++;
                                    if (count($errors) < 10) {
                                        $errors[] = "#{$record->id} {$record->word}: {$exception->getMessage()}";
                                    }
                                }
                            }

                            $body = "Processed {$processed}. RI synced {$revizyImageSynced}, RA synced {$revizyAudioSynced}, WI synced {$walidioSynced}.";
                            if ($walidioBlocked > 0) {
                                $body .= " WI blocked {$walidioBlocked} (missing Revizy Image ID).";
                            }
                            if ($walidioSkippedConfig > 0) {
                                $body .= " WI skipped {$walidioSkippedConfig} (Walidio not configured).";
                            }
                            if ($failed > 0) {
                                $body .= " Failed {$failed}.";
                                if ($errors !== []) {
                                    $body .= ' Example: ' . $errors[0];
                                }
                            }

                            $notification = Notification::make()
                                ->title('Bulk asset sync completed')
                                ->body($body);

                            if ($failed > 0) {
                                $notification->warning();
                            } else {
                                $notification->success();
                            }

                            $notification->send();
                        }),
                    Tables\Actions\BulkAction::make('translate_selected')
                        ->label('Translate Selected (Arabic)')
                        ->icon('heroicon-o-language')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $user = auth()->user();
                            $ids = $records->pluck('id')->toArray();
                            
                            \App\Jobs\Raiida\TranslateVocabularyJob::dispatch($ids, $user?->id);

                            Notification::make()
                                ->title('Translation job queued')
                                ->body('Queued translation for selected items.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVocabulary::route('/'),
            'edit' => Pages\EditVocabulary::route('/{record}/edit'),
        ];
    }

    private static function syncImageToRevizy(VocabularyItem $item, bool $onlyIfMissing): bool
    {
        if ($onlyIfMissing && trim((string) $item->revizy_image_file_id) !== '') {
            return false;
        }

        if (trim((string) $item->image_path) === '') {
            throw new \RuntimeException('No image associated with this asset.');
        }

        $path = app(MediaFileLocator::class)->resolveImagePath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('File not found on server: ' . (string) $item->image_path);
        }

        $response = app(RevizySystemClient::class)->uploadFile($path, $item->word ?: 'Asset ' . $item->id);
        $secret = trim((string) ($response['secret_id'] ?? ''));
        if ($secret === '') {
            throw new \RuntimeException('Revizy response missing secret_id.');
        }

        $item->revizy_image_file_id = $secret;
        $item->save();

        return true;
    }

    private static function syncAudioToRevizy(VocabularyItem $item, bool $onlyIfMissing): bool
    {
        if ($onlyIfMissing && trim((string) $item->revizy_audio_file_id) !== '') {
            return false;
        }

        if (trim((string) $item->audio_path) === '') {
            throw new \RuntimeException('No audio associated with this asset.');
        }

        $path = app(MediaFileLocator::class)->resolveAudioPath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('File not found on server: ' . (string) $item->audio_path);
        }

        $response = app(RevizySystemClient::class)->uploadFile($path, $item->word ?: 'Asset ' . $item->id);
        $secret = trim((string) ($response['secret_id'] ?? ''));
        if ($secret === '') {
            throw new \RuntimeException('Revizy response missing secret_id.');
        }

        $item->revizy_audio_file_id = $secret;
        $item->save();

        return true;
    }

    private static function syncImageToWalidio(VocabularyItem $item, bool $onlyIfMissing): bool
    {
        if ($onlyIfMissing && trim((string) $item->walidio_image_id) !== '') {
            return false;
        }

        if (! self::isWalidioConfigured()) {
            throw new \RuntimeException('WALIDIO_PUBLIC_KEY is not configured.');
        }

        if (trim((string) $item->revizy_image_file_id) === '') {
            throw new \RuntimeException('Must sync image to Revizy first before uploading to Walidio.');
        }

        if (trim((string) $item->image_path) === '') {
            throw new \RuntimeException('No image file associated with this asset.');
        }

        $path = app(MediaFileLocator::class)->resolveImagePath($item);
        if (! is_string($path)) {
            throw new \RuntimeException('File not found on server: ' . (string) $item->image_path);
        }

        $payload = app(WalidioClient::class)->uploadImage($path, [
            'name' => $item->word ?: 'Asset ' . $item->id,
            'n' => $item->grade,
            'p' => $item->period,
            'sem' => $item->week,
            'revizy_file_id' => $item->revizy_image_file_id,
        ]);

        $walidioId = null;
        if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data']['id'])) {
            $walidioId = trim((string) $payload['data']['id']);
        } elseif (isset($payload['id'])) {
            $walidioId = trim((string) $payload['id']);
        }

        if (! is_string($walidioId) || $walidioId === '') {
            throw new \RuntimeException('Walidio response missing ID.');
        }

        $item->walidio_image_id = $walidioId;
        $item->save();

        return true;
    }

    private static function isWalidioConfigured(): bool
    {
        $publicKey = trim((string) config('raiida.walidio.public_key', ''));
        $baseUrl = trim((string) config('raiida.walidio.base_url', ''));

        return $publicKey !== '' && $baseUrl !== '';
    }

    public static function applySearchToTableQuery(Builder $query, string $search, array $searchableColumns): Builder
    {
        $query = parent::applySearchToTableQuery($query, $search, $searchableColumns);

        if ($search) {
            $query->orderByRaw("CASE WHEN word = ? THEN 0 ELSE 1 END", [$search]);
        }

        return $query;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }
}
