<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Raiida\FileAsset;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileAssetOpenController extends Controller
{
    public function __invoke(FileAsset $fileAsset): BinaryFileResponse|RedirectResponse
    {
        $localPath = $this->resolveLocalFilePath((string) $fileAsset->local_path);
        if ($localPath !== null) {
            return response()->file($localPath, [
                'Content-Disposition' => 'inline; filename="' . basename($localPath) . '"',
            ]);
        }

        $originalUrl = trim((string) $fileAsset->original_url);
        if ($originalUrl !== '' && filter_var($originalUrl, FILTER_VALIDATE_URL) !== false) {
            return redirect()->away($originalUrl);
        }

        abort(404, 'File not found.');
    }

    private function resolveLocalFilePath(string $relativePath): ?string
    {
        $normalized = str_replace('\\', '/', trim($relativePath, " \t\n\r\0\x0B/"));
        if ($normalized === '' || str_contains($normalized, '..')) {
            return null;
        }

        $root = rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);
        if ($root === '') {
            return null;
        }

        $absolutePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        if (! is_file($absolutePath)) {
            return null;
        }

        $realAbsolutePath = realpath($absolutePath);
        $realRoot = realpath($root);
        if ($realAbsolutePath === false || $realRoot === false) {
            return null;
        }

        $realRootWithSlash = rtrim($realRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (! str_starts_with($realAbsolutePath, $realRootWithSlash)) {
            return null;
        }

        return $realAbsolutePath;
    }
}
