<?php

namespace App\Services\Raiida\External;

use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WalidioClient
{
    /**
     * @param  array<string, scalar|null>  $fields
     * @return array<string, mixed>
     */
    public function uploadImage(string $filePath, array $fields): array
    {
        $contents = @file_get_contents($filePath);
        if (! is_string($contents)) {
            throw new RaiidaApiException('Unable to read file for Walidio upload', 500);
        }

        $response = $this->request()
            ->attach('seed_image_path', $contents, basename($filePath))
            ->post('/images', $fields);

        return $this->decodeOrFail($response);
    }

    private function request(): PendingRequest
    {
        $publicKey = (string) config('raiida.walidio.public_key');
        if ($publicKey === '') {
            throw new RaiidaApiException('WALIDIO_PUBLIC_KEY is not configured', 500);
        }

        $baseUrl = rtrim((string) config('raiida.walidio.base_url'), '/');
        if ($baseUrl === '') {
            throw new RaiidaApiException('WALIDIO_BASE_URL is not configured', 500);
        }

        return Http::baseUrl($baseUrl)
            ->timeout(30)
            ->retry(2, 500)
            ->acceptJson()
            ->withHeaders([
                'X-Public-Key' => $publicKey,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeOrFail(Response $response): array
    {
        if (! $response->successful()) {
            throw new RaiidaApiException(
                'Walidio API Error: ' . $response->body(),
                $response->status()
            );
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RaiidaApiException('Walidio returned invalid payload', 502);
        }

        return $payload;
    }
}
