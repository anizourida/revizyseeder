<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Jobs\Raiida\ExtractVocabularyJob;
use App\Services\Raiida\VocabularyExtractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VocabularyExtractionController extends Controller
{
    public function extractAll(Request $request): JsonResponse
    {
        $user = $request->user();

        ExtractVocabularyJob::dispatch(
            [],
            (string) $request->attributes->get('workflow_context_id'),
            $user?->id,
            $user?->email,
            $user?->role
        );

        return response()->json([
            'message' => 'Vocabulary extraction started in background',
        ]);
    }

    public function extractOne(int $file_id, VocabularyExtractionService $service): JsonResponse
    {
        $result = $service->extractSingleFile($file_id);

        return response()->json($result);
    }
}
