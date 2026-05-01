<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LivretResource\Pages;
use App\Jobs\RevizySeederLMStudioOCRJob;
use App\Models\Raiida\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class LivretResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationLabel = 'Livrets';

    protected static ?string $pluralLabel = 'Livrets';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Preview')
                    ->schema([
                        Forms\Components\Placeholder::make('preview')
                            ->hiddenLabel()
                            ->content(function (?Page $record): ?HtmlString {
                                if (! $record instanceof Page || (int) $record->id <= 0) {
                                    return null;
                                }

                                return new HtmlString(
                                    '<img src="' . url('storage/' . ltrim((string) $record->image_path, '/')) . '" style="max-height: 760px; width: auto; margin: 0 auto; display: block; border-radius: 10px; border: 1px solid #e5e7eb;" />'
                                );
                            }),
                    ]),

                Forms\Components\Section::make('Page Metadata')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('page_number')
                                    ->label('Page Number')
                                    ->numeric()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set): void {
                                        $set('page_number_extraction_method', 'admin_manually');
                                    }),
                                Forms\Components\TextInput::make('n_p_sem')
                                    ->label('Lesson Scope')
                                    ->readOnly()
                                    ->dehydrated(false),
                                Forms\Components\Placeholder::make('grade_label')
                                    ->label('Grade')
                                    ->content(fn (?Page $record): string => $record?->grade?->name ?? '-'),
                                Forms\Components\TextInput::make('page_number_extraction_method')
                                    ->label('Extraction Method')
                                    ->readOnly()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('image_size')
                                    ->label('Image Size (bytes)')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => $state ? (string) $state : '-'),
                                Forms\Components\TextInput::make('md5_checksum')
                                    ->label('Checksum')
                                    ->readOnly()
                                    ->dehydrated(false),
                            ]),

                        Forms\Components\Textarea::make('page_number_extraction_error')
                            ->label('Extraction Error')
                            ->rows(2)
                            ->readOnly()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('OCR Text Outputs')
                    ->schema([
                        Forms\Components\Placeholder::make('ocr_status')
                            ->label('Status')
                            ->content(function (?Page $record): string {
                                if (! $record instanceof Page) {
                                    return '-';
                                }

                                $olm = filled($record->ocr_olmocr_path) ? 'Available' : 'Missing';
                                $chandra = filled($record->ocr_chandra_path) ? 'Available' : 'Missing';

                                return "olmOCR: {$olm} | Chandra: {$chandra}";
                            }),
                        Forms\Components\TextInput::make('ocr_olmocr_path')
                            ->label('olmOCR Path')
                            ->readOnly()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('ocr_chandra_path')
                            ->label('Chandra Path')
                            ->readOnly()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_number')
                    ->label('Page')
                    ->weight('bold')
                    ->size('lg')
                    ->extraAttributes(['style' => 'font-size: 24px; color: #dc2626; text-align: center;'])
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Page Preview')
                    ->state(fn (Page $record): string => url('storage/' . ltrim((string) $record->image_path, '/')))
                    ->width(210)
                    ->height(320)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; background: #f8fafc; border-radius: 10px; border: 1px solid #e5e7eb;',
                    ]),

                Tables\Columns\TextColumn::make('n_p_sem')
                    ->label('Scope')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('grade.name')
                    ->label('Grade')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('page_number_extraction_method')
                    ->label('Method')
                    ->badge()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_olmocr')
                    ->label('olmOCR')
                    ->boolean()
                    ->getStateUsing(fn (Page $record): bool => filled($record->ocr_olmocr_path)),

                Tables\Columns\IconColumn::make('has_chandra')
                    ->label('Chandra')
                    ->boolean()
                    ->getStateUsing(fn (Page $record): bool => filled($record->ocr_chandra_path)),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->toggleable(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_id')
                    ->relationship('grade', 'name')
                    ->label('Livret Grade')
                    ->default(1)
                    ->selectablePlaceholder(false),

                Tables\Filters\TernaryFilter::make('has_ocr_text')
                    ->label('Has OCR Text')
                    ->placeholder('All')
                    ->trueLabel('With OCR')
                    ->falseLabel('Without OCR')
                    ->queries(
                        true: fn (Builder $query) => $query->where(function (Builder $q): void {
                            $q->whereNotNull('ocr_olmocr_path')->orWhereNotNull('ocr_chandra_path');
                        }),
                        false: fn (Builder $query) => $query->whereNull('ocr_olmocr_path')->whereNull('ocr_chandra_path'),
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('run_olmocr')
                        ->label('olmOCR')
                        ->icon('heroicon-m-sparkles')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalDescription('Runs olmOCR text extraction only for this page.')
                        ->action(function (Page $record): void {
                            RevizySeederLMStudioOCRJob::dispatch($record->id, 'allenai/olmocr-2-7b', 'text_only');

                            Notification::make()
                                ->title('olmOCR extraction queued')
                                ->body('Page #' . (string) $record->page_number . ' queued for text extraction.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('run_chandra')
                        ->label('Chandra')
                        ->icon('heroicon-m-bolt')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalDescription('Runs Chandra text extraction only for this page.')
                        ->action(function (Page $record): void {
                            RevizySeederLMStudioOCRJob::dispatch($record->id, 'chandra-ocr', 'text_only');

                            Notification::make()
                                ->title('Chandra extraction queued')
                                ->body('Page #' . (string) $record->page_number . ' queued for text extraction.')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Extract Text')
                    ->icon('heroicon-m-chevron-down')
                    ->button()
                    ->color('gray'),
            ], position: ActionsPosition::AfterColumns)
            ->actionsColumnLabel('Actions');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLivrets::route('/'),
            'create' => Pages\CreateLivret::route('/create'),
            'edit' => Pages\EditLivret::route('/{record}/edit'),
        ];
    }
}
