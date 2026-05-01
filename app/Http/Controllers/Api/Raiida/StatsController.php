<?php

namespace App\Http\Controllers\Api\Raiida;

use App\Http\Controllers\Controller;
use App\Models\Raiida\FileAsset;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalFiles = FileAsset::count();
        $downloadedFiles = FileAsset::where('is_downloaded', true)->count();
        $corruptFiles = FileAsset::where('is_corrupt', true)->count();
        $totalSize = (int) FileAsset::where('is_downloaded', true)->sum('size_bytes');

        return response()->json([
            'total_files' => $totalFiles,
            'downloaded_files' => $downloadedFiles,
            'corrupt_files' => $corruptFiles,
            'total_size_gb' => $totalSize / (1024 * 1024 * 1024),
            'completion_percentage' => $totalFiles > 0
                ? ($downloadedFiles / $totalFiles) * 100
                : 0,
        ]);
    }
}
