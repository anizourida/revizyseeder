<?php

namespace App\Services\Raiida;

use App\Models\Raiida\ApiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepLTranslationService
{
    public function __construct(
        private readonly ApiProviderRegistryService $providers,
        private readonly ApiProviderUsageService $usage
    ) {
    }

    /**
     * Get usage statistics from DeepL API.
     *
     * @return array{character_count: int, character_limit: int}|null
     */
    public function getUsage(): ?array
    {
        $provider = $this->providers->findBySlug('deepl');
        if (! $provider instanceof ApiProvider || ! $provider->is_active) {
            return null;
        }

        try {
            $summary = $this->usage->refreshRemoteUsage($provider);
            $usage = (array) ($summary['usage'] ?? []);

            return [
                'character_count' => (int) ($usage['remote_used'] ?? $usage['characters_count'] ?? 0),
                'character_limit' => (int) ($usage['limit'] ?? $usage['remote_limit'] ?? 0),
            ];
        } catch (\Throwable $e) {
            Log::error('DeepL getUsage exception', ['message' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Translate an array of words from French to Arabic.
     *
     * @param string[] $texts
     * @return string[] Translated words
     */
    public function translateBatch(array $texts): array
    {
        $provider = $this->providers->findBySlug('deepl');
        if (! $provider instanceof ApiProvider || ! $provider->is_active || empty($texts)) {
            return array_fill(0, count($texts), null);
        }

        $apiKey = trim((string) ($provider->api_key ?? config('raiida.deepl.api_key')));
        if ($apiKey === '') {
            return array_fill(0, count($texts), null);
        }

        $baseUrl = trim((string) ($provider->base_url ?? ''));
        if ($baseUrl === '') {
            $baseUrl = str_ends_with($apiKey, ':fx')
                ? 'https://api-free.deepl.com'
                : 'https://api.deepl.com';
        }

        $endpoint = rtrim($baseUrl, '/') . '/v2/translate';
        $characters = 0;
        foreach ($texts as $text) {
            $characters += mb_strlen((string) $text, 'UTF-8');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "DeepL-Auth-Key {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'text' => $texts,
                'source_lang' => 'FR',
                'target_lang' => 'AR',
            ]);

            if ($response->successful()) {
                $translations = $response->json('translations');
                if (is_array($translations)) {
                    $this->usage->recordUsage(
                        $provider,
                        [
                            'requests' => 1,
                            'characters' => $characters,
                        ],
                        null,
                        null,
                        ['operation' => 'translate_batch']
                    );

                    return array_map(fn($t) => $t['text'], $translations);
                }
            }

            $this->usage->recordUsage(
                $provider,
                [
                    'requests' => 1,
                    'characters' => $characters,
                ],
                null,
                'DeepL translate failed: HTTP ' . $response->status() . ' - ' . $response->body(),
                ['operation' => 'translate_batch']
            );

            Log::error('DeepL translation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            $this->usage->recordUsage(
                $provider,
                [
                    'requests' => 1,
                    'characters' => $characters,
                ],
                null,
                'DeepL translate exception: ' . $e->getMessage(),
                ['operation' => 'translate_batch']
            );

            Log::error('DeepL translation exception', ['message' => $e->getMessage()]);
        }

        return array_fill(0, count($texts), null);
    }
}
