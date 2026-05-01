<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\QuestionStudioService;
use Illuminate\Http\JsonResponse;
use Throwable;

class QuestionGenerationController extends Controller
{
    public function generateForAsset(int $asset_id, QuestionStudioService $service): JsonResponse
    {
        try {
            return response()->json($service->generateQuestionsForAsset($asset_id));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function batchGeneratePublish(QuestionStudioService $service): JsonResponse
    {
        try {
            return response()->json($service->batchGenerateAndPublish());
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }
}
