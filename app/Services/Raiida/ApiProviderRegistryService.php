<?php

namespace App\Services\Raiida;

use App\Models\Raiida\ApiProvider;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ApiProviderRegistryService
{
    /**
     * @return Collection<int, ApiProvider>
     */
    public function all(): Collection
    {
        $this->syncBuiltins();

        return ApiProvider::query()
            ->orderBy('slug')
            ->get();
    }

    public function findBySlug(string $slug): ?ApiProvider
    {
        $this->syncBuiltins();

        return ApiProvider::query()
            ->where('slug', strtolower(trim($slug)))
            ->first();
    }

    public function requireBySlug(string $slug): ApiProvider
    {
        $provider = $this->findBySlug($slug);
        if (! $provider instanceof ApiProvider) {
            throw new RaiidaApiException('API provider not found: ' . $slug, 404);
        }

        return $provider;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function upsert(array $attributes): ApiProvider
    {
        $slug = strtolower(trim((string) ($attributes['slug'] ?? '')));
        if ($slug === '') {
            throw new RaiidaApiException('Provider slug is required', 422);
        }

        $slug = Str::slug($slug, '-');
        if ($slug === '') {
            throw new RaiidaApiException('Invalid provider slug', 422);
        }

        $payload = [
            'provider_type' => strtolower(trim((string) ($attributes['provider_type'] ?? 'custom'))),
            'display_name' => $this->nullIfEmpty($attributes['display_name'] ?? null),
            'base_url' => $this->nullIfEmpty($attributes['base_url'] ?? null),
            'model' => $this->nullIfEmpty($attributes['model'] ?? null),
            'usage_endpoint' => $this->nullIfEmpty($attributes['usage_endpoint'] ?? null),
            'monthly_limit' => $this->nullablePositiveInt($attributes['monthly_limit'] ?? null),
            'limit_unit' => strtolower(trim((string) ($attributes['limit_unit'] ?? 'requests'))),
            'is_active' => array_key_exists('is_active', $attributes) ? (bool) $attributes['is_active'] : true,
            'metadata' => is_array($attributes['metadata'] ?? null) ? $attributes['metadata'] : null,
        ];

        if (array_key_exists('api_key', $attributes)) {
            $payload['api_key'] = $this->nullIfEmpty($attributes['api_key']);
        }
        if (array_key_exists('auth_cookie', $attributes)) {
            $payload['auth_cookie'] = $this->nullIfEmpty($attributes['auth_cookie']);
        }

        ApiProvider::query()->updateOrCreate(['slug' => $slug], $payload);

        $provider = ApiProvider::query()->where('slug', $slug)->first();
        if (! $provider instanceof ApiProvider) {
            throw new RaiidaApiException('Unable to persist provider: ' . $slug, 500);
        }

        return $provider;
    }

    private function syncBuiltins(): void
    {
        $builtins = (array) config('raiida.api_providers.builtins', []);

        foreach ($builtins as $slug => $definition) {
            if (! is_array($definition)) {
                continue;
            }

            $normalizedSlug = strtolower(trim((string) $slug));
            if ($normalizedSlug === '') {
                continue;
            }

            $defaults = [
                'provider_type' => strtolower(trim((string) ($definition['provider_type'] ?? $normalizedSlug))),
                'display_name' => $this->nullIfEmpty($definition['display_name'] ?? null),
                'base_url' => $this->nullIfEmpty($definition['base_url'] ?? null),
                'api_key' => $this->nullIfEmpty($definition['api_key'] ?? null),
                'auth_cookie' => $this->nullIfEmpty($definition['auth_cookie'] ?? null),
                'model' => $this->nullIfEmpty($definition['model'] ?? null),
                'usage_endpoint' => $this->nullIfEmpty($definition['usage_endpoint'] ?? null),
                'monthly_limit' => $this->nullablePositiveInt($definition['monthly_limit'] ?? null),
                'limit_unit' => strtolower(trim((string) ($definition['limit_unit'] ?? 'requests'))),
                'is_active' => (bool) ($definition['is_active'] ?? true),
                'metadata' => is_array($definition['metadata'] ?? null) ? $definition['metadata'] : null,
            ];

            $provider = ApiProvider::query()->firstOrCreate(
                ['slug' => $normalizedSlug],
                $defaults
            );

            $updates = [];

            if (($provider->api_key ?? null) === null && $defaults['api_key'] !== null) {
                $updates['api_key'] = $defaults['api_key'];
            }
            if (($provider->auth_cookie ?? null) === null && $defaults['auth_cookie'] !== null) {
                $updates['auth_cookie'] = $defaults['auth_cookie'];
            }
            if (($provider->base_url ?? null) === null && $defaults['base_url'] !== null) {
                $updates['base_url'] = $defaults['base_url'];
            }
            if (($provider->model ?? null) === null && $defaults['model'] !== null) {
                $updates['model'] = $defaults['model'];
            }
            if (($provider->usage_endpoint ?? null) === null && $defaults['usage_endpoint'] !== null) {
                $updates['usage_endpoint'] = $defaults['usage_endpoint'];
            }
            if (($provider->monthly_limit ?? null) === null && $defaults['monthly_limit'] !== null) {
                $updates['monthly_limit'] = $defaults['monthly_limit'];
            }
            if (empty($provider->provider_type) && $defaults['provider_type'] !== '') {
                $updates['provider_type'] = $defaults['provider_type'];
            }

            if ($updates !== []) {
                $provider->update($updates);
            }
        }
    }

    private function nullIfEmpty(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function nullablePositiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = (int) $value;

        return $normalized > 0 ? $normalized : null;
    }
}
