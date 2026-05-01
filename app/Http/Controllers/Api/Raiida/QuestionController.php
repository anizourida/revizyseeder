<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\QuestionPublishAttempt;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use App\Services\Raiida\QuestionStudioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Throwable;

class QuestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = QuestionPublishAttempt::query()->orderByDesc('created_at');

        $limit = $request->query('limit');
        $offset = max(0, $request->integer('offset', 0));
        if ($limit !== null && is_numeric((string) $limit)) {
            $query->offset($offset)->limit(max(1, min((int) $limit, 500)));
        }

        return response()->json(
            $query->get()->map(static fn (QuestionPublishAttempt $attempt): array => $attempt->toArray())->values()
        );
    }

    public function publishAttempts(Request $request): JsonResponse
    {
        $query = QuestionPublishAttempt::query()->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        return response()->json(
            $query->get()->map(static fn (QuestionPublishAttempt $attempt): array => $attempt->toArray())->values()
        );
    }

    public function destroy(int $attempt_id): JsonResponse
    {
        $attempt = QuestionPublishAttempt::query()->find($attempt_id);
        if (! $attempt instanceof QuestionPublishAttempt) {
            return response()->json(['detail' => 'Question not found'], 404);
        }

        $attempt->delete();

        return response()->json(['success' => true]);
    }

    public function counts(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $view = $request->query('view');

        $all = $this->countsMap(null);
        $published = $this->countsMap('published');

        if ($view === 'both') {
            return response()->json([
                'all' => $all,
                'published' => $published,
            ]);
        }

        if ($status === 'published') {
            return response()->json($published);
        }

        return response()->json($all);
    }

    public function checkDuplicates(Request $request, QuestionStudioService $service): JsonResponse
    {
        $questions = $request->input('questions');
        if (! is_array($questions)) {
            return response()->json(['detail' => 'Invalid request payload.'], 422);
        }

        try {
            return response()->json($service->checkDuplicates($questions));
        } catch (RaiidaApiException $exception) {
            return response()->json(['detail' => $exception->getMessage()], $exception->statusCode());
        } catch (Throwable $exception) {
            return response()->json(['detail' => $exception->getMessage()], 500);
        }
    }

    private function countsMap(?string $status): Collection
    {
        $query = QuestionPublishAttempt::query()
            ->selectRaw('concept_id, COUNT(*) as aggregate')
            ->groupBy('concept_id');

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->pluck('aggregate', 'concept_id')
            ->map(static fn ($count): int => (int) $count);
    }
}
