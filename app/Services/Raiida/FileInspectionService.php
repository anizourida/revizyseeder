<?php

namespace App\Services\Raiida;

use App\Models\Raiida\FileAsset;
use Illuminate\Support\Facades\Log;
use Throwable;
use ZipArchive;

class FileInspectionService
{
    public function run(): array
    {
        $files = FileAsset::query()
            ->where('is_downloaded', true)
            ->where('is_integrity_checked', false)
            ->orderBy('id')
            ->get();

        $summary = [
            'total' => $files->count(),
            'checked' => 0,
            'corrupt' => 0,
            'missing' => 0,
        ];

        foreach ($files as $file) {
            $fullPath = $this->fullPath((string) $file->local_path);

            if (! is_file($fullPath)) {
                $file->is_downloaded = false;
                $file->save();
                $summary['missing']++;
                continue;
            }

            $isValid = $this->checkFileIntegrity($fullPath);
            $file->is_integrity_checked = true;
            $file->is_corrupt = ! $isValid;
            $file->save();

            $summary['checked']++;
            if (! $isValid) {
                $summary['corrupt']++;
            }
        }

        return $summary;
    }

    private function checkFileIntegrity(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['pptx', 'pptm', 'ppsx'], true)) {
            return $this->isValidOpenXmlPresentation($path);
        }

        if (in_array($ext, ['ppt', 'pps'], true)) {
            return is_file($path) && filesize($path) > 0;
        }

        return false;
    }

    private function isValidOpenXmlPresentation(string $path): bool
    {
        $zip = new ZipArchive();

        try {
            $opened = $zip->open($path);
            if ($opened !== true) {
                return false;
            }

            $hasContentTypes = $zip->locateName('[Content_Types].xml') !== false;
            $hasPresentationXml = $zip->locateName('ppt/presentation.xml') !== false
                || $zip->locateName('ppt/slides/slide1.xml') !== false;

            $zip->close();

            return $hasContentTypes && $hasPresentationXml;
        } catch (Throwable $e) {
            Log::warning('Integrity check failed for file', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            if ($zip->status !== ZipArchive::ER_OK) {
                $zip->close();
            }

            return false;
        }
    }

    private function fullPath(string $relativePath): string
    {
        return rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    }
}
