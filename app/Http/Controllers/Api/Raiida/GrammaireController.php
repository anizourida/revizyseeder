<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\Grammaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GrammaireController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Grammaire::query();

        if ($request->filled('n')) {
            $query->where('n', (string) $request->query('n'));
        }
        if ($request->filled('p')) {
            $query->where('p', (string) $request->query('p'));
        }
        if ($request->filled('sem')) {
            $query->where('sem', (string) $request->query('sem'));
        }

        $rows = $query->orderBy('n')
            ->orderBy('p')
            ->orderBy('sem')
            ->get();

        return response()->json(
            $rows->map(static fn (Grammaire $item): array => $item->toArray())->values()
        );
    }
}
