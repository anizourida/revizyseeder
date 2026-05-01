<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConjugaisonResource\Pages;
use App\Jobs\Raiida\ExtractConjugaisonLessonsJob;
use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\ConjugaisonGrade;
use App\Models\Raiida\ConjugaisonPeriod;
use App\Models\Raiida\ConjugaisonWeek;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConjugaisonResource extends Resource
{
    protected static ?string $model = Conjugaison::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Conjugaison';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Conjugaison Lesson';

    protected static ?string $pluralModelLabel = 'Conjugaison Lessons';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(static function (Builder $query): Builder {
                return $query
                    ->orderByRaw('CAST(SUBSTR(n, 2) AS INTEGER) ASC')
                    ->orderByRaw('CAST(SUBSTR(p, 2) AS INTEGER) ASC')
                    ->orderByRaw('CAST(SUBSTR(sem, 4) AS INTEGER) ASC');
            })
            ->poll('5s')
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('grade.code')
                    ->label('Grade')
                    ->formatStateUsing(static function ($state, Conjugaison $record): string {
                        $stateValue = trim((string) $state);
                        if ($stateValue !== '') {
                            return $stateValue;
                        }

                        $fallback = trim((string) $record->n);

                        return $fallback !== '' ? $fallback : 'N?';
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('period.code')
                    ->label('Period')
                    ->formatStateUsing(static function ($state, Conjugaison $record): string {
                        $stateValue = trim((string) $state);
                        if ($stateValue !== '') {
                            return $stateValue;
                        }

                        $fallback = trim((string) $record->p);

                        return $fallback !== '' ? $fallback : 'P?';
                    })
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('semWeek.code')
                    ->label('Week')
                    ->formatStateUsing(static function ($state, Conjugaison $record): string {
                        $stateValue = trim((string) $state);
                        if ($stateValue !== '') {
                            return $stateValue;
                        }

                        $fallback = trim((string) $record->sem);

                        return $fallback !== '' ? $fallback : 'SEM?';
                    })
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->formatStateUsing(static fn (?string $state): string => trim((string) $state))
                    ->searchable()
                    ->limit(65),
                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->formatStateUsing(static fn (?string $state): string => trim((string) $state))
                    ->searchable()
                    ->limit(65)
                    ->hidden(),
                Tables\Columns\TextColumn::make('verbe')
                    ->label('Verbe')
                    ->formatStateUsing(static fn (?string $state): string => trim((string) $state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tense')
                    ->label('Tense')
                    ->formatStateUsing(static fn (?string $state): string => trim((string) $state))
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('confidence_score')
                    ->label('Score')
                    ->description('Confidence, higher is better')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('concept_id')
                    ->label('Concept')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (?string $state): string => $state ? '#'.$state : 'None'),
                Tables\Columns\TextColumn::make('source_lesson_id')->label('Lesson')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('source_slide_id')->label('Slide')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('raw_data')->label('Raw')->limit(70)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_id')
                    ->label('Grade')
                    ->options(fn (): array => ConjugaisonGrade::query()->orderBy('grade_number')->pluck('code', 'id')->all()),
                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Period')
                    ->options(fn (): array => ConjugaisonPeriod::query()->orderBy('period_number')->pluck('code', 'id')->all()),
                Tables\Filters\SelectFilter::make('week_id')
                    ->label('Week')
                    ->options(fn (): array => ConjugaisonWeek::query()->orderBy('week_number')->pluck('code', 'id')->all()),
                Tables\Filters\Filter::make('with_conjugaison')
                    ->label('With Conjugaison')
                    ->default()
                    ->form([
                        \Filament\Forms\Components\Checkbox::make('enabled')
                            ->label('With Conjugaison Only')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->query(static function (Builder $query, array $data): Builder {
                        if (! ((bool) ($data['enabled'] ?? false))) {
                            return $query;
                        }

                        return $query->where(static function (Builder $inner): void {
                            $inner
                                ->where('confidence_score', '>', 0)
                                ->orWhereRaw("TRIM(COALESCE(name, '')) <> ''")
                                ->orWhereRaw("TRIM(COALESCE(verbe, '')) <> ''");
                        });
                    })
                    ->indicateUsing(static function (array $data): ?string {
                        return ((bool) ($data['enabled'] ?? false))
                            ? 'With Conjugaison Only'
                            : null;
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->headerActions([
                Tables\Actions\Action::make('extract_conjugaison')
                    ->label('Extract Conjugaison')
                    ->icon('heroicon-o-sparkles')
                    ->requiresConfirmation()
                    ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin'))
                    ->action(function (): void {
                        $user = auth()->user();

                        ExtractConjugaisonLessonsJob::dispatch(
                            true,
                            null,
                            $user?->id,
                            $user?->email,
                            $user?->role
                        );

                        Notification::make()
                            ->title('Conjugaison extraction queued')
                            ->body('Background extraction started from presentation_data.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('create_questions')
                    ->label('Questions')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn (Conjugaison $record): string => static::getUrl('questions', ['record' => $record])),
                Tables\Actions\Action::make('view_raw')
                    ->label('View Raw')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Raw Data')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Conjugaison $record) => view('filament.pages.actions.view-raw-json', ['data' => $record->raw_data])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConjugaisons::route('/'),
            'questions' => Pages\ManageConjugaisonQuestions::route('/{record}/questions'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['grade', 'period', 'semWeek'])
            ->whereIn('n', ['N1', 'N2', 'N3', 'N4', 'N5', 'N6'])
            ->whereIn('p', ['P1', 'P2', 'P3', 'P4', 'P5'])
            ->whereIn('sem', ['SEM1', 'SEM2', 'SEM3', 'SEM4', 'SEM5', 'SEM6']);
    }
}
