<?php

namespace App\Services\Raiida;

use App\Services\Raiida\Exceptions\RevizyPublishException;
use Illuminate\Support\Facades\Http;
use Throwable;

class RevizyQuestionApiClient
{
    public function publishQuestion(
        string $conceptId,
        string $name,
        string $type,
        string $status,
        array $data
    ): array {
        if (! is_numeric($conceptId)) {
            throw new RevizyPublishException('Invalid concept_id');
        }

        $apiKey = (string) config('raiida.revizy.api_key');
        if ($apiKey === '') {
            throw new RevizyPublishException('REVIZY_API_KEY is not configured');
        }

        $baseUrl = rtrim((string) config('raiida.revizy.base_url'), '/');

        try {
            $response = Http::baseUrl($baseUrl)
                ->timeout(30)
                ->withHeaders([
                    'X-SYSTEM-API-KEY' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('/questions', [
                    'concept_id' => (int) $conceptId,
                    'name' => $name,
                    'type' => $type,
                    'status' => $status,
                    'data' => $data,
                ]);
        } catch (Throwable $exception) {
            throw new RevizyPublishException($exception->getMessage());
        }

        if (! $response->successful()) {
            throw new RevizyPublishException(
                'Revizy publish failed',
                $response->status(),
                $response->body()
            );
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : [];
    }
}
