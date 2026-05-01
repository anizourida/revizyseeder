<?php

namespace App\Filament\Pages;

use App\Models\Raiida\ApiProvider;
use App\Services\Raiida\TypecastCurlCredentialParser;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TypecastAudioCredentialsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Audio Credentials';

    protected static ?string $navigationGroup = 'RevizySeeder';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Typecast Audio Credentials';

    protected static ?string $slug = 'audio-credentials';

    protected static string $view = 'filament.pages.typecast-audio-credentials-page';

    /**
     * @var array<string,mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->initialState());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Current Status')
                    ->schema([
                        Forms\Components\Placeholder::make('credentials_status')
                            ->label('Stored credentials')
                            ->content(function (): string {
                                $provider = $this->typecastProvider();
                                if (! $provider instanceof ApiProvider) {
                                    return 'No Typecast provider row yet. Save once to create it.';
                                }

                                $hasAuthorization = trim((string) ($provider->api_key ?? '')) !== '';
                                $hasCookie = trim((string) ($provider->auth_cookie ?? '')) !== '';
                                $status = ($hasAuthorization ? 'Authorization: yes' : 'Authorization: missing')
                                    . ' | '
                                    . ($hasCookie ? 'Cookie: yes' : 'Cookie: missing');

                                return "{$status} | Updated: " . optional($provider->updated_at)->format('Y-m-d H:i');
                            }),
                    ]),

                Forms\Components\Section::make('Update From cURL')
                    ->description('Paste the full cURL request copied from browser network tab (same method as tts-proxy).')
                    ->schema([
                        Forms\Components\Textarea::make('curl_command')
                            ->label('cURL Command')
                            ->rows(9)
                            ->placeholder("curl 'https://typecast.ai/api/speak/batch/post' ...")
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Optional Manual Overrides')
                    ->description('These fields override extracted values if provided.')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('authorization')
                            ->label('Authorization Header')
                            ->password()
                            ->revealable()
                            ->maxLength(5000),
                        Forms\Components\Textarea::make('auth_cookie')
                            ->label('Cookie Header')
                            ->rows(4)
                            ->maxLength(60000),
                    ]),

                Forms\Components\Section::make('Voice Settings')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('Provider Slug')
                            ->default('typecast')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('display_name')
                            ->label('Display Name')
                            ->default('Typecast TTS')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Provider Active')
                            ->default(true),
                        Forms\Components\TextInput::make('actor_id')
                            ->label('Actor ID')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lang')
                            ->label('Language')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('referer')
                            ->label('Referer URL')
                            ->required()
                            ->url()
                            ->maxLength(2048),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $parser = app(TypecastCurlCredentialParser::class);
        $parsed = $parser->parse((string) ($state['curl_command'] ?? ''));

        $authorization = trim((string) ($state['authorization'] ?? ''));
        if ($authorization === '') {
            $authorization = trim((string) ($parsed['authorization'] ?? ''));
        }

        $cookie = trim((string) ($state['auth_cookie'] ?? ''));
        if ($cookie === '') {
            $cookie = trim((string) ($parsed['cookie'] ?? ''));
        }

        $slug = trim((string) ($state['slug'] ?? 'typecast'));
        if ($slug === '') {
            $slug = 'typecast';
        }

        $provider = $this->typecastProviderBySlug($slug);
        $isNew = ! $provider instanceof ApiProvider;
        if (! $provider instanceof ApiProvider) {
            $provider = new ApiProvider();
            $provider->slug = $slug;
        }

        if ($isNew && $authorization === '' && $cookie === '') {
            Notification::make()
                ->title('Credentials missing')
                ->body('Paste a cURL command (or fill manual Authorization/Cookie) before saving.')
                ->danger()
                ->send();

            return;
        }

        $provider->provider_type = 'typecast';
        $provider->display_name = trim((string) ($state['display_name'] ?? 'Typecast TTS'));
        $provider->base_url = (string) config('raiida.audio_generator.typecast.base_url', 'https://typecast.ai');
        $provider->limit_unit = 'requests';
        $provider->is_active = (bool) ($state['is_active'] ?? true);
        $provider->model = trim((string) ($state['actor_id'] ?? config('raiida.audio_generator.typecast.actor_id', '')));

        if ($authorization !== '') {
            $provider->api_key = $authorization;
        }
        if ($cookie !== '') {
            $provider->auth_cookie = $cookie;
        }

        $metadata = is_array($provider->metadata) ? $provider->metadata : [];
        $metadata['actor_id'] = trim((string) ($state['actor_id'] ?? ''));
        $metadata['lang'] = trim((string) ($state['lang'] ?? 'fra'));
        $metadata['referer'] = trim((string) ($state['referer'] ?? config('raiida.audio_generator.typecast.referer', '')));
        $provider->metadata = $metadata;
        $provider->save();

        $this->form->fill($this->initialState($provider->slug));

        Notification::make()
            ->title('Audio credentials saved')
            ->body('Typecast credentials/settings were updated from cURL/manual input.')
            ->success()
            ->send();
    }

    /**
     * @return array<string,mixed>
     */
    private function initialState(string $slug = 'typecast'): array
    {
        $provider = $this->typecastProviderBySlug($slug);
        $metadata = is_array($provider?->metadata) ? $provider->metadata : [];

        return [
            'slug' => $provider?->slug ?? $slug,
            'display_name' => $provider?->display_name ?? 'Typecast TTS',
            'is_active' => $provider?->is_active ?? true,
            'actor_id' => (string) ($metadata['actor_id'] ?? $provider?->model ?? config('raiida.audio_generator.typecast.actor_id', '64f97820ffc5b7a301bf119e')),
            'lang' => (string) ($metadata['lang'] ?? config('raiida.audio_generator.typecast.lang', 'fra')),
            'referer' => (string) ($metadata['referer'] ?? config('raiida.audio_generator.typecast.referer', 'https://typecast.ai/text-to-speech/698a59a7ce61a36e23ee15ca')),
            'curl_command' => '',
            'authorization' => '',
            'auth_cookie' => '',
        ];
    }

    private function typecastProvider(): ?ApiProvider
    {
        return $this->typecastProviderBySlug('typecast');
    }

    private function typecastProviderBySlug(string $slug): ?ApiProvider
    {
        return ApiProvider::query()
            ->where('slug', strtolower(trim($slug)))
            ->first();
    }
}
