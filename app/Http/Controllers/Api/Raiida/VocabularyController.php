<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\VocabularyItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VocabularyController extends Controller
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

        $items = $query->orderByDesc('extracted_at')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(static fn (VocabularyItem $item): array => $item->toArray())
            ->values();

        return response()->json($items);
    }

    public function stats(): JsonResponse
    {
        $total = VocabularyItem::count();
        $byGrade = VocabularyItem::query()
            ->selectRaw('grade, COUNT(*) as aggregate')
            ->groupBy('grade')
            ->pluck('aggregate', 'grade')
            ->map(static fn ($count): int => (int) $count);

        return response()->json([
            'total_items' => $total,
            'by_grade' => $byGrade,
        ]);
    }
}
