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

    public function updateQuestion(
        string $questionId,
        ?string $name = null,
        ?string $type = null,
        ?string $status = null,
        ?array $data = null
    ): array {
        if (! is_numeric($questionId)) {
            throw new RevizyPublishException('Invalid question id');
        }

        $apiKey = (string) config('raiida.revizy.api_key');
        if ($apiKey === '') {
            throw new RevizyPublishException('REVIZY_API_KEY is not configured');
        }

        $baseUrl = rtrim((string) config('raiida.revizy.base_url'), '/');

        $payload = [];
        if ($name !== null) {
            $payload['name'] = $name;
        }
        if ($type !== null) {
            $payload['type'] = $type;
        }
        if ($status !== null) {
            $payload['status'] = $status;
        }
        if ($data !== null) {
            $payload['data'] = $data;
        }

        try {
            $response = Http::baseUrl($baseUrl)
                ->timeout(30)
                ->withHeaders([
                    'X-SYSTEM-API-KEY' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('/questions/' . (int) $questionId, $payload);
        } catch (Throwable $exception) {
            throw new RevizyPublishException($exception->getMessage());
        }

        if (! $response->successful()) {
            throw new RevizyPublishException(
                'Revizy update failed',
                $response->status(),
                $response->body()
            );
        }

        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }
}
