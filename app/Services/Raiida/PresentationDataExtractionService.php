<?php

namespace App\Services\Raiida;

use App\Models\Raiida\FileAsset;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class PresentationDataExtractionService
{
    public function extractFromFileAsset(FileAsset $fileAsset, bool $force = false): array
    {
        try {
            $filePath = $this->resolveAbsoluteFilePath((string) $fileAsset->local_path);
            if (! is_file($filePath)) {
                throw new RuntimeException("Local file not found: {$filePath}");
            }

            $lessonId = pathinfo((string) $fileAsset->filename, PATHINFO_FILENAME);
            if ($lessonId === '') {
                $lessonId = 'lesson_' . $fileAsset->id;
            }

            $outputRoot = rtrim((string) config('raiida.presentation_data.output_root', storage_path('app/presentation_data')), DIRECTORY_SEPARATOR);
            if ($outputRoot === '') {
                throw new RuntimeException('Presentation output root is empty.');
            }

            File::ensureDirectoryExists($outputRoot);

            $lessonDir = $outputRoot . DIRECTORY_SEPARATOR . $lessonId;
            $jsonPath = $lessonDir . DIRECTORY_SEPARATOR . 'data.json';
            $assetsDir = $lessonDir . DIRECTORY_SEPARATOR . 'assets';

            if (! $force && is_file($jsonPath)) {
                $summary = $this->buildSummaryFromJson($jsonPath, $lessonId, $assetsDir);
                $this->markAssetSuccess($fileAsset, $summary, $jsonPath, $assetsDir, fromCache: true);

                return $summary + ['from_cache' => true];
            }

            $pythonBin = (string) config('raiida.presentation_data.python_bin', 'python3');
            $scriptPath = $this->resolveScriptPath();
            if (! is_file($scriptPath)) {
                throw new RuntimeException("Presentation extractor script not found: {$scriptPath}");
            }

            $process = new Process([
                $pythonBin,
                $scriptPath,
                '--input',
                $filePath,
                '--lesson-id',
                $lessonId,
                '--output-root',
                $outputRoot,
            ]);
            $process->setTimeout(max(30, (int) config('raiida.presentation_data.process_timeout_seconds', 300)));
            $process->run();

            if (! $process->isSuccessful()) {
                $error = trim($process->getErrorOutput());
                if ($error === '') {
                    $error = trim($process->getOutput());
                }

                throw new RuntimeException($error !== '' ? $error : 'Unknown python extraction failure.');
            }

            if (! is_file($jsonPath)) {
                throw new RuntimeException("Expected output json missing: {$jsonPath}");
            }

            $summary = $this->buildSummaryFromJson($jsonPath, $lessonId, $assetsDir);
            $this->markAssetSuccess($fileAsset, $summary, $jsonPath, $assetsDir, fromCache: false);

            return $summary + [
                'stdout' => trim($process->getOutput()),
                'from_cache' => false,
            ];
        } catch (Throwable $e) {
            $this->markAssetFailure($fileAsset, $e->getMessage());
            throw $e;
        }
    }

    private function buildSummaryFromJson(string $jsonPath, string $lessonId, string $assetsDir): array
    {
        $payload = json_decode((string) file_get_contents($jsonPath), true, 512, JSON_THROW_ON_ERROR);
        $slides = is_array($payload['slides'] ?? null) ? $payload['slides'] : [];
        $slideCount = (int) ($payload['metadata']['total_slides'] ?? count($slides));

        $images = 0;
        $videos = 0;

        foreach ($slides as $slide) {
            $elements = is_array($slide['elements'] ?? null) ? $slide['elements'] : [];
            foreach ($elements as $element) {
                if (! is_array($element)) {
                    continue;
                }

                $type = strtolower((string) ($element['type'] ?? ''));
                if ($type === 'image') {
                    $images++;
                } elseif ($type === 'video') {
                    $videos++;
                }
            }
        }

        return [
            'lesson_id' => $lessonId,
            'slide_count' => $slideCount,
            'images' => $images,
            'videos' => $videos,
            'json_path' => $this->toProjectRelativePath($jsonPath),
            'assets_dir' => $this->toProjectRelativePath($assetsDir),
        ];
    }

    private function markAssetSuccess(
        FileAsset $fileAsset,
        array $summary,
        string $jsonPath,
        string $assetsDir,
        bool $fromCache
    ): void {
        $fileAsset->is_presentation_data_extracted = true;
        $fileAsset->presentation_slide_count = (int) ($summary['slide_count'] ?? 0);
        $fileAsset->presentation_json_path = $this->toProjectRelativePath($jsonPath);
        $fileAsset->presentation_assets_dir = $this->toProjectRelativePath($assetsDir);
        $fileAsset->presentation_extraction_error = null;
        if (! $fromCache || $fileAsset->presentation_extracted_at === null) {
            $fileAsset->presentation_extracted_at = now();
        }
        $fileAsset->save();
    }

    private function markAssetFailure(FileAsset $fileAsset, string $message): void
    {
        $fileAsset->is_presentation_data_extracted = false;
        $fileAsset->presentation_extraction_error = mb_substr($message, 0, 60000, 'UTF-8');
        $fileAsset->save();
    }

    private function resolveAbsoluteFilePath(string $relativePath): string
    {
        $normalized = trim(str_replace('\\', '/', $relativePath), " \t\n\r\0\x0B/");
        $root = rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR);

        return $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
    }

    private function resolveScriptPath(): string
    {
        $scriptPath = (string) config('raiida.presentation_data.script_path', base_path('scripts/extract_lesson_data.py'));
        if (str_starts_with($scriptPath, DIRECTORY_SEPARATOR)) {
            return $scriptPath;
        }

        return base_path($scriptPath);
    }

    private function toProjectRelativePath(string $absolutePath): string
    {
        $absolutePath = str_replace('\\', '/', $absolutePath);
        $base = rtrim(str_replace('\\', '/', base_path()), '/');

        if (str_starts_with($absolutePath, $base . '/')) {
            return ltrim(substr($absolutePath, strlen($base)), '/');
        }

        return $absolutePath;
    }
}
