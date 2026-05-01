<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Jobs\Raiida\InspectFilesJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrityController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        InspectFilesJob::dispatch(
            (string) $request->attributes->get('workflow_context_id'),
            $user?->id,
            $user?->email,
            $user?->role
        );

        return response()->json([
            'message' => 'Inspection started in background',
        ]);
    }
}
