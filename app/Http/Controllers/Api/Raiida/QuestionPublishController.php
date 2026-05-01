<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\QuestionStudioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class QuestionPublishController extends Controller
{
    public function publish(int $local_question_id, Request $request, QuestionStudioService $service): JsonResponse
    {
        $payload = $request->all();

        if (! is_array($payload['data'] ?? null)
            || ! array_key_exists('concept_id', $payload)
            || ! array_key_exists('name', $payload)
            || ! array_key_exists('type', $payload)) {
            return response()->json(['detail' => 'Invalid request payload.'], 422);
        }

        try {
            return response()->json($service->publishQuestion($local_question_id, $payload));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function unaccept(int $local_question_id, Request $request, QuestionStudioService $service): JsonResponse
    {
        $payload = $request->all();

        if (! is_array($payload['data'] ?? null)
            || ! array_key_exists('concept_id', $payload)
            || ! array_key_exists('name', $payload)) {
            return response()->json(['detail' => 'Invalid request payload.'], 422);
        }

        try {
            return response()->json($service->unacceptQuestion($local_question_id, $payload));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }
}
