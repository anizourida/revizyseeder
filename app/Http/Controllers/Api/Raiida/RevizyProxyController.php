<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\External\RevizySystemClient;
use Illuminate\Http\JsonResponse;
use Throwable;

class RevizyProxyController extends Controller
{
    public function skill(int $skill_id, RevizySystemClient $client): JsonResponse
    {
        return $this->proxy($client, '/skills/' . $skill_id);
    }

    public function unit(int $unit_id, RevizySystemClient $client): JsonResponse
    {
        return $this->proxy($client, '/unites/' . $unit_id);
    }

    public function flashcardCategory(int $category_id, RevizySystemClient $client): JsonResponse
    {
        return $this->proxy($client, '/flashcard-categories/' . $category_id);
    }

    public function concept(int $concept_id, RevizySystemClient $client): JsonResponse
    {
        return $this->proxy($client, '/concepts/' . $concept_id);
    }

    private function proxy(RevizySystemClient $client, string $path): JsonResponse
    {
        try {
            return response()->json($client->get($path));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }
}
