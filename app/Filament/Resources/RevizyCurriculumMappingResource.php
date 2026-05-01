<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevizyCurriculumMappingResource\Pages;
use App\Models\Raiida\RevizyCurriculumMapping;
use App\Services\Raiida\RevizyCurriculumMappingImportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RevizyCurriculumMappingResource extends Resource
{
    protected static ?string $model = RevizyCurriculumMapping::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Revizy Mapping';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Revizy Mapping';

    protected static ?string $pluralModelLabel = 'Revizy Mappings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Scope')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('subject_code')
                        ->label('Subject')
                        ->required()
                        ->maxLength(20)
                        ->default('FR'),
                    Forms\Components\Select::make('grade_code')
                        ->label('Grade')
                        ->required()
                        ->options([
                            'N1' => 'N1',
                            'N2' => 'N2',
                            'N3' => 'N3',
                            'N4' => 'N4',
                            'N5' => 'N5',
                            'N6' => 'N6',
                        ]),
                    Forms\Components\Select::make('period_code')
                        ->label('Period')
                        ->required()
                        ->options([
                            'P1' => 'P1',
                            'P2' => 'P2',
                            'P3' => 'P3',
                            'P4' => 'P4',
                            'P5' => 'P5',
                        ]),
                    Forms\Components\TextInput::make('period_index')
                        ->label('Period #')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->helperText('Optional. Used for sorting.'),
                    Forms\Components\TextInput::make('grade_index')
                        ->label('Grade #')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(20)
                        ->helperText('Optional. Used for sorting.'),
                ]),
            Forms\Components\Section::make('Revizy Unite (Period)')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('revizy_unite_id')
                        ->label('Unite ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('revizy_unite_index')
                        ->label('Unite Index')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('revizy_unite_name')
                        ->label('Unite Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('revizy_unite_code')
                        ->label('Unite Code')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Revizy Skills')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('revizy_vocab_skill_id')
                        ->label('Vocabulary Skill ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('revizy_vocab_skill_name')
                        ->label('Vocabulary Skill Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('revizy_conjugaison_skill_id')
                        ->label('Conjugaison Skill ID')
                        ->numeric(),
                    Forms\Components\TextInput::make('revizy_conjugaison_skill_name')
                        ->label('Conjugaison Skill Name')
                        ->maxLength(255),
                ]),
            Forms\Components\Section::make('Meta')
                ->schema([
                    Forms\Components\Textarea::make('meta')
                        ->rows(6)
                        ->afterStateHydrated(function (Forms\Components\Textarea $component, $state): void {
                            if (is_array($state)) {
                                $component->state(json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            }
                        })
                        ->dehydrateStateUsing(static function ($state): ?array {
                            if (! is_string($state)) {
                                return null;
                            }

                            $trimmed = trim($state);
                            if ($trimmed === '') {
                                return null;
                            }

                            $decoded = json_decode($trimmed, true);
                            if (is_array($decoded)) {
                                return $decoded;
                            }

                            return ['raw' => $trimmed];
                        })
                        ->helperText('Optional JSON metadata stored for auditing.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(static function (Builder $query): Builder {
                return $query
                    ->orderBy('subject_code')
                    ->orderByRaw("COALESCE(grade_index, CAST(SUBSTR(grade_code, 2) AS UNSIGNED)) ASC")
                    ->orderByRaw("COALESCE(period_index, CAST(SUBSTR(period_code, 2) AS UNSIGNED)) ASC");
            })
            ->poll('10s')
            ->columns([
                Tables\Columns\TextColumn::make('subject_code')
                    ->label('Subj')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade_code')
                    ->label('Grade')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_code')
                    ->label('Period')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('revizy_unite_name')
                    ->label('Unite')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('revizy_unite_id')
                    ->label('Unite ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('revizy_vocab_skill_id')
                    ->label('Vocab Skill')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('revizy_conjugaison_skill_id')
                    ->label('Conj Skill')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_code')
                    ->label('Subject')
                    ->options(fn (): array => RevizyCurriculumMapping::query()
                        ->select('subject_code')
                        ->whereNotNull('subject_code')
                        ->distinct()
                        ->orderBy('subject_code')
                        ->pluck('subject_code', 'subject_code')
                        ->all()),
                Tables\Filters\SelectFilter::make('grade_code')
                    ->label('Grade')
                    ->options(fn (): array => RevizyCurriculumMapping::query()
                        ->select('grade_code')
                        ->whereNotNull('grade_code')
                        ->distinct()
                        ->orderBy('grade_code')
                        ->pluck('grade_code', 'grade_code')
                        ->all()),
                Tables\Filters\SelectFilter::make('period_code')
                    ->label('Period')
                    ->options(fn (): array => RevizyCurriculumMapping::query()
                        ->select('period_code')
                        ->whereNotNull('period_code')
                        ->distinct()
                        ->orderBy('period_code')
                        ->pluck('period_code', 'period_code')
                        ->all()),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import_from_json')
                    ->label('Import JSON')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->modalHeading('Import Revizy Skills + Unites JSON')
                    ->modalDescription('Paste the JSON arrays exported from production Revizy SQL queries. This will create/update rows for N1..N6 and P1..P5.')
                    ->form([
                        Forms\Components\TextInput::make('subject_code')
                            ->label('Subject Code')
                            ->default('FR')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('skills_json')
                            ->label('skills.json')
                            ->rows(10)
                            ->required(),
                        Forms\Components\Textarea::make('unites_json')
                            ->label('unites.json')
                            ->rows(10)
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $skills = json_decode((string) ($data['skills_json'] ?? ''), true);
                        $unites = json_decode((string) ($data['unites_json'] ?? ''), true);

                        if (! is_array($skills) || ! array_is_list($skills)) {
                            Notification::make()
                                ->title('Import failed')
                                ->body('skills.json must be a JSON array.')
                                ->danger()
                                ->send();
                            return;
                        }

                        if (! is_array($unites) || ! array_is_list($unites)) {
                            Notification::make()
                                ->title('Import failed')
                                ->body('unites.json must be a JSON array.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $summary = app(RevizyCurriculumMappingImportService::class)->importFromArrays(
                            array_values(array_filter($skills, static fn ($row): bool => is_array($row))),
                            array_values(array_filter($unites, static fn ($row): bool => is_array($row))),
                            [
                                'subject_code' => (string) ($data['subject_code'] ?? 'FR'),
                            ]
                        );

                        $created = (int) ($summary['created'] ?? 0);
                        $updated = (int) ($summary['updated'] ?? 0);
                        $errors = (int) count((array) ($summary['errors'] ?? []));

                        $notification = Notification::make()
                            ->title('Import completed')
                            ->body("Created {$created}, updated {$updated}, errors {$errors}.");

                        if ($errors > 0) {
                            $notification->warning();
                        } else {
                            $notification->success();
                        }

                        $notification->send();
                    }),
                Tables\Actions\CreateAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRevizyCurriculumMappings::route('/'),
            'create' => Pages\CreateRevizyCurriculumMapping::route('/create'),
            'edit' => Pages\EditRevizyCurriculumMapping::route('/{record}/edit'),
        ];
    }
}
