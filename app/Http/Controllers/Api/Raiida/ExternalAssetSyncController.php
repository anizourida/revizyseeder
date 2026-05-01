<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\External\RevizySystemClient;
use App\Services\Raiida\External\WalidioClient;
use App\Services\Raiida\MediaFileLocator;
use Illuminate\Http\JsonResponse;
use Throwable;

class ExternalAssetSyncController extends Controller
{
    public function uploadImageToRevizy(
        int $asset_id,
        MediaFileLocator $locator,
        RevizySystemClient $revizy
    ): JsonResponse {
        $item = VocabularyItem::query()->find($asset_id);
        if (! $item instanceof VocabularyItem) {
            return response()->json(['detail' => 'Asset not found'], 404);
        }

        if (! $item->image_path) {
            return response()->json(['detail' => 'No image associated with this asset'], 400);
        }

        $path = $locator->resolveImagePath($item);
        if (! is_string($path)) {
            return response()->json(['detail' => 'File not found on server: ' . $item->image_path], 404);
        }

        try {
            $response = $revizy->uploadFile($path, $item->word ?: 'Asset ' . $item->id);
            $secret = $response['secret_id'] ?? null;
            if (! is_string($secret) || $secret === '') {
                return response()->json(['detail' => 'Revizy response missing secret_id'], 500);
            }

            $item->revizy_image_file_id = $secret;
            $item->save();

            return response()->json($this->assetPayload($item));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function uploadAudioToRevizy(
        int $asset_id,
        MediaFileLocator $locator,
        RevizySystemClient $revizy
    ): JsonResponse {
        $item = VocabularyItem::query()->find($asset_id);
        if (! $item instanceof VocabularyItem) {
            return response()->json(['detail' => 'Asset not found'], 404);
        }

        if (! $item->audio_path) {
            return response()->json(['detail' => 'No audio associated with this asset'], 400);
        }

        $path = $locator->resolveAudioPath($item);
        if (! is_string($path)) {
            return response()->json(['detail' => 'File not found on server: ' . $item->audio_path], 404);
        }

        try {
            $response = $revizy->uploadFile($path, $item->word ?: 'Asset ' . $item->id);
            $secret = $response['secret_id'] ?? null;
            if (! is_string($secret) || $secret === '') {
                return response()->json(['detail' => 'Revizy response missing secret_id'], 500);
            }

            $item->revizy_audio_file_id = $secret;
            $item->save();

            return response()->json($this->assetPayload($item));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    public function uploadToWalidio(
        int $asset_id,
        MediaFileLocator $locator,
        WalidioClient $walidio
    ): JsonResponse {
        $item = VocabularyItem::query()->find($asset_id);
        if (! $item instanceof VocabularyItem) {
            return response()->json(['detail' => 'Asset not found'], 404);
        }

        if (! $item->revizy_image_file_id) {
            return response()->json(['detail' => 'Must sync image to Revizy first before uploading to Walidio.'], 400);
        }

        if (! $item->image_path) {
            return response()->json(['detail' => 'No image file associated with this asset.'], 400);
        }

        $path = $locator->resolveImagePath($item);
        if (! is_string($path)) {
            return response()->json(['detail' => 'File not found: ' . $item->image_path], 404);
        }

        try {
            $payload = $walidio->uploadImage($path, [
                'name' => $item->word ?: 'Asset ' . $item->id,
                'n' => $item->grade,
                'p' => $item->period,
                'sem' => $item->week,
                'revizy_file_id' => $item->revizy_image_file_id,
            ]);

            $walidioId = null;
            if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data']['id'])) {
                $walidioId = (string) $payload['data']['id'];
            } elseif (isset($payload['id'])) {
                $walidioId = (string) $payload['id'];
            }

            if (! is_string($walidioId) || $walidioId === '') {
                return response()->json(['detail' => 'Walidio Response missing ID. Data: ' . json_encode($payload)], 500);
            }

            $item->walidio_image_id = $walidioId;
            $item->save();

            return response()->json($this->assetPayload($item));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function assetPayload(VocabularyItem $item): array
    {
        return [
            'id' => $item->id,
            'vocabulary_id' => $item->id,
            'name' => $item->word,
            'image' => $item->image_path,
            'audio' => $item->audio_path,
            'revizy_image_file_id' => $item->revizy_image_file_id,
            'revizy_audio_file_id' => $item->revizy_audio_file_id,
            'walidio_image_id' => $item->walidio_image_id,
            'flashcard_id' => $item->flashcard_id,
            'concept_id' => $item->concept_id,
        ];
    }
}
