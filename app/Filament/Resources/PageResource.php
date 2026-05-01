<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Raiida\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Image Preview')
                    ->schema([
                        Forms\Components\Placeholder::make('preview')
                            ->hiddenLabel()
                            ->content(fn ($record) => $record ? new \Illuminate\Support\HtmlString('<img src="' . url('storage/' . $record->image_path) . '" style="max-height: 800px; width: auto; margin: 0 auto; display: block;" />') : null),
                    ])
                    ->collapsible(),
                Forms\Components\Select::make('grade_id')
                    ->relationship('grade', 'name')
                    ->required(),
                Forms\Components\TextInput::make('n_p_sem')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('page_number')
                    ->maxLength(255)
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('page_number_extraction_method', 'admin_manually');
                    }),
                Forms\Components\TextInput::make('page_number_extraction_method')
                    ->maxLength(255),
                Forms\Components\TextInput::make('md5_checksum')
                    ->maxLength(255),
                Forms\Components\Textarea::make('page_number_extraction_error')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image_path')
                    ->disk('public')
                    ->directory('manual_uploads')
                    ->image()
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->state(function (\App\Models\Raiida\Page $record) {
                        return url('storage/' . $record->image_path);
                    })
                    ->square(),
                Tables\Columns\TextColumn::make('grade.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('n_p_sem')
                    ->formatStateUsing(fn (string $state): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" style="' . match (true) {
                            str_contains($state, 'SEM1') => 'background-color: #3b82f6; color: white;',
                            str_contains($state, 'SEM2') => 'background-color: #06b6d4; color: white;',
                            str_contains($state, 'SEM3') => 'background-color: #10b981; color: white;',
                            str_contains($state, 'SEM4') => 'background-color: #84cc16; color: white;',
                            str_contains($state, 'SEM5') => 'background-color: #eab308; color: white;',
                            str_contains($state, 'SEM6') => 'background-color: #f97316; color: white;',
                            default => '',
                        } . '">' . $state . '</span>'
                    ))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('page_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('page_number_extraction_method')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('md5_checksum')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('image_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? \Illuminate\Support\Number::fileSize($state) : '')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('page_number_extraction_error')
                    ->badge()
                    ->color('danger')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_id')
                    ->label('Grade')
                    ->multiple()
                    ->options([
                        1 => 'Grade 1',
                        6 => 'Grade 2',
                        5 => 'Grade 3',
                        4 => 'Grade 4',
                        3 => 'Grade 5',
                        2 => 'Grade 6',
                    ]),
                Tables\Filters\SelectFilter::make('page_number_extraction_method')
                    ->options([
                        'auto' => 'Auto',
                        'manual' => 'Manual',
                    ]),
                Tables\Filters\TernaryFilter::make('page_number_status')
                    ->label('Page Number Extraction')
                    ->placeholder('All')
                    ->trueLabel('Extracted')
                    ->falseLabel('Not Extracted')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('page_number')->where('page_number', '!=', ''),
                        false: fn (Builder $query) => $query->where(fn ($query) => $query->whereNull('page_number')->orWhere('page_number', '')),
                    ),
                Tables\Filters\Filter::make('hide_duplicates')
                    ->label('Hide Duplicates')
                    ->toggle()
                    ->default()
                    ->query(fn (Builder $query) => $query->whereIn('id', function ($sub) {
                        $sub->selectRaw('MIN(id)')
                            ->from('pages')
                            ->whereNotNull('md5_checksum')
                            ->where('n_p_sem', 'not like', '%&%')
                            ->where('image_path', 'not like', '%&%')
                            ->groupBy('md5_checksum')
                            ->union(
                                \Illuminate\Support\Facades\DB::table('pages')
                                    ->select('id')
                                    ->whereNull('md5_checksum')
                                    ->where('n_p_sem', 'not like', '%&%')
                                    ->where('image_path', 'not like', '%&%')
                            );
                    })),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('n_p_sem', 'not like', '%&%')
            ->where('image_path', 'not like', '%&%');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
