<?php

namespace App\Services\Raiida;

use App\Models\Raiida\VocabularyItem;

class MediaFileLocator
{
    public function resolveImagePath(VocabularyItem $item): ?string
    {
        $raw = (string) ($item->image_path ?? '');
        if ($raw === '') {
            return null;
        }

        return $this->findExistingPath($raw, ['vocab_assets']);
    }

    public function resolveAudioPath(VocabularyItem $item): ?string
    {
        $raw = (string) ($item->audio_path ?? '');
        if ($raw === '') {
            return null;
        }

        return $this->findExistingPath($raw, ['audios']);
    }

    public function resolveBaseWordAudioPath(VocabularyItem $item): ?string
    {
        $raw = (string) ($item->base_word_audio_path ?? '');
        if ($raw === '') {
            return null;
        }

        return $this->findExistingPath($raw, ['audios', 'audios/base_words']);
    }

    /**
     * @param  array<int, string>  $defaultFolders
     */
    private function findExistingPath(string $rawPath, array $defaultFolders): ?string
    {
        if ($this->isAbsolute($rawPath) && is_file($rawPath)) {
            return $rawPath;
        }

        $normalized = ltrim(str_replace('\\', '/', $rawPath), '/');
        $basename = basename($normalized);

        $candidates = [
            public_path($normalized),
            base_path($normalized),
            storage_path('app/public/' . $normalized),
        ];

        foreach ($defaultFolders as $folder) {
            $candidates[] = public_path(trim($folder, '/') . '/' . $basename);
            $candidates[] = base_path(trim($folder, '/') . '/' . $basename);
            $candidates[] = storage_path('app/public/' . trim($folder, '/') . '/' . $basename);
        }

        $sourceRoot = rtrim((string) config('raiida.source_static_path', ''), "/\\");
        if ($sourceRoot !== '') {
            $candidates[] = $sourceRoot . '/' . $normalized;

            foreach ($defaultFolders as $folder) {
                $candidates[] = $sourceRoot . '/' . trim($folder, '/') . '/' . $basename;
            }
        }

        $seen = [];
        foreach ($candidates as $candidate) {
            if (! is_string($candidate) || $candidate === '') {
                continue;
            }

            $resolved = str_replace('\\', '/', $candidate);
            if (isset($seen[$resolved])) {
                continue;
            }
            $seen[$resolved] = true;

            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isAbsolute(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }

        return (bool) preg_match('/^[A-Za-z]:\\\\/', $path);
    }
}
