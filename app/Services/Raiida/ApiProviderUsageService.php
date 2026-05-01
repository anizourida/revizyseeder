<?php

namespace App\Services\Raiida;

use App\Models\Raiida\ApiProvider;
use App\Models\Raiida\ApiProviderUsage;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Support\Facades\Http;

class ApiProviderUsageService
{
    /**
     * @param  array{requests?:int,input_tokens?:int,output_tokens?:int,total_tokens?:int,characters?:int}  $delta
     * @param  array{used?:int,limit?:int}|null  $remoteSnapshot
     * @param  array<string,mixed>  $metadata
     */
    public function recordUsage(
        ApiProvider $provider,
        array $delta = [],
        ?array $remoteSnapshot = null,
        ?string $error = null,
        array $metadata = []
    ): ApiProviderUsage {
        $usage = $this->currentUsage($provider);

        $usage->requests_count = max(0, (int) $usage->requests_count + max(0, (int) ($delta['requests'] ?? 0)));
        $usage->input_tokens_count = max(0, (int) $usage->input_tokens_count + max(0, (int) ($delta['input_tokens'] ?? 0)));
        $usage->output_tokens_count = max(0, (int) $usage->output_tokens_count + max(0, (int) ($delta['output_tokens'] ?? 0)));
        $usage->total_tokens_count = max(0, (int) $usage->total_tokens_count + max(0, (int) ($delta['total_tokens'] ?? 0)));
        $usage->characters_count = max(0, (int) $usage->characters_count + max(0, (int) ($delta['characters'] ?? 0)));

        if ($remoteSnapshot !== null) {
            if (array_key_exists('used', $remoteSnapshot) && $remoteSnapshot['used'] !== null) {
                $usage->remote_used = max(0, (int) $remoteSnapshot['used']);
            }
            if (array_key_exists('limit', $remoteSnapshot) && $remoteSnapshot['limit'] !== null) {
                $usage->remote_limit = max(0, (int) $remoteSnapshot['limit']);
            }
            $usage->last_synced_at = now();
        }

        if ($error !== null && trim($error) !== '') {
            $usage->last_error = mb_substr($error, 0, 60000, 'UTF-8');
        } elseif ($remoteSnapshot !== null) {
            $usage->last_error = null;
        }

        if ($metadata !== []) {
            $usage->metadata = array_merge((array) $usage->metadata, $metadata);
        }

        $usage->save();

        return $usage->refresh();
    }

    public function currentUsage(ApiProvider $provider): ApiProviderUsage
    {
        return ApiProviderUsage::query()->firstOrCreate(
            [
                'api_provider_id' => $provider->id,
                'period_key' => now()->format('Y-m'),
            ],
            [
                'requests_count' => 0,
                'input_tokens_count' => 0,
                'output_tokens_count' => 0,
                'total_tokens_count' => 0,
                'characters_count' => 0,
            ]
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function refreshRemoteUsage(ApiProvider $provider): array
    {
        $providerType = strtolower((string) $provider->provider_type);

        if ($providerType === 'deepl' || $provider->slug === 'deepl') {
            return $this->refreshDeepLUsage($provider);
        }

        return $this->summary($provider);
    }

    /**
     * @return array<string,mixed>
     */
    public function summary(ApiProvider $provider): array
    {
        $usage = $this->currentUsage($provider);

        $unit = strtolower((string) ($provider->limit_unit ?? 'requests'));
        $remoteUsed = $usage->remote_used !== null ? (int) $usage->remote_used : null;
        $remoteLimit = $usage->remote_limit !== null ? (int) $usage->remote_limit : null;
        $configuredLimit = $provider->monthly_limit !== null ? (int) $provider->monthly_limit : null;

        $trackedUsed = match ($unit) {
            'characters' => (int) $usage->characters_count,
            'tokens' => (int) $usage->total_tokens_count,
            default => (int) $usage->requests_count,
        };

        $effectiveUsed = $remoteUsed !== null ? max($trackedUsed, $remoteUsed) : $trackedUsed;
        $effectiveLimit = $remoteLimit ?? $configuredLimit;
        $remaining = $effectiveLimit !== null ? max(0, $effectiveLimit - $effectiveUsed) : null;

        return [
            'provider' => [
                'id' => (int) $provider->id,
                'slug' => (string) $provider->slug,
                'provider_type' => (string) $provider->provider_type,
                'display_name' => $provider->display_name,
                'model' => $provider->model,
                'base_url' => $provider->base_url,
                'is_active' => (bool) $provider->is_active,
                'limit_unit' => $unit,
            ],
            'usage' => [
                'period_key' => (string) $usage->period_key,
                'requests_count' => (int) $usage->requests_count,
                'input_tokens_count' => (int) $usage->input_tokens_count,
                'output_tokens_count' => (int) $usage->output_tokens_count,
                'total_tokens_count' => (int) $usage->total_tokens_count,
                'characters_count' => (int) $usage->characters_count,
                'remote_used' => $remoteUsed,
                'remote_limit' => $remoteLimit,
                'configured_limit' => $configuredLimit,
                'used' => $effectiveUsed,
                'limit' => $effectiveLimit,
                'remaining' => $remaining,
                'last_synced_at' => optional($usage->last_synced_at)->toIso8601String(),
                'last_error' => $usage->last_error,
            ],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function refreshDeepLUsage(ApiProvider $provider): array
    {
        $apiKey = trim((string) ($provider->api_key ?? ''));
        if ($apiKey === '') {
            throw new RaiidaApiException('DeepL API key is not configured', 422);
        }

        $endpoint = (string) ($provider->usage_endpoint ?? '');
        if ($endpoint === '') {
            $endpoint = str_ends_with($apiKey, ':fx')
                ? 'https://api-free.deepl.com/v2/usage'
                : 'https://api.deepl.com/v2/usage';
        }

        $response = Http::withHeaders([
            'Authorization' => "DeepL-Auth-Key {$apiKey}",
        ])
            ->timeout(30)
            ->retry(2, 500)
            ->get($endpoint);

        if (! $response->successful()) {
            $this->recordUsage(
                $provider,
                [],
                null,
                'DeepL usage sync failed: HTTP ' . $response->status() . ' - ' . $response->body(),
                ['endpoint' => $endpoint]
            );

            throw new RaiidaApiException('DeepL usage sync failed', $response->status());
        }

        $used = $response->integer('character_count');
        $limit = $response->integer('character_limit');

        $this->recordUsage(
            $provider,
            [],
            [
                'used' => max(0, $used),
                'limit' => max(0, $limit),
            ],
            null,
            ['endpoint' => $endpoint, 'source' => 'deepl']
        );

        return $this->summary($provider->refresh());
    }
}
