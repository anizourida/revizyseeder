<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\Audio;
use App\Services\Raiida\AudioGenerationService;
use Illuminate\Http\JsonResponse;

class AudioController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = Audio::query()
            ->select([
                'audios.id',
                'audios.vocabulary_item_id',
                'audios.file_path',
                'audios.created_at',
                'vocabulary_items.word',
                'vocabulary_items.image_path',
            ])
            ->join('vocabulary_items', 'audios.vocabulary_item_id', '=', 'vocabulary_items.id')
            ->orderByDesc('audios.created_at')
            ->get();

        $payload = $rows->map(static function (Audio $audio): array {
            return [
                'id' => $audio->id,
                'vocabulary_id' => (int) $audio->vocabulary_item_id,
                'word' => $audio->word,
                'image' => $audio->image_path,
                'audio_file' => $audio->file_path,
                'created_at' => $audio->created_at,
            ];
        })->values();

        return response()->json($payload);
    }

    public function generateNext(AudioGenerationService $service): JsonResponse
    {
        return response()->json($service->generateNext());
    }
}
