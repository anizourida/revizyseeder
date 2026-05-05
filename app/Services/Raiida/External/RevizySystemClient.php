<?php

namespace App\Services\Raiida\External;

use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class RevizySystemClient
{
    /**
     * @return array<string, mixed>
     */
    public function uploadFile(string $filePath, string $name): array
    {
        $contents = @file_get_contents($filePath);
        if (! is_string($contents)) {
            throw new RaiidaApiException('Unable to read file for Revizy upload', 500);
        }

        $response = $this->request()
            ->attach('file', $contents, basename($filePath))
            ->post('/files', [
                'signature' => 'py',
                'name' => $name,
            ]);

        return $this->decodeOrFail($response);
    }

    /**
     * Replace the binary for an existing system file secret.
     *
     * @return array<string, mixed>
     */
    public function updateFile(string $secretId, string $filePath, string $name, string $signature = 'py'): array
    {
        $secret = trim($secretId);
        if ($secret === '') {
            throw new RaiidaApiException('Missing file secret ID for Revizy update', 422);
        }

        $contents = @file_get_contents($filePath);
        if (! is_string($contents)) {
            throw new RaiidaApiException('Unable to read file for Revizy update', 500);
        }

        $response = $this->request()
            ->attach('file', $contents, basename($filePath))
            ->post('/files/' . rawurlencode($secret), [
                'signature' => $signature,
                'name' => $name,
            ]);

        return $this->decodeOrFail($response);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $path): array
    {
        $response = $this->request()->get($path);

        return $this->decodeOrFail($response);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload): array
    {
        $response = $this->request()->post($path, $payload);

        return $this->decodeOrFail($response);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $path): array
    {
        $response = $this->request()->delete($path);

        return $this->decodeOrFail($response);
    }

    public function extractResourceId(array $payload): ?string
    {
        if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data']['id'])) {
            return (string) $payload['data']['id'];
        }

        if (isset($payload['id'])) {
            return (string) $payload['id'];
        }

        return null;
    }

    private function request(): PendingRequest
    {
        $apiKey = (string) config('raiida.revizy.api_key');
        if ($apiKey === '') {
            throw new RaiidaApiException('REVIZY_API_KEY is not configured', 500);
        }

        $baseUrl = rtrim((string) config('raiida.revizy.base_url'), '/');
        if ($baseUrl === '') {
            throw new RaiidaApiException('REVIZY_BASE_URL is not configured', 500);
        }

        return Http::baseUrl($baseUrl)
            ->timeout(30)
            ->retry(2, 500)
            ->acceptJson()
            ->withHeaders([
                'X-SYSTEM-API-KEY' => $apiKey,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeOrFail(Response $response): array
    {
        if (! $response->successful()) {
            throw new RaiidaApiException(
                'Revizy Error: ' . $response->body(),
                $response->status()
            );
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RaiidaApiException('Revizy returned invalid payload', 502);
        }

        return $payload;
    }
}
