<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\Conjugaison;
use App\Models\Raiida\Grammaire;
use App\Models\Raiida\VocabularyItem;
use Illuminate\Http\JsonResponse;

class RoadmapController extends Controller
{
    public function index(): JsonResponse
    {
        $conjugaisons = Conjugaison::query()->get(['n', 'p', 'sem', 'name', 'verbe', 'tense', 'raw_data']);
        $grammaires = Grammaire::query()->get(['n', 'p', 'sem', 'objectif']);
        $vocabStats = VocabularyItem::query()
            ->selectRaw('grade, period, week, COUNT(id) as aggregate')
            ->groupBy('grade', 'period', 'week')
            ->get();

        $roadmap = [];

        foreach ($conjugaisons as $item) {
            $key = "{$item->n}|{$item->p}|{$item->sem}";
            if (! isset($roadmap[$key])) {
                $roadmap[$key] = ['n' => $item->n, 'p' => $item->p, 'sem' => $item->sem, 'vocab_count' => 0];
            }

            $roadmap[$key]['conjugaison'] = $item->verbe
                ? trim((string) $item->verbe) . ' (' . trim((string) $item->tense) . ')'
                : mb_substr((string) ($item->name ?: $item->raw_data), 0, 50);
        }

        foreach ($grammaires as $item) {
            $key = "{$item->n}|{$item->p}|{$item->sem}";
            if (! isset($roadmap[$key])) {
                $roadmap[$key] = ['n' => $item->n, 'p' => $item->p, 'sem' => $item->sem, 'vocab_count' => 0];
            }

            $roadmap[$key]['grammaire'] = $item->objectif;
        }

        foreach ($vocabStats as $item) {
            $key = "{$item->grade}|{$item->period}|{$item->week}";
            if (! isset($roadmap[$key])) {
                $roadmap[$key] = ['n' => $item->grade, 'p' => $item->period, 'sem' => $item->week];
            }

            $roadmap[$key]['vocab_count'] = (int) $item->aggregate;
        }

        $payload = array_values(array_map(static function (array $item): array {
            return [
                'n' => $item['n'],
                'p' => $item['p'],
                'sem' => $item['sem'],
                'conjugaison' => $item['conjugaison'] ?? '-',
                'grammaire' => $item['grammaire'] ?? '-',
                'vocab_count' => (int) ($item['vocab_count'] ?? 0),
            ];
        }, $roadmap));

        usort($payload, static fn (array $a, array $b): int => [$a['n'], $a['p'], $a['sem']] <=> [$b['n'], $b['p'], $b['sem']]);

        return response()->json($payload);
    }
}
