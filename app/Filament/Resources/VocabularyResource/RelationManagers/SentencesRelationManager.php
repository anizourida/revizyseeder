<?php

namespace App\Filament\Resources\VocabularyResource\RelationManagers;

use App\Models\Raiida\VocabularySentence;
use App\Services\Raiida\DeepLTranslationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Throwable;

class SentencesRelationManager extends RelationManager
{
    protected static string $relationship = 'sentences';

    protected static ?string $recordTitleAttribute = 'sentence';

    protected static ?string $title = 'Phrases Exemples (Sentences)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('sentence')
                    ->label('Phrase en Français')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull()
                    ->placeholder('Exemple: Le garçon donne un cadeau à son ami.')
                    ->helperText('Phrase authentique du programme contenant le mot de vocabulaire.'),

                Forms\Components\Textarea::make('sentence_ar')
                    ->label('Traduction en Arabe')
                    ->rows(2)
                    ->columnSpanFull()
                    ->extraInputAttributes(['dir' => 'rtl'])
                    ->placeholder('مثال: الولد يعطي هدية لصديقه.'),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('source_session')
                            ->label('Séance / Session (ex: S3, S5)')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('source_slide')
                            ->label('Diapositive / Slide #')
                            ->numeric(),

                        Forms\Components\Select::make('source_type')
                            ->label('Type de Source')
                            ->options([
                                'slide' => 'Présentation Slide',
                                'ocr' => 'Page Livret (OCR)',
                                'manual' => 'Saisie Manuelle',
                            ])
                            ->default('slide')
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('audio_path')
                    ->label('Chemin Audio (TTS)')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sentence')
            ->defaultSort('source_slide', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('sentence')
                    ->label('Phrase (French)')
                    ->searchable()
                    ->wrap()
                    ->copyable()
                    ->copyMessage('Phrase copiée !')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('sentence_ar')
                    ->label('Traduction (Arabe)')
                    ->searchable()
                    ->wrap()
                    ->extraAttributes(['dir' => 'rtl'])
                    ->placeholder('— Non traduit —')
                    ->color(fn ($state) => empty($state) ? 'gray' : 'success'),

                Tables\Columns\TextColumn::make('source_info')
                    ->label('Source')
                    ->getStateUsing(function (VocabularySentence $record): string {
                        if ($record->source_session || $record->source_slide) {
                            return ($record->source_session ?: '') . ($record->source_slide ? ' (Slide ' . $record->source_slide . ')' : '');
                        }
                        return $record->source_type ?: '—';
                    })
                    ->badge()
                    ->color(fn (VocabularySentence $record) => $record->preview_url ? 'info' : 'gray')
                    ->icon(fn (VocabularySentence $record) => $record->preview_url ? 'heroicon-m-arrow-top-right-on-square' : null)
                    ->iconPosition('after')
                    ->url(fn (VocabularySentence $record) => $record->preview_url)
                    ->openUrlInNewTab()
                    ->tooltip(fn (VocabularySentence $record) => $record->preview_url ? 'Ouvrir la diapositive PPT' : null),

                Tables\Columns\IconColumn::make('has_audio')
                    ->label('Audio')
                    ->boolean()
                    ->getStateUsing(fn (VocabularySentence $record): bool => ! empty($record->audio_path) || ! empty($record->revizy_audio_file_id))
                    ->trueIcon('heroicon-o-speaker-wave')
                    ->falseIcon('heroicon-o-speaker-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source_type')
                    ->options([
                        'slide' => 'Slide Presentation',
                        'ocr' => 'OCR Book',
                        'manual' => 'Manual Input',
                    ]),
                Tables\Filters\TernaryFilter::make('has_translation')
                    ->label('Traduction Arabe')
                    ->placeholder('Toutes')
                    ->trueLabel('Traduite')
                    ->falseLabel('Non traduite')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('sentence_ar')->where('sentence_ar', '!=', ''),
                        false: fn ($query) => $query->where(fn ($q) => $q->whereNull('sentence_ar')->orWhere('sentence_ar', '')),
                    ),
                Tables\Filters\TernaryFilter::make('has_audio')
                    ->label('Audio TTS')
                    ->placeholder('Tous')
                    ->trueLabel('Avec Audio')
                    ->falseLabel('Sans Audio')
                    ->queries(
                        true: fn ($query) => $query->where(fn ($q) => $q->whereNotNull('audio_path')->orWhereNotNull('revizy_audio_file_id')),
                        false: fn ($query) => $query->whereNull('audio_path')->whereNull('revizy_audio_file_id'),
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter une Phrase')
                    ->mutateFormDataUsing(function (array $data): array {
                        $vocab = $this->getOwnerRecord();
                        $data['vocabulary_item_id'] = $vocab->id;
                        $data['word'] = $vocab->word;
                        $data['base_word'] = $vocab->base_word;
                        $data['grade'] = $vocab->grade;
                        $data['subject'] = $vocab->subject ?: 'FR';
                        $data['period'] = $vocab->period;
                        $data['week'] = $vocab->week;
                        $data['lesson_id'] = $vocab->lesson_id;
                        $data['image_path'] = $vocab->image_path;
                        return $data;
                    }),

                Tables\Actions\Action::make('translate_all_missing')
                    ->label('Traduire en Arabe (DeepL)')
                    ->icon('heroicon-o-language')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Traduire les phrases manquantes en arabe')
                    ->modalDescription('Traduire automatiquement toutes les phrases de ce mot qui n\'ont pas encore de traduction arabe avec DeepL ?')
                    ->action(function (DeepLTranslationService $translator) {
                        try {
                            $vocab = $this->getOwnerRecord();
                            $sentences = $vocab->sentences()
                                ->where(fn ($q) => $q->whereNull('sentence_ar')->orWhere('sentence_ar', ''))
                                ->whereNotNull('sentence')
                                ->where('sentence', '!=', '')
                                ->get();

                            if ($sentences->isEmpty()) {
                                Notification::make()
                                    ->title('Aucune phrase à traduire')
                                    ->body('Toutes les phrases ont déjà une traduction.')
                                    ->info()
                                    ->send();
                                return;
                            }

                            $texts = $sentences->pluck('sentence')->all();
                            $translations = $translator->translateBatch($texts);

                            $translatedCount = 0;
                            foreach ($sentences as $index => $sentenceModel) {
                                $arabic = $translations[$index] ?? null;
                                if ($arabic) {
                                    $sentenceModel->update(['sentence_ar' => trim($arabic)]);
                                    $translatedCount++;
                                }
                            }

                            Notification::make()
                                ->title('Traduction terminée')
                                ->body("{$translatedCount} phrase(s) traduite(s) avec succès !")
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Erreur de traduction')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('preview_ppt')
                    ->label('Aperçu PPT')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn (VocabularySentence $record) => $record->preview_url)
                    ->openUrlInNewTab()
                    ->visible(fn (VocabularySentence $record) => $record->preview_url !== null),

                Tables\Actions\Action::make('translate_single')
                    ->label('Traduire')
                    ->icon('heroicon-o-language')
                    ->color('gray')
                    ->action(function (VocabularySentence $record, DeepLTranslationService $translator) {
                        try {
                            if (empty($record->sentence)) {
                                return;
                            }
                            $translations = $translator->translateBatch([$record->sentence]);
                            $ar = $translations[0] ?? null;
                            if ($ar) {
                                $record->update(['sentence_ar' => trim($ar)]);
                                Notification::make()
                                    ->title('Traduction réussie')
                                    ->body($ar)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Impossible de traduire')
                                    ->body('Le service DeepL n\'a pas retourné de résultat.')
                                    ->warning()
                                    ->send();
                            }
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Erreur DeepL')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
