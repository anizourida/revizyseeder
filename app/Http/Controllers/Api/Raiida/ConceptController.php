<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Http\Requests\Raiida\ConceptCreateRequest;
use App\Jobs\Raiida\GenerateVocabularyConceptsJob;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\External\RevizySystemClient;
use App\Services\Raiida\VocabularyConceptGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ConceptController extends Controller
{
    public function createForVocabulary(
        int $asset_id,
        ConceptCreateRequest $request,
        RevizySystemClient $revizy
    ): JsonResponse {
        $item = VocabularyItem::query()->find($asset_id);
        if (! $item instanceof VocabularyItem) {
            return response()->json(['detail' => 'Asset not found'], 404);
        }

        $payload = $this->preparePayload($request->validated());

        try {
            $response = $revizy->post('/concepts', $payload);
            $conceptId = $revizy->extractResourceId($response);

            if (is_string($conceptId) && $conceptId !== '') {
                $item->concept_id = $conceptId;
                $item->revizy_skill_id = (int) $payload['skill_id'];
                $item->revizy_unite_id = (int) $payload['unite_id'];
                $item->save();

                return response()->json([
                    'success' => true,
                    'concept_id' => $conceptId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Concept created but ID not returned',
            ]);
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function createGeneric(ConceptCreateRequest $request, RevizySystemClient $revizy): JsonResponse
    {
        $payload = $this->preparePayload($request->validated());

        try {
            $response = $revizy->post('/concepts', $payload);
            $conceptId = $revizy->extractResourceId($response);

            if (is_string($conceptId) && $conceptId !== '') {
                return response()->json([
                    'success' => true,
                    'concept_id' => $conceptId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Concept created but ID not returned',
            ]);
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function recoverMissing(Request $request, VocabularyConceptGenerationService $service): JsonResponse
    {
        $validated = $request->validate([
            'subject_code' => ['nullable', 'string', 'max:20'],
            'grade' => ['nullable', 'regex:/^N[1-6]$/'],
            'period' => ['nullable', 'regex:/^P[1-9][0-9]*$/'],
            'week' => ['nullable', 'regex:/^SEM[0-6]$/'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
            'wait_ms' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'debug_search' => ['nullable', 'boolean'],
            'queue' => ['nullable', 'boolean'],
            'description_template' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $options = [
            'subject_code' => isset($validated['subject_code']) ? strtoupper(trim((string) $validated['subject_code'])) : null,
            'grade' => $validated['grade'] ?? null,
            'period' => $validated['period'] ?? null,
            'week' => $validated['week'] ?? null,
            'limit' => $validated['limit'] ?? null,
            'wait_ms' => $validated['wait_ms'] ?? null,
            'debug_search' => $validated['debug_search'] ?? null,
            'description_template' => $validated['description_template'] ?? null,
            'status' => $validated['status'] ?? null,
            'is_active' => $validated['is_active'] ?? null,
        ];
        $options = array_filter($options, static fn ($value) => $value !== null);

        $queue = (bool) ($validated['queue'] ?? false);
        if ($queue) {
            $user = $request->user();
            GenerateVocabularyConceptsJob::dispatch(
                $options,
                (string) $request->attributes->get('workflow_context_id'),
                $user?->id,
                $user?->email,
                $user?->role
            );

            return response()->json([
                'queued' => true,
                'message' => 'Vocabulary concept recovery queued',
            ], 202);
        }

        try {
            $summary = $service->generateBatch($options);

            return response()->json($summary);
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function preparePayload(array $validated): array
    {
        $validated['status'] = $validated['status'] ?? 'published';
        $validated['is_active'] = array_key_exists('is_active', $validated)
            ? (bool) $validated['is_active']
            : true;

        return $validated;
    }
}
