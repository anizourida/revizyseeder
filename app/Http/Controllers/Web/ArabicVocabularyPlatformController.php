<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArabicVocabularyPlatformController extends Controller
{
    /**
     * Display the visual Arabic Vocabulary platform.
     */
    public function index(Request $request): View|JsonResponse
    {
        $grade = $request->query('grade');
        $period = $request->query('period');
        $week = $request->query('week');
        $strategy = $request->query('strategy');
        $hasImage = $request->query('has_image');
        $search = trim((string) $request->query('search', ''));

        $query = ArabicVocabularyItem::query();

        if ($grade && $grade !== 'all') {
            $query->where('grade', $grade);
        }

        if ($period && $period !== 'all') {
            $query->where('period', $period);
        }

        if ($week && $week !== 'all') {
            $query->where('week', $week);
        }

        if ($strategy && $strategy !== 'all') {
            $query->where('strategy', $strategy);
        }

        if ($hasImage === '1' || $hasImage === 'true') {
            $query->whereNotNull('image_path')->where('image_path', '!=', '');
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('word', 'like', "%{$search}%")
                    ->orWhere('raw_word', 'like', "%{$search}%")
                    ->orWhere('example_sentence', 'like', "%{$search}%")
                    ->orWhere('lesson_id', 'like', "%{$search}%");
            });
        }

        // Ordered by Grade, Period, Week, Slide
        $items = $query
            ->orderByRaw("CASE grade 
                WHEN 'N1' THEN 1 
                WHEN 'N2' THEN 2 
                WHEN 'N3' THEN 3 
                WHEN 'N4' THEN 4 
                WHEN 'N5' THEN 5 
                WHEN 'N6' THEN 6 
                WHEN 'N1&2' THEN 7 
                WHEN 'N3&4' THEN 8 
                WHEN 'N5&6' THEN 9 
                ELSE 10 END ASC")
            ->orderByRaw("CAST(SUBSTR(period, 2) AS INTEGER) ASC")
            ->orderByRaw("CAST(SUBSTR(week, 4) AS INTEGER) ASC")
            ->orderBy('slide_index', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => ArabicVocabularyItem::count(),
            'with_images' => ArabicVocabularyItem::whereNotNull('image_path')->where('image_path', '!=', '')->count(),
            'strategies' => [
                'flashcard' => ArabicVocabularyItem::where('strategy', 'معجم مصور')->count(),
                'mojami' => ArabicVocabularyItem::where('strategy', 'المعجم المساعد')->count(),
                'network' => ArabicVocabularyItem::where('strategy', 'شبكة المفردات')->count(),
                'map' => ArabicVocabularyItem::where('strategy', 'خريطة الكلمة')->count(),
            ],
            'filtered_count' => $items->count(),
        ];

        if ($request->is('api/*') || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $items->count(),
                'stats' => $stats,
                'items' => $items,
            ]);
        }

        $availableGrades = ArabicVocabularyItem::query()->distinct()->whereNotNull('grade')->orderBy('grade')->pluck('grade');
        $availablePeriods = ArabicVocabularyItem::query()->distinct()->whereNotNull('period')->orderBy('period')->pluck('period');
        $availableWeeks = ArabicVocabularyItem::query()->distinct()->whereNotNull('week')->orderBy('week')->pluck('week');
        $availableStrategies = ArabicVocabularyItem::query()->distinct()->whereNotNull('strategy')->orderBy('strategy')->pluck('strategy');

        return view('arabic-vocabulary.platform', compact(
            'items',
            'stats',
            'availableGrades',
            'availablePeriods',
            'availableWeeks',
            'availableStrategies',
            'grade',
            'period',
            'week',
            'strategy',
            'hasImage',
            'search'
        ));
    }

    /**
     * API endpoint for fetching items as JSON.
     */
    public function apiItems(Request $request): JsonResponse
    {
        return $this->index($request);
    }
}
