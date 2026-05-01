<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\VocabularyItem;
use App\Services\Raiida\AssetSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VocabularyAssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min($request->integer('limit', 100), 500));
        $offset = max(0, $request->integer('offset', 0));

        $query = VocabularyItem::query();

        if ($request->filled('grade')) {
            $query->where('grade', (string) $request->query('grade'));
        }
        if ($request->filled('period')) {
            $query->where('period', (string) $request->query('period'));
        }
        if ($request->filled('week')) {
            $query->where('week', (string) $request->query('week'));
        }

        $total = (clone $query)->count();
        $rows = $query->orderBy('id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $items = $rows->map(static function (VocabularyItem $item): array {
            $data = $item->toArray();
            $data['image'] = $item->image_path;
            $data['audio'] = $item->audio_path;
            $data['name'] = $item->word;
            $data['name_ar'] = $item->ar_translation;

            return $data;
        })->values();

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function syncAudioLinks(AssetSyncService $service): JsonResponse
    {
        return response()->json($service->syncAudioPaths());
    }

    public function searchByConcept(string $concept_id): JsonResponse
    {
        $item = VocabularyItem::query()
            ->where('concept_id', $concept_id)
            ->first();

        if (! $item instanceof VocabularyItem) {
            return response()->json([
                'detail' => 'Concept not found in local vocabulary assets',
            ], 404);
        }

        return response()->json([
            'id' => $item->id,
            'vocabulary_id' => $item->id,
            'image' => $item->image_path,
            'audio' => $item->audio_path,
            'name' => $item->word,
            'name_ar' => $item->ar_translation,
            'concept_id' => $item->concept_id,
            'vocabulary' => [
                'id' => $item->id,
                'word' => $item->word,
                'ar_translation' => $item->ar_translation,
                'grade' => $item->grade,
                'period' => $item->period,
                'week' => $item->week,
                'lesson_id' => $item->lesson_id,
                'image_path' => $item->image_path,
            ],
        ]);
    }

    public function findBySecretId(string $secret_id): JsonResponse
    {
        $item = VocabularyItem::query()
            ->where('revizy_image_file_id', $secret_id)
            ->first();

        if (! $item instanceof VocabularyItem) {
            $item = VocabularyItem::query()
                ->where('revizy_audio_file_id', $secret_id)
                ->first();
        }

        if (! $item instanceof VocabularyItem) {
            return response()->json([
                'detail' => 'Asset not found with this secret ID',
            ], 404);
        }

        return response()->json([
            'id' => $item->id,
            'vocabulary_id' => $item->id,
            'name' => $item->word,
            'image' => $item->image_path,
            'audio' => $item->audio_path,
            'revizy_image_file_id' => $item->revizy_image_file_id,
            'revizy_audio_file_id' => $item->revizy_audio_file_id,
        ]);
    }
}
