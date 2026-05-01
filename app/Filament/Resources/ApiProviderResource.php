<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiProviderResource\Pages;
use App\Models\Raiida\ApiProvider;
use App\Models\Raiida\ApiProviderUsage;
use App\Services\Raiida\ApiProviderRegistryService;
use App\Services\Raiida\ApiProviderUsageService;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ApiProviderResource extends Resource
{
    protected static ?string $model = ApiProvider::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = 'API Providers';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'API Provider';

    protected static ?string $pluralModelLabel = 'API Providers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Provider')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required(fn (?ApiProvider $record): bool => $record === null)
                        ->alphaDash()
                        ->maxLength(120)
                        ->unique(ignoreRecord: true)
                        ->disabled(fn (?ApiProvider $record): bool => $record !== null)
                        ->dehydrated(fn (?ApiProvider $record): bool => $record === null)
                        ->helperText('Unique key used by the app (example: gemini, deepl, gemini-backup).'),
                    Forms\Components\Select::make('provider_type')
                        ->label('Provider Type')
                        ->required()
                        ->searchable()
                        ->options([
                            'gemini' => 'Gemini',
                            'deepl' => 'DeepL',
                            'typecast' => 'Typecast TTS',
                            'openai' => 'OpenAI',
                            'custom' => 'Custom',
                        ])
                        ->default('custom')
                        ->helperText('For Gemini failover, add multiple active rows with type = Gemini (slug `gemini` is tried first).'),
                    Forms\Components\TextInput::make('display_name')
                        ->label('Display Name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('base_url')
                        ->label('Base URL')
                        ->url()
                        ->maxLength(2048),
                    Forms\Components\TextInput::make('model')
                        ->label('Model')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('usage_endpoint')
                        ->label('Usage Endpoint')
                        ->url()
                        ->maxLength(2048),
                    Forms\Components\TextInput::make('api_key')
                        ->label('API Key / Authorization Header')
                        ->password()
                        ->revealable()
                        ->maxLength(2048)
                        ->afterStateHydrated(function (Forms\Components\TextInput $component): void {
                            $component->state('');
                        })
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText('Leave blank when editing to keep the current key.'),
                    Forms\Components\Textarea::make('auth_cookie')
                        ->label('Cookie Header')
                        ->rows(4)
                        ->maxLength(60000)
                        ->afterStateHydrated(function (Forms\Components\Textarea $component): void {
                            $component->state('');
                        })
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText('For Typecast/session providers. Leave blank when editing to keep the current cookie.'),
                ]),
            Forms\Components\Section::make('Limits & Status')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('limit_unit')
                        ->label('Limit Unit')
                        ->required()
                        ->options([
                            'requests' => 'Requests',
                            'tokens' => 'Tokens',
                            'characters' => 'Characters',
                        ])
                        ->default('requests'),
                    Forms\Components\TextInput::make('monthly_limit')
                        ->label('Monthly Limit')
                        ->numeric()
                        ->minValue(1)
                        ->helperText('Optional soft limit used to compute remaining usage.'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),
            Forms\Components\Section::make('Metadata')
                ->schema([
                    Forms\Components\KeyValue::make('metadata')
                        ->label('Metadata')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->addActionLabel('Add metadata')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(static function (Builder $query): Builder {
                return $query
                    ->with(['usages' => function ($usageQuery): void {
                        $usageQuery
                            ->where('period_key', now()->format('Y-m'))
                            ->latest('id');
                    }])
                    ->orderBy('slug');
            })
            ->poll('10s')
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('provider_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('limit_unit')
                    ->label('Unit')
                    ->badge(),
                Tables\Columns\TextColumn::make('monthly_limit')
                    ->label('Monthly Limit')
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state) : '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('used')
                    ->label('Used')
                    ->state(fn (ApiProvider $record): string => number_format(self::effectiveUsed($record)))
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('remaining')
                    ->label('Remaining')
                    ->state(function (ApiProvider $record): string {
                        $remaining = self::remaining($record);

                        return $remaining === null ? '-' : number_format($remaining);
                    })
                    ->badge()
                    ->color(function (ApiProvider $record): string {
                        $remaining = self::remaining($record);
                        if ($remaining === null) {
                            return 'gray';
                        }
                        if ($remaining <= 0) {
                            return 'danger';
                        }
                        if ($remaining < 1000 && strtolower((string) $record->limit_unit) !== 'requests') {
                            return 'warning';
                        }

                        return 'success';
                    }),
                Tables\Columns\TextColumn::make('period_key')
                    ->label('Usage Month')
                    ->state(fn (ApiProvider $record): string => (string) (self::usage($record)?->period_key ?? now()->format('Y-m')))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('requests_count')
                    ->label('Req')
                    ->state(fn (ApiProvider $record): int => (int) (self::usage($record)?->requests_count ?? 0))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_tokens_count')
                    ->label('Tokens')
                    ->state(fn (ApiProvider $record): int => (int) (self::usage($record)?->total_tokens_count ?? 0))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('characters_count')
                    ->label('Chars')
                    ->state(fn (ApiProvider $record): int => (int) (self::usage($record)?->characters_count ?? 0))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider_type')
                    ->label('Type')
                    ->options(fn (): array => ApiProvider::query()
                        ->select('provider_type')
                        ->whereNotNull('provider_type')
                        ->distinct()
                        ->orderBy('provider_type')
                        ->pluck('provider_type', 'provider_type')
                        ->all()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->attribute('is_active'),
            ])
            ->actions([
                Tables\Actions\Action::make('refresh_usage')
                    ->label('Refresh Usage')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(function (ApiProvider $record): void {
                        try {
                            app(ApiProviderUsageService::class)->refreshRemoteUsage($record);

                            Notification::make()
                                ->title('Usage refreshed')
                                ->body("Updated usage snapshot for {$record->slug}.")
                                ->success()
                                ->send();
                        } catch (RaiidaApiException $exception) {
                            Notification::make()
                                ->title('Usage refresh failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('Usage refresh failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (ApiProvider $record): bool => ! self::isBuiltInProvider($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => (bool) auth()->user()?->can('raiida-admin')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiProviders::route('/'),
            'create' => Pages\CreateApiProvider::route('/create'),
            'edit' => Pages\EditApiProvider::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        app(ApiProviderRegistryService::class)->all();

        return parent::getEloquentQuery();
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return ! $record instanceof ApiProvider || ! self::isBuiltInProvider($record);
    }

    private static function usage(ApiProvider $record): ?ApiProviderUsage
    {
        $loaded = $record->getRelationValue('usages');
        if ($loaded instanceof \Illuminate\Support\Collection) {
            $first = $loaded->first();

            return $first instanceof ApiProviderUsage ? $first : null;
        }

        $fallback = $record->currentUsage();

        return $fallback instanceof ApiProviderUsage ? $fallback : null;
    }

    private static function effectiveUsed(ApiProvider $record): int
    {
        $usage = self::usage($record);
        $trackedUsed = match (strtolower((string) ($record->limit_unit ?? 'requests'))) {
            'characters' => (int) ($usage?->characters_count ?? 0),
            'tokens' => (int) ($usage?->total_tokens_count ?? 0),
            default => (int) ($usage?->requests_count ?? 0),
        };

        $remoteUsed = $usage?->remote_used !== null ? (int) $usage->remote_used : null;
        if ($remoteUsed === null) {
            return max(0, $trackedUsed);
        }

        return max(0, max($trackedUsed, $remoteUsed));
    }

    private static function effectiveLimit(ApiProvider $record): ?int
    {
        $usage = self::usage($record);

        if ($usage?->remote_limit !== null) {
            return max(0, (int) $usage->remote_limit);
        }

        if ($record->monthly_limit !== null) {
            return max(0, (int) $record->monthly_limit);
        }

        return null;
    }

    private static function remaining(ApiProvider $record): ?int
    {
        $limit = self::effectiveLimit($record);
        if ($limit === null) {
            return null;
        }

        return max(0, $limit - self::effectiveUsed($record));
    }

    private static function isBuiltInProvider(ApiProvider $provider): bool
    {
        $configured = array_keys((array) config('raiida.api_providers.builtins', []));
        $configured = array_map(static fn (string $slug): string => strtolower(trim($slug)), $configured);

        return in_array(strtolower(trim((string) $provider->slug)), $configured, true);
    }
}
