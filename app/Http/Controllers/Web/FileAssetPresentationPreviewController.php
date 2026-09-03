<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Raiida\FileAsset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileAssetPresentationPreviewController extends Controller
{
    private const DEFAULT_SLIDE_WIDTH_EMU = 12192000;

    private const DEFAULT_SLIDE_HEIGHT_EMU = 6858000;

    public function show(Request $request, FileAsset $fileAsset): View
    {
        $jsonPath = $this->resolvePresentationJsonAbsolutePath($fileAsset);
        if ($jsonPath === null) {
            abort(Response::HTTP_NOT_FOUND, 'Extracted presentation data not found.');
        }

        $payload = json_decode((string) file_get_contents($jsonPath), true);
        if (! is_array($payload)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid presentation JSON payload.');
        }

        $metadata = is_array($payload['metadata'] ?? null) ? $payload['metadata'] : [];

        $slideWidth = max(1, (int) ($metadata['slide_width_emu'] ?? self::DEFAULT_SLIDE_WIDTH_EMU));
        $slideHeight = max(1, (int) ($metadata['slide_height_emu'] ?? self::DEFAULT_SLIDE_HEIGHT_EMU));

        $slides = [];
        foreach ((array) ($payload['slides'] ?? []) as $slide) {
            if (! is_array($slide)) {
                continue;
            }

            $elements = [];
            foreach ((array) ($slide['elements'] ?? []) as $element) {
                if (! is_array($element)) {
                    continue;
                }

                $bbox = $this->normalizeBbox($element['bbox'] ?? null);
                if ($bbox === null) {
                    continue;
                }

                [$x, $y, $width, $height] = $bbox;

                $type = strtolower(trim((string) ($element['type'] ?? '')));
                if (! in_array($type, ['text', 'image', 'video'], true)) {
                    continue;
                }

                $assetPath = trim(str_replace('\\', '/', (string) ($element['file_path'] ?? '')), '/');

                $elements[] = [
                    'type' => $type,
                    'content' => (string) ($element['content'] ?? ''),
                    'description' => (string) ($element['description'] ?? ''),
                    'left_pct' => max(0, min(100, ($x / $slideWidth) * 100)),
                    'top_pct' => max(0, min(100, ($y / $slideHeight) * 100)),
                    'width_pct' => max(0, min(100, ($width / $slideWidth) * 100)),
                    'height_pct' => max(0, min(100, ($height / $slideHeight) * 100)),
                    'asset_url' => $assetPath !== ''
                        ? route('admin.files.preview.asset', ['fileAsset' => $fileAsset->id, 'assetPath' => $assetPath])
                        : null,
                ];
            }

            $slides[] = [
                'id' => (int) ($slide['id'] ?? (count($slides) + 1)),
                'elements' => $elements,
            ];
        }

        $requestedSlide = max(0, (int) $request->query('slide', 0));
        $requestedHighlight = trim((string) $request->query('highlight', $request->query('q', '')));
        $activeSlide = $requestedSlide > 0
            ? collect($slides)->firstWhere('id', $requestedSlide)
            : null;

        return view('raiida.presentation-preview', [
            'fileAsset' => $fileAsset,
            'slides' => $slides,
            'slideWidth' => $slideWidth,
            'slideHeight' => $slideHeight,
            'requestedSlide' => $requestedSlide,
            'requestedSlideExists' => $activeSlide !== null,
            'requestedHighlight' => $requestedHighlight,
        ]);
    }

    public function asset(FileAsset $fileAsset, string $assetPath): BinaryFileResponse
    {
        $jsonPath = $this->resolvePresentationJsonAbsolutePath($fileAsset);
        if ($jsonPath === null) {
            abort(Response::HTTP_NOT_FOUND, 'Extracted presentation data not found.');
        }

        $lessonDir = dirname($jsonPath);
        $resolvedAsset = $this->resolvePreviewAssetAbsolutePath($lessonDir, $assetPath);
        if ($resolvedAsset === null) {
            abort(Response::HTTP_NOT_FOUND, 'Preview asset not found.');
        }

        return response()->file($resolvedAsset, [
            'Content-Disposition' => 'inline; filename="' . basename($resolvedAsset) . '"',
        ]);
    }

    private function normalizeBbox(mixed $bbox): ?array
    {
        if (! is_array($bbox) || count($bbox) !== 4) {
            return null;
        }

        $values = array_map(static fn (mixed $value): int => max(0, (int) $value), $bbox);
        if ($values[2] === 0 || $values[3] === 0) {
            return null;
        }

        return $values;
    }

    private function resolvePresentationJsonAbsolutePath(FileAsset $fileAsset): ?string
    {
        $configured = trim((string) $fileAsset->presentation_json_path);
        if ($configured !== '') {
            $candidate = $this->resolveToAbsolutePath($configured);
            if ($candidate !== null) {
                return $candidate;
            }
        }

        $lessonId = pathinfo((string) $fileAsset->filename, PATHINFO_FILENAME);
        if ($lessonId === '') {
            return null;
        }

        $outputRoot = rtrim((string) config('raiida.presentation_data.output_root', storage_path('app/presentation_data')), DIRECTORY_SEPARATOR);
        if ($outputRoot === '') {
            return null;
        }

        $fallback = $outputRoot . DIRECTORY_SEPARATOR . $lessonId . DIRECTORY_SEPARATOR . 'data.json';

        return is_file($fallback) ? $fallback : null;
    }

    private function resolveToAbsolutePath(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        $absolutePath = str_starts_with($path, DIRECTORY_SEPARATOR)
            ? $path
            : base_path($path);

        return is_file($absolutePath) ? $absolutePath : null;
    }

    private function resolvePreviewAssetAbsolutePath(string $lessonDir, string $relativeAssetPath): ?string
    {
        $normalized = trim(str_replace('\\', '/', $relativeAssetPath), " \t\n\r\0\x0B/");
        if ($normalized === '' || str_contains($normalized, '..')) {
            return null;
        }

        $assetPath = $lessonDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        if (! is_file($assetPath)) {
            return null;
        }

        $realAssetPath = realpath($assetPath);
        $realLessonDir = realpath($lessonDir);
        if ($realAssetPath === false || $realLessonDir === false) {
            return null;
        }

        $realLessonDirWithSlash = rtrim($realLessonDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (! str_starts_with($realAssetPath, $realLessonDirWithSlash)) {
            return null;
        }

        return $realAssetPath;
    }
}
