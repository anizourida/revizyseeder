<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Jobs\Raiida\SyncFilesJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        SyncFilesJob::dispatch(
            (string) $request->attributes->get('workflow_context_id'),
            $user?->id,
            $user?->email,
            $user?->role
        );

        return response()->json([
            'message' => 'Sync started in background',
        ]);
    }
}
