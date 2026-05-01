<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Http\Requests\Raiida\ApiProviderUpsertRequest;
use App\Services\Raiida\ApiProviderRegistryService;
use App\Services\Raiida\ApiProviderUsageService;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Http\JsonResponse;
use Throwable;

class ApiProviderController extends Controller
{
    public function index(
        ApiProviderRegistryService $registry,
        ApiProviderUsageService $usage
    ): JsonResponse {
        $providers = $registry->all();

        $items = [];
        foreach ($providers as $provider) {
            $items[] = $usage->summary($provider);
        }

        return response()->json([
            'items' => $items,
            'total' => count($items),
        ]);
    }

    public function upsert(
        ApiProviderUpsertRequest $request,
        ApiProviderRegistryService $registry,
        ApiProviderUsageService $usage
    ): JsonResponse {
        try {
            $provider = $registry->upsert($request->validated());

            return response()->json([
                'success' => true,
                'item' => $usage->summary($provider),
            ]);
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function usage(
        string $slug,
        ApiProviderRegistryService $registry,
        ApiProviderUsageService $usage
    ): JsonResponse {
        try {
            $provider = $registry->requireBySlug($slug);

            return response()->json($usage->summary($provider));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function refreshUsage(
        string $slug,
        ApiProviderRegistryService $registry,
        ApiProviderUsageService $usage
    ): JsonResponse {
        try {
            $provider = $registry->requireBySlug($slug);
            $summary = $usage->refreshRemoteUsage($provider);

            return response()->json([
                'success' => true,
                'item' => $summary,
            ]);
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }
}

