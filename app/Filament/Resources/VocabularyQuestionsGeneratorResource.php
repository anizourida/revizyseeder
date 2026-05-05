<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VocabularyQuestionsGeneratorResource\Pages;
use App\Models\Raiida\QuestionPublishAttempt;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\QuestionJsonNormalizer;
use App\Services\Raiida\QuestionStudioService;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Throwable;

class VocabularyQuestionsGeneratorResource extends Resource
{
    protected static ?string $model = VocabularyItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Vocabulary Questions Generator';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Vocabulary Generator';

    protected static ?string $pluralModelLabel = 'Vocabulary Questions Generator';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(static function (Builder $query): Builder {
                return $query
                    ->addSelect([
                        'published_questions_count' => QuestionPublishAttempt::query()
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('question_publish_attempts.concept_id', 'vocabulary_items.concept_id')
                            ->where('question_publish_attempts.status', 'published'),
                    ])
                    ->orderByRaw("CASE grade WHEN 'N1' THEN 1 WHEN 'N2' THEN 2 WHEN 'N3' THEN 3 WHEN 'N4' THEN 4 WHEN 'N5' THEN 5 WHEN 'N6' THEN 6 ELSE 7 END ASC")
                    ->orderByRaw("CAST(SUBSTR(period, 2) AS INTEGER) ASC")
                    ->orderByRaw("CAST(SUBSTR(week, 4) AS INTEGER) ASC")
                    ->orderBy('word', 'asc');
            })
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('word')
                    ->label('Word')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
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
                Tables\Columns\TextColumn::make('concept_id')
                    ->label('Concept')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (?string $state): string => $state ? '#'.$state : 'None'),
                Tables\Columns\TextColumn::make('published_questions_count')
                    ->label('Created Qs')
                    ->badge()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state): string => ((int) $state) > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state): string => (string) ((int) $state)),
                Tables\Columns\IconColumn::make('has_image')
                    ->label('Image')
                    ->boolean()
                    ->getStateUsing(static fn (VocabularyItem $record): bool => trim((string) $record->revizy_image_file_id) !== ''),
                Tables\Columns\IconColumn::make('has_audio')
                    ->label('Audio')
                    ->boolean()
                    ->getStateUsing(static fn (VocabularyItem $record): bool => trim((string) $record->revizy_audio_file_id) !== ''),
                Tables\Columns\TextColumn::make('lexical_type')
                    ->label('Type')
                    ->badge()
                    ->color('success')
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
            ])
            ->filters([
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
                        ->where('lexical_type', '!=', '')
                        ->distinct()
                        ->orderBy('lexical_type')
                        ->pluck('lexical_type', 'lexical_type')
                        ->all()),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Gender')
                    ->options(fn (): array => VocabularyItem::query()
                        ->whereNotNull('gender')
                        ->where('gender', '!=', '')
                        ->distinct()
                        ->orderBy('gender')
                        ->pluck('gender', 'gender')
                        ->all()),
                Tables\Filters\SelectFilter::make('distractor_group')
                    ->label('Group')
                    ->options(fn (): array => VocabularyItem::query()
                        ->whereNotNull('distractor_group')
                        ->where('distractor_group', '!=', '')
                        ->distinct()
                        ->orderBy('distractor_group')
                        ->pluck('distractor_group', 'distractor_group')
                        ->all()),
                Tables\Filters\Filter::make('ready_only')
                    ->label('Ready For Generation')
                    ->default()
                    ->query(static function (Builder $query): Builder {
                        return $query
                            ->whereNotNull('concept_id')
                            ->where('concept_id', '!=', '')
                            ->whereNotNull('revizy_image_file_id')
                            ->where('revizy_image_file_id', '!=', '')
                            ->whereNotNull('lexical_type')
                            ->where('lexical_type', '!=', '');
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('generate_standard_questions')
                    ->label('Generate Standard')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->modalHeading('Standard Questions Preview')
                    ->modalDescription('Generates standard questions only (no fill_text and no letter_by_letter).')
                    ->modalSubmitActionLabel('Push Questions')
                    ->modalCancelActionLabel('Close')
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->action(static function (VocabularyItem $record): void {
                        $service = app(QuestionStudioService::class);
                        $normalizer = app(QuestionJsonNormalizer::class);
                        $questions = $service->generateStandardQuestionsForAsset((int) $record->id);

                        $published = 0;
                        $duplicatesExisting = 0;
                        $duplicatesBatch = 0;
                        $duplicates = 0;
                        $failed = 0;
                        $errors = [];

                        $duplicateCheckPayload = [];
                        foreach ($questions as $index => $question) {
                            $duplicateCheckPayload[] = [
                                'index' => (int) $index,
                                'concept_id' => (string) ($question['concept_id'] ?? ''),
                                'data' => is_array($question['data'] ?? null) ? $question['data'] : [],
                            ];
                        }

                        $duplicateCheckResult = $service->checkDuplicates($duplicateCheckPayload);
                        $existingDuplicateIndexes = [];
                        foreach (($duplicateCheckResult['duplicates'] ?? []) as $duplicateRow) {
                            $existingDuplicateIndexes[(int) ($duplicateRow['index'] ?? -1)] = true;
                        }

                        $batchSignatures = [];

                        foreach ($questions as $index => $question) {
                            if (isset($existingDuplicateIndexes[(int) $index])) {
                                $duplicatesExisting++;
                                $duplicates++;
                                continue;
                            }

                            $questionData = is_array($question['data'] ?? null) ? $question['data'] : [];
                            $normalized = $normalizer->normalize($questionData);
                            $signature = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            if ($signature !== false && isset($batchSignatures[$signature])) {
                                $duplicatesBatch++;
                                $duplicates++;
                                continue;
                            }

                            if ($signature !== false) {
                                $batchSignatures[$signature] = true;
                            }

                            try {
                                $result = $service->publishQuestion((int) $index, [
                                    'concept_id' => (string) ($question['concept_id'] ?? ''),
                                    'name' => (string) ($question['name'] ?? 'Question'),
                                    'type' => (string) ($question['type'] ?? 'universal'),
                                    'status' => 'published',
                                    'data' => $questionData,
                                ]);

                                if ((bool) ($result['is_duplicate'] ?? false)) {
                                    $duplicatesExisting++;
                                    $duplicates++;
                                } else {
                                    $published++;
                                }
                            } catch (Throwable $exception) {
                                $failed++;
                                if (count($errors) < 3) {
                                    $errors[] = $exception->getMessage();
                                }
                            }
                        }

                        $summary = 'Published: '.$published
                            .' | Duplicates: '.$duplicates
                            .' (existing: '.$duplicatesExisting.', batch: '.$duplicatesBatch.')'
                            .' | Failed: '.$failed;

                        if ($failed > 0 && $published === 0 && $duplicates === 0) {
                            Notification::make()
                                ->title('Push failed')
                                ->body($summary.(! empty($errors) ? ' | '.implode(' | ', $errors) : ''))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Questions pushed')
                            ->body($summary)
                            ->success()
                            ->send();
                    })
                    ->modalContent(static function (VocabularyItem $record) {
                        $questions = [];
                        $error = null;
                        $imagePreviewMap = [];
                        $audioPreviewMap = [];

                        try {
                            $questions = app(QuestionStudioService::class)
                                ->generateStandardQuestionsForAsset((int) $record->id);
                            [$imagePreviewMap, $audioPreviewMap] = static::buildMediaPreviewMaps($questions);
                        } catch (Throwable $exception) {
                            $error = $exception->getMessage();
                        }

                        return view('filament.pages.actions.vocabulary-standard-questions-preview', [
                            'record' => $record,
                            'questions' => $questions,
                            'error' => $error,
                            'imagePreviewMap' => $imagePreviewMap,
                            'audioPreviewMap' => $audioPreviewMap,
                        ]);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVocabularyQuestionsGenerators::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('grade', ['N1', 'N2', 'N3', 'N4', 'N5', 'N6']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @return array{0: array<string, string>, 1: array<string, string>}
     */
    private static function buildMediaPreviewMaps(array $questions): array
    {
        $imageIds = [];
        $audioIds = [];

        foreach ($questions as $question) {
            $data = is_array($question['data'] ?? null) ? $question['data'] : [];
            $media = is_array($data['media'] ?? null) ? $data['media'] : [];
            static::collectMediaIds($media, $imageIds, $audioIds);

            $answers = is_array($data['answers'] ?? null) ? $data['answers'] : [];
            foreach ($answers as $answer) {
                $answerMedia = is_array($answer['media'] ?? null) ? $answer['media'] : [];
                static::collectMediaIds($answerMedia, $imageIds, $audioIds);
            }
        }

        $imageMap = [];
        $audioMap = [];

        if ($imageIds !== []) {
            $imageRows = VocabularyItem::query()
                ->select(['revizy_image_file_id', 'image_path'])
                ->whereIn('revizy_image_file_id', array_values(array_keys($imageIds)))
                ->get();

            foreach ($imageRows as $row) {
                $secretId = trim((string) $row->revizy_image_file_id);
                $url = static::resolveImagePreviewUrl($row->image_path);
                if ($secretId !== '' && $url !== null && ! isset($imageMap[$secretId])) {
                    $imageMap[$secretId] = $url;
                }
            }
        }

        if ($audioIds !== []) {
            $audioRows = VocabularyItem::query()
                ->select(['revizy_audio_file_id', 'audio_path'])
                ->whereIn('revizy_audio_file_id', array_values(array_keys($audioIds)))
                ->get();

            foreach ($audioRows as $row) {
                $secretId = trim((string) $row->revizy_audio_file_id);
                $url = static::resolveAudioPreviewUrl($row->audio_path);
                if ($secretId !== '' && $url !== null && ! isset($audioMap[$secretId])) {
                    $audioMap[$secretId] = $url;
                }
            }
        }

        return [$imageMap, $audioMap];
    }

    /**
     * @param  array<string, mixed>  $media
     * @param  array<string, true>  $imageIds
     * @param  array<string, true>  $audioIds
     */
    private static function collectMediaIds(array $media, array &$imageIds, array &$audioIds): void
    {
        $imageId = trim((string) ($media['image'] ?? ''));
        $audioId = trim((string) ($media['audio'] ?? ''));

        if ($imageId !== '') {
            $imageIds[$imageId] = true;
        }

        if ($audioId !== '') {
            $audioIds[$audioId] = true;
        }
    }

    private static function resolveImagePreviewUrl(?string $path): ?string
    {
        $value = trim((string) $path);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    private static function resolveAudioPreviewUrl(?string $path): ?string
    {
        $value = trim((string) $path);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset('audios/' . ltrim($value, '/'));
    }

    public static function applySearchToTableQuery(Builder $query, string $search, array $searchableColumns): Builder
    {
        $query = parent::applySearchToTableQuery($query, $search, $searchableColumns);

        if ($search) {
            $query->orderByRaw("CASE WHEN word = ? THEN 0 ELSE 1 END", [$search]);
        }

        return $query;
    }
}
