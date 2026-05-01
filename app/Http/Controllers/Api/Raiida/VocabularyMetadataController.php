<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Http\Requests\Raiida\ClassifyVocabularyMetadataRequest;
use App\Jobs\Raiida\ClassifyVocabularyMetadataJob;
use App\Services\Raiida\VocabularyMetadataClassificationService;
use Illuminate\Http\JsonResponse;

class VocabularyMetadataController extends Controller
{
    public function classify(
        ClassifyVocabularyMetadataRequest $request,
        VocabularyMetadataClassificationService $service
    ): JsonResponse {
        $validated = $request->validated();
        $queue = (bool) ($validated['queue'] ?? false);

        $options = [
            'limit' => (int) ($validated['limit'] ?? 120),
            'grade' => $validated['grade'] ?? null,
            'period' => $validated['period'] ?? null,
            'week' => $validated['week'] ?? null,
            'dry_run' => (bool) ($validated['dry_run'] ?? false),
            'force' => (bool) ($validated['force'] ?? false),
        ];

        if ($queue) {
            $user = $request->user();

            ClassifyVocabularyMetadataJob::dispatch(
                $options,
                (string) $request->attributes->get('workflow_context_id'),
                $user?->id,
                $user?->email,
                $user?->role
            );

            return response()->json([
                'message' => 'Vocabulary metadata classification started in background',
                'options' => $options,
            ]);
        }

        $summary = $service->classify($options);

        return response()->json($summary);
    }
}
