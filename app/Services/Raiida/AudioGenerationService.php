<?php

namespace App\Services\Raiida;

use App\Models\Raiida\ApiProvider;
use App\Models\Raiida\Audio;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AudioGenerationService
{
    public function __construct(
        private readonly ApiProviderRegistryService $providers
    ) {
    }

    /**
     * Legacy endpoint used by old UI auto-loop.
     *
     * @return array<string,mixed>
     */
    public function generateNext(): array
    {
        try {
            $summary = $this->generateBatch([
                'limit' => 1,
                'force' => false,
            ]);
        } catch (RaiidaApiException $exception) {
            return [
                'status' => 'error',
                'message' => $exception->getMessage(),
                'wait' => (int) config('raiida.audio_generator.wait_seconds_between_items', 10),
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'error',
                'message' => $exception->getMessage(),
                'wait' => (int) config('raiida.audio_generator.wait_seconds_between_items', 10),
            ];
        }

        $targeted = (int) ($summary['targeted'] ?? 0);
        if ($targeted <= 0) {
            return [
                'status' => 'complete',
                'message' => 'All vocabulary items already have audio.',
            ];
        }

        if ((int) ($summary['generated_total'] ?? 0) > 0) {
            return [
                'status' => 'success',
                'message' => 'Audio generated',
                'item' => (string) ($summary['last_generated_item'] ?? ''),
                'file' => (string) ($summary['last_generated_file'] ?? ''),
                'wait' => (int) config('raiida.audio_generator.wait_seconds_between_items', 10),
            ];
        }

        if ((int) ($summary['temporary_failures'] ?? 0) > 0) {
            return [
                'status' => 'retry',
                'message' => (string) (($summary['errors'][0] ?? 'Temporary generation failure')),
                'item' => (string) ($summary['last_attempted_item'] ?? ''),
                'wait' => max(5, (int) config('raiida.audio_generator.wait_seconds_between_items', 10)),
            ];
        }

        return [
            'status' => 'error',
            'message' => (string) (($summary['errors'][0] ?? 'Audio generation failed')),
            'item' => (string) ($summary['last_attempted_item'] ?? ''),
        ];
    }

    /**
     * @param  array{limit?:int,grade?:string,period?:string,week?:string,force?:bool,item_id?:int,verbose?:bool}  $options
     * @return array<string,mixed>
     */
    public function generateBatch(array $options = []): array
    {
        if (! (bool) config('raiida.audio_generator.enabled', false)) {
            throw new RaiidaApiException(
                'Audio generator is disabled. Enable RAIIDA_AUDIO_GENERATOR_ENABLED first.',
                422
            );
        }

        $limit = max(1, min((int) ($options['limit'] ?? 50), 500));
        $force = (bool) ($options['force'] ?? false);
        $verbose = (bool) ($options['verbose'] ?? false);

        $provider = $this->resolveTypecastProvider();

        $query = VocabularyItem::query()
            ->select([
                'id',
                'word',
                'audio_path',
                'grade',
                'period',
                'week',
            ])
            ->orderBy('id');

        if (! $force && empty($options['item_id'])) {
            $query->where(function (Builder $missing): void {
                $missing->whereNull('audio_path')->orWhere('audio_path', '');
            });
        }

        $this->applyScopeFilters($query, $options);

        $targets = $query->limit($limit)->get();

        $summary = [
            'force' => $force,
            'verbose' => $verbose,
            'provider' => [
                'id' => (int) $provider->id,
                'slug' => (string) ($provider->slug ?? ''),
                'type' => (string) ($provider->provider_type ?? ''),
            ],
            'targeted' => $targets->count(),
            'generated_total' => 0,
            'skipped_existing' => 0,
            'failed_total' => 0,
            'temporary_failures' => 0,
            'errors' => [],
            'error_items' => [],
            'last_attempted_item' => null,
            'last_attempted_item_id' => null,
            'last_generated_item' => null,
            'last_generated_item_id' => null,
            'last_generated_file' => null,
        ];

        foreach ($targets as $item) {
            $summary['last_attempted_item'] = (string) $item->word;
            $summary['last_attempted_item_id'] = (int) $item->id;

            $audit = [
                'vocabulary_item_id' => (int) $item->id,
                'word' => (string) $item->word,
                'audio_path' => (string) ($item->audio_path ?? ''),
                'provider_id' => (int) $provider->id,
                'provider_slug' => (string) ($provider->slug ?? ''),
                'provider_type' => (string) ($provider->provider_type ?? ''),
                'force' => $force,
            ];

            if ($verbose) {
                Log::info('raiida.audio_generator.item.started', $audit);
            }

            if (! $force && trim((string) ($item->audio_path ?? '')) !== '') {
                $summary['skipped_existing']++;
                if ($verbose) {
                    Log::info('raiida.audio_generator.item.skipped_existing', $audit);
                }

                continue;
            }

            try {
                $result = $this->generateForItem($provider, $item);
                $summary['generated_total']++;
                $summary['last_generated_item'] = (string) $item->word;
                $summary['last_generated_item_id'] = (int) $item->id;
                $summary['last_generated_file'] = (string) ($result['file'] ?? null);
                if ($verbose) {
                    Log::info('raiida.audio_generator.item.completed', $audit + [
                        'file' => (string) ($result['file'] ?? ''),
                    ]);
                }
            } catch (RaiidaApiException $exception) {
                $summary['failed_total']++;
                if ($this->isRetriableStatus($exception->statusCode())) {
                    $summary['temporary_failures']++;
                }
                $message = $exception->getMessage();
                if (count($summary['errors']) < 50) {
                    $summary['errors'][] = $message;
                }
                if (count($summary['error_items']) < 50) {
                    $summary['error_items'][] = [
                        'id' => (int) $item->id,
                        'word' => (string) $item->word,
                        'status' => (int) $exception->statusCode(),
                        'message' => $message,
                    ];
                }
                Log::warning('raiida.audio_generator.item.failed', $audit + [
                    'exception' => $exception::class,
                    'status' => (int) $exception->statusCode(),
                    'error' => $message,
                ]);
            } catch (Throwable $exception) {
                $summary['failed_total']++;
                $summary['temporary_failures']++;
                $message = $exception->getMessage();
                if (count($summary['errors']) < 50) {
                    $summary['errors'][] = $message;
                }
                if (count($summary['error_items']) < 50) {
                    $summary['error_items'][] = [
                        'id' => (int) $item->id,
                        'word' => (string) $item->word,
                        'status' => 500,
                        'message' => $message,
                    ];
                }
                Log::error('raiida.audio_generator.item.failed', $audit + [
                    'exception' => $exception::class,
                    'error' => $message,
                ]);
            }
        }

        $remaining = VocabularyItem::query();
        $this->applyScopeFilters($remaining, $options);
        $remaining->where(function (Builder $missing): void {
            $missing->whereNull('audio_path')->orWhere('audio_path', '');
        });
        $summary['remaining_missing_in_scope'] = (int) $remaining->count();

        return $summary;
    }

    /**
     * @return array{file:string,speak_url:string,audio_url:string,signed_url:string}
     */
    private function generateForItem(ApiProvider $provider, VocabularyItem $item): array
    {
        $word = trim((string) $item->word);
        if ($word === '') {
            throw new RaiidaApiException("Vocabulary item {$item->id} has empty word", 422);
        }

        $audioPayload = $this->requestTypecastAudio($provider, $word);
        $filename = $this->storeAudioBinary($item, (string) $audioPayload['binary']);

        Audio::query()->updateOrCreate(
            ['vocabulary_item_id' => (int) $item->id],
            [
                'text' => $word,
                'file_path' => $filename,
            ]
        );

        if ((string) ($item->audio_path ?? '') !== $filename) {
            $item->audio_path = $filename;
            $item->save();
        }

        return [
            'file' => $filename,
            'speak_url' => (string) ($audioPayload['speak_url'] ?? ''),
            'audio_url' => (string) ($audioPayload['audio_url'] ?? ''),
            'signed_url' => (string) ($audioPayload['signed_url'] ?? ''),
        ];
    }

    /**
     * @return array{binary:string,speak_url:string,audio_url:string,signed_url:string}
     */
    private function requestTypecastAudio(ApiProvider $provider, string $text): array
    {
        $headers = $this->typecastHeaders($provider);
        $timeout = max(20, (int) config('raiida.audio_generator.typecast.request_timeout_seconds', 90));

        $actorId = trim((string) data_get($provider->metadata, 'actor_id', ''));
        if ($actorId === '') {
            $actorId = trim((string) $provider->model);
        }
        if ($actorId === '') {
            $actorId = trim((string) config('raiida.audio_generator.typecast.actor_id', '64f97820ffc5b7a301bf119e'));
        }

        $lang = trim((string) data_get($provider->metadata, 'lang', ''));
        if ($lang === '') {
            $lang = trim((string) config('raiida.audio_generator.typecast.lang', 'fra'));
        }

        $postEndpoint = (string) config('raiida.audio_generator.typecast.post_endpoint', 'https://typecast.ai/api/speak/batch/post');
        $statusEndpoint = (string) config('raiida.audio_generator.typecast.get_endpoint', 'https://typecast.ai/api/speak/batch/get');

        $postData = [[
            'text' => $text,
            'actor_id' => $actorId,
            'expressivity' => 0,
            'tempo' => 1,
            'pitch' => 0,
            'style_label' => 'normal-1',
            'style_label_version' => 'v2',
            'emotion_scale' => 1,
            'lang' => $lang,
            'mode' => 'one-vocoder',
            'retake' => true,
            'bp_c_l' => true,
            'adjust_lastword' => 0,
        ]];

        $responseStep1 = Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders($headers)
            ->post($postEndpoint, $postData);

        if (! $responseStep1->successful()) {
            throw new RaiidaApiException(
                'Typecast step1 failed: HTTP '
                    . $responseStep1->status()
                    . ' - '
                    . $this->responseSnippet($responseStep1->body()),
                $responseStep1->status()
            );
        }

        $speakUrl = (string) data_get($responseStep1->json(), 'result.speak_urls.0', '');
        if ($speakUrl === '') {
            throw new RaiidaApiException('Typecast step1 failed: missing speak URL', 502);
        }

        $audioUrl = '';
        $pollAttempts = max(1, (int) config('raiida.audio_generator.typecast.poll_attempts', 60));
        $pollIntervalMs = max(100, (int) config('raiida.audio_generator.typecast.poll_interval_ms', 1000));

        for ($attempt = 1; $attempt <= $pollAttempts; $attempt++) {
            usleep($pollIntervalMs * 1000);

            $responseStep2 = Http::timeout($timeout)
                ->acceptJson()
                ->withHeaders($headers)
                ->post($statusEndpoint, [$speakUrl]);

            if (! $responseStep2->successful()) {
                if ($attempt === $pollAttempts) {
                    throw new RaiidaApiException(
                        'Typecast step2 failed: HTTP '
                            . $responseStep2->status()
                            . ' - '
                            . $this->responseSnippet($responseStep2->body()),
                        $responseStep2->status()
                    );
                }

                continue;
            }

            $result = data_get($responseStep2->json(), 'result.0', []);
            $status = (string) data_get($result, 'status', '');
            $candidateAudioUrl = (string) data_get($result, 'audio.url', '');

            if ($status === 'done' && $candidateAudioUrl !== '') {
                $audioUrl = $candidateAudioUrl;
                break;
            }

            if ($status === 'error') {
                $detail = (string) data_get($result, 'error_message', 'Unknown generation error');
                throw new RaiidaApiException('Typecast generation failed: ' . $detail, 502);
            }
        }

        if ($audioUrl === '') {
            throw new RaiidaApiException('Typecast generation timeout waiting for audio URL', 504);
        }

        $downloadHeaders = $headers;
        unset($downloadHeaders['content-type']);

        $cloudfrontUrl = rtrim($audioUrl, '/') . '/cloudfront';
        $responseStep3 = Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders($downloadHeaders)
            ->get($cloudfrontUrl);

        if (! $responseStep3->successful()) {
            throw new RaiidaApiException(
                'Typecast step3 failed: HTTP '
                    . $responseStep3->status()
                    . ' - '
                    . $this->responseSnippet($responseStep3->body()),
                $responseStep3->status()
            );
        }

        $signedUrl = (string) data_get($responseStep3->json(), 'result', '');
        if ($signedUrl === '') {
            throw new RaiidaApiException('Typecast step3 failed: missing signed audio URL', 502);
        }

        $responseStep4 = Http::timeout($timeout * 2)
            ->withHeaders([
                'user-agent' => (string) config('raiida.audio_generator.typecast.user_agent', ''),
            ])
            ->get($signedUrl);

        if (! $responseStep4->successful()) {
            throw new RaiidaApiException(
                'Typecast step4 failed: HTTP '
                    . $responseStep4->status()
                    . ' - '
                    . $this->responseSnippet($responseStep4->body()),
                $responseStep4->status()
            );
        }

        $binary = (string) $responseStep4->body();
        if ($binary === '') {
            throw new RaiidaApiException('Typecast step4 failed: downloaded audio is empty', 502);
        }

        return [
            'binary' => $binary,
            'speak_url' => $speakUrl,
            'audio_url' => $audioUrl,
            'signed_url' => $signedUrl,
        ];
    }

    /**
     * @return array<string,string>
     */
    private function typecastHeaders(ApiProvider $provider): array
    {
        $authorization = trim((string) ($provider->api_key ?? ''));
        $cookie = trim((string) ($provider->auth_cookie ?? ''));

        if ($authorization === '') {
            throw new RaiidaApiException(
                'Typecast Authorization header is missing. Update it in Admin > Audio Credentials.',
                422
            );
        }

        if ($cookie === '') {
            throw new RaiidaApiException(
                'Typecast Cookie header is missing. Update it in Admin > Audio Credentials.',
                422
            );
        }

        $origin = (string) config('raiida.audio_generator.typecast.origin', 'https://typecast.ai');
        $referer = trim((string) data_get($provider->metadata, 'referer', ''));
        if ($referer === '') {
            $referer = (string) config(
                'raiida.audio_generator.typecast.referer',
                'https://typecast.ai/text-to-speech/698a59a7ce61a36e23ee15ca'
            );
        }

        return [
            'accept' => 'application/json, text/plain, */*',
            'accept-language' => 'en-US,en;q=0.9',
            'authorization' => $authorization,
            'content-type' => 'application/json',
            'cookie' => $cookie,
            'origin' => $origin,
            'referer' => $referer,
            'priority' => 'u=1, i',
            'sec-ch-ua' => '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => (string) config(
                'raiida.audio_generator.typecast.user_agent',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36'
            ),
        ];
    }

    private function resolveTypecastProvider(): ApiProvider
    {
        $this->providers->all();

        $preferredSlug = strtolower(trim((string) config('raiida.audio_generator.provider_slug', 'typecast')));

        $provider = ApiProvider::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereRaw('LOWER(provider_type) = ?', ['typecast'])
                    ->orWhereRaw('LOWER(slug) = ?', ['typecast']);
            })
            ->orderByRaw("CASE WHEN LOWER(slug) = ? THEN 0 WHEN LOWER(slug) = 'typecast' THEN 1 ELSE 2 END", [$preferredSlug])
            ->orderBy('id')
            ->first();

        if (! $provider instanceof ApiProvider) {
            throw new RaiidaApiException(
                'No active Typecast provider found. Configure Admin > Audio Credentials first.',
                422
            );
        }

        return $provider;
    }

    private function storeAudioBinary(VocabularyItem $item, string $binary): string
    {
        $audioRoot = public_path('audios');
        if (! is_dir($audioRoot)) {
            @mkdir($audioRoot, 0775, true);
        }

        if (! is_dir($audioRoot) || ! is_writable($audioRoot)) {
            throw new RaiidaApiException("Audio output directory is not writable: {$audioRoot}", 500);
        }

        $base = $this->sanitizeFilename((string) $item->word);
        if ($base === '') {
            $base = 'audio_' . (int) $item->id;
        }

        $filename = mb_substr($base, 0, 50, 'UTF-8') . '.wav';
        $fullPath = $audioRoot . DIRECTORY_SEPARATOR . $filename;

        $written = @file_put_contents($fullPath, $binary);
        if ($written === false) {
            throw new RaiidaApiException("Failed to write audio file: {$fullPath}", 500);
        }

        return $filename;
    }

    private function sanitizeFilename(string $text): string
    {
        $ascii = Str::ascii($text);
        $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '_', $ascii));
        $slug = trim($slug, '_');

        return $slug;
    }

    private function responseSnippet(string $body): string
    {
        $flat = preg_replace('/\s+/u', ' ', trim($body)) ?? trim($body);
        if ($flat === '') {
            return 'empty response body';
        }

        return mb_substr($flat, 0, 260, 'UTF-8');
    }

    /**
     * @param  array<string,mixed>  $options
     */
    private function applyScopeFilters(Builder $query, array $options): void
    {
        if (! empty($options['item_id'])) {
            $query->where('id', (int) $options['item_id']);
        }
        if (! empty($options['grade'])) {
            $query->where('grade', (string) $options['grade']);
        }
        if (! empty($options['period'])) {
            $query->where('period', (string) $options['period']);
        }
        if (! empty($options['week'])) {
            $query->where('week', (string) $options['week']);
        }
    }

    private function isRetriableStatus(int $status): bool
    {
        return in_array($status, [408, 409, 425, 429], true) || $status >= 500;
    }
}
