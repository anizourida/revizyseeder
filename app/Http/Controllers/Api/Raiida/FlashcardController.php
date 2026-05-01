<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\External\RevizySystemClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FlashcardController extends Controller
{
    public function createFromVocabulary(int $asset_id, Request $request, RevizySystemClient $revizy): JsonResponse
    {
        $categoryId = $request->integer('flashcard_category_id');
        if ($categoryId <= 0) {
            return response()->json(['detail' => 'flashcard_category_id is required'], 422);
        }

        $item = VocabularyItem::query()->find($asset_id);
        if (! $item instanceof VocabularyItem) {
            return response()->json(['detail' => 'Asset not found'], 404);
        }

        $frontText = $this->styledFrontText((string) ($item->word ?? ''));

        $payload = [
            'flashcard_category_id' => $categoryId,
            'front_text' => $frontText,
            'back_text' => (string) ($item->ar_translation ?? ''),
            'front_media_file_secret' => $item->revizy_image_file_id,
            'front_audio_file_secret' => $item->revizy_audio_file_id,
            'back_media_file_secret' => null,
            'back_audio_file_secret' => null,
            'status' => 'published',
        ];

        try {
            $response = $revizy->post('/flashcards', $payload);
            $flashcardId = $revizy->extractResourceId($response);

            if (is_string($flashcardId) && $flashcardId !== '') {
                $item->flashcard_id = $flashcardId;
                $item->save();

                return response()->json([
                    'success' => true,
                    'flashcard_id' => $flashcardId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Flashcard created but ID not returned',
            ]);
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    private function styledFrontText(string $text): string
    {
        $text = preg_replace('/^(Le|Un)\s/', '[BLUE]$1[/BLUE] ', $text) ?? $text;

        return preg_replace('/^(La|Une)\s/', '[PINK]$1[/PINK] ', $text) ?? $text;
    }
}
