<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\Conjugaison;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConjugaisonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Conjugaison::query()->with(['grade', 'period', 'semWeek']);

        if ($request->filled('n')) {
            $query->where('n', (string) $request->query('n'));
        }
        if ($request->filled('p')) {
            $query->where('p', (string) $request->query('p'));
        }
        if ($request->filled('sem')) {
            $query->where('sem', (string) $request->query('sem'));
        }

        $rows = $query
            ->orderByRaw('CAST(SUBSTR(n, 2) AS INTEGER) ASC')
            ->orderByRaw('CAST(SUBSTR(p, 2) AS INTEGER) ASC')
            ->orderByRaw('CAST(SUBSTR(sem, 4) AS INTEGER) ASC')
            ->get();

        $payload = $rows->map(static function (Conjugaison $item): array {
            return [
                'id' => $item->id,
                'n' => $item->n,
                'p' => $item->p,
                'sem' => $item->sem,
                'grade' => $item->grade?->code ?? ($item->n ?: 'N?'),
                'grade_number' => $item->grade?->grade_number,
                'period' => $item->period?->code ?? ($item->p ?: 'P?'),
                'period_number' => $item->period?->period_number,
                'week_ref' => $item->semWeek?->code ?? ($item->sem ?: 'SEM?'),
                'week_number' => $item->semWeek?->week_number,
                'name' => $item->name,
                'question' => $item->question,
                'verbe' => $item->verbe,
                'tense' => $item->tense,
                'raw_data' => $item->raw_data,
                'related_raw_data' => $item->related_raw_data,
                'concept_id' => $item->concept_id,
                'week' => $item->week,
                'source_lesson_id' => $item->source_lesson_id,
                'source_slide_id' => $item->source_slide_id,
                'source_file_asset_id' => $item->source_file_asset_id,
                'source_preview_url' => $item->source_file_asset_id
                    ? route('admin.files.preview', ['fileAsset' => $item->source_file_asset_id])
                    : null,
                'source_slide_preview_url' => ($item->source_file_asset_id && $item->source_slide_id)
                    ? route('admin.files.preview', ['fileAsset' => $item->source_file_asset_id, 'slide' => $item->source_slide_id]) . '#slide-' . $item->source_slide_id
                    : null,
                'confidence_score' => $item->confidence_score,
                'revizy_skill_id' => $item->revizy_skill_id,
                'revizy_unite_id' => $item->revizy_unite_id,
            ];
        })->values();

        return response()->json($payload);
    }
    public function updateConcept(int $id, Request $request): JsonResponse
    {
        $conjugaison = Conjugaison::find($id);
        if (! $conjugaison) {
            return response()->json(['detail' => 'Conjugaison not found'], 404);
        }

        $request->validate([
            'concept_id'      => ['required', 'string'],
            'revizy_skill_id' => ['nullable', 'integer'],
            'revizy_unite_id' => ['nullable', 'integer'],
        ]);

        $conjugaison->concept_id      = $request->input('concept_id');
        $conjugaison->revizy_skill_id = $request->input('revizy_skill_id');
        $conjugaison->revizy_unite_id = $request->input('revizy_unite_id');
        $conjugaison->save();

        return response()->json(['success' => true, 'concept_id' => $conjugaison->concept_id]);
    }
}
