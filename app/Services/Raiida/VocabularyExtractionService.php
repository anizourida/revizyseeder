<?php

namespace App\Services\Raiida;

use App\Models\Raiida\FileAsset;
use App\Models\Raiida\VocabularyItem;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;
use ZipArchive;

class VocabularyExtractionService
{
    /**
     * Preview extracted vocabulary rows for one lesson without writing DB rows.
     *
     * @return array<int,array{
     *   word:string,
     *   image_path:string,
     *   lesson_id:string,
     *   grade:string,
     *   subject:string,
     *   period:string,
     *   week:string
     * }>
     */
    public function previewLessonVocabulary(
        string $presentationPath,
        string $lessonId,
        string $grade,
        string $subject,
        string $period,
        string $week
    ): array {
        if (! is_file($presentationPath)) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($presentationPath) !== true) {
            return [];
        }

        $tempDir = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'raiida_vocab_preview_'
            . md5($lessonId . '|' . $presentationPath . '|' . microtime(true));

        @mkdir($tempDir, 0777, true);

        try {
            $rows = $this->extractVocabularyRowsFromZip(
                $zip,
                $lessonId,
                $grade,
                $subject,
                $period,
                $week,
                $tempDir
            );
        } finally {
            $zip->close();
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
        }

        return $rows;
    }

    /**
     * @param  array{limit?:int,lesson_id?:string,force?:bool}  $options
     */
    public function runGlobalExtraction(array $options = []): array
    {
        $force = (bool) ($options['force'] ?? false);
        $lessonId = trim((string) ($options['lesson_id'] ?? ''));
        $limit = (int) ($options['limit'] ?? 0);

        $query = FileAsset::query()
            ->select('file_assets.*')
            ->join('weeks', 'file_assets.week_id', '=', 'weeks.id')
            ->join('periods', 'weeks.period_id', '=', 'periods.id')
            ->join('subjects', 'periods.subject_id', '=', 'subjects.id')
            ->join('grades', 'subjects.grade_id', '=', 'grades.id')
            ->whereIn('grades.name', config('raiida.vocabulary.grade_whitelist', ['1', '2', '3', '4', '5', '6']))
            ->whereIn('subjects.name', config('raiida.vocabulary.subject_whitelist', ['Français', 'French', 'FR']))
            ->where('file_assets.session_id', (string) config('raiida.vocabulary.session_for_global_extraction', 'S1'))
            ->where('file_assets.is_downloaded', true)
            ->orderBy('file_assets.id');

        if ($lessonId !== '') {
            $query->whereIn('file_assets.filename', [
                $lessonId . '.pptx',
                $lessonId . '.ppsx',
            ]);
        }

        if (! $force) {
            $query->where(function ($query): void {
                $query->where('file_assets.is_vocab_extracted', false);

                if ((bool) config('raiida.vocabulary.retry_zero_count_lessons', false)) {
                    $query->orWhere(function ($retry): void {
                        $retry
                            ->where('file_assets.is_vocab_extracted', true)
                            ->where('file_assets.vocab_count', 0);
                    });
                }
            });
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $files = $query->get();

        $summary = [
            'total' => $files->count(),
            'processed' => 0,
            'failed' => 0,
            'extracted_total' => 0,
        ];

        foreach ($files as $fileAsset) {
            try {
                $result = $this->extractFromFileAsset($fileAsset);
                $summary['processed']++;
                $summary['extracted_total'] += (int) ($result['count'] ?? 0);
            } catch (Throwable $e) {
                $summary['failed']++;
                Log::warning('Vocabulary extraction failed', [
                    'file_asset_id' => $fileAsset->id,
                    'filename' => $fileAsset->filename,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $summary;
    }

    public function extractSingleFile(int $fileId): array
    {
        $fileAsset = FileAsset::query()->find($fileId);
        if (! $fileAsset) {
            return ['error' => 'File not found'];
        }

        $result = $this->extractFromFileAsset($fileAsset);

        return [
            'count' => (int) ($result['count'] ?? 0),
            'lesson' => (string) ($result['lesson'] ?? pathinfo((string) $fileAsset->filename, PATHINFO_FILENAME)),
        ];
    }

    private function extractFromFileAsset(FileAsset $fileAsset): array
    {
        $filename = (string) $fileAsset->filename;
        $lessonId = pathinfo($filename, PATHINFO_FILENAME);
        $filePath = $this->fullFilePath((string) $fileAsset->local_path);

        $nLevel = $this->resolveLevelFromFilename($filename);
        if (str_contains($nLevel, '&')) {
            $this->markLessonFilesExtracted($lessonId, 0);

            return [
                'lesson' => $lessonId,
                'count' => 0,
            ];
        }

        if (! is_file($filePath)) {
            Log::warning('Vocabulary extraction skipped missing file', [
                'file_asset_id' => $fileAsset->id,
                'path' => $filePath,
            ]);

            return [
                'lesson' => $lessonId,
                'count' => 0,
            ];
        }

        [$subject, $period, $week] = $this->extractLessonKeys($lessonId);
        $this->processPresentation($filePath, $lessonId, $nLevel, $subject, $period, $week);

        $count = VocabularyItem::query()
            ->where('lesson_id', $lessonId)
            ->count();

        $this->markLessonFilesExtracted($lessonId, $count);

        return [
            'lesson' => $lessonId,
            'count' => $count,
        ];
    }

    private function processPresentation(
        string $presentationPath,
        string $lessonId,
        string $grade,
        string $subject,
        string $period,
        string $week
    ): void {
        $zip = new ZipArchive();
        if ($zip->open($presentationPath) !== true) {
            return;
        }

        $assetsDir = $this->lessonAssetsDir($lessonId);
        $this->resetDirectory($assetsDir);
        $rows = $this->extractVocabularyRowsFromZip(
            $zip,
            $lessonId,
            $grade,
            $subject,
            $period,
            $week,
            $assetsDir
        );
        $extractedWords = [];
        foreach ($rows as $row) {
            VocabularyItem::query()->updateOrCreate(
                [
                    'word' => (string) $row['word'],
                    'lesson_id' => $lessonId,
                    'grade' => $grade,
                ],
                [
                    'image_path' => (string) $row['image_path'],
                    'subject' => $subject,
                    'period' => $period,
                    'week' => $week,
                ]
            );
            $extractedWords[] = (string) $row['word'];
        }

        $extractedWords = array_values(array_unique($extractedWords));
        if ($extractedWords !== []) {
            $this->purgeStaleUnlinkedVocabularyRows($lessonId, $grade, $extractedWords);
        }

        $zip->close();
    }

    /**
     * @return array<int,array{
     *   word:string,
     *   image_path:string,
     *   lesson_id:string,
     *   grade:string,
     *   subject:string,
     *   period:string,
     *   week:string
     * }>
     */
    private function extractVocabularyRowsFromZip(
        ZipArchive $zip,
        string $lessonId,
        string $grade,
        string $subject,
        string $period,
        string $week,
        string $assetsDir
    ): array {
        $slides = $this->slidePaths($zip);
        $marker = $this->normalizeText((string) config('raiida.vocabulary.marker_phrase', 'qui veut répéter'));
        $exclusions = config('raiida.vocabulary.text_exclusions', ['objectifs', 'enseignant', 'date', 'semaine', 'titre']);

        $dedupe = [];
        $rows = [];
        $seenByImage = [];

        foreach ($slides as $slidePath) {
            $texts = $this->extractSlideTexts($zip, $slidePath);

            if (! $this->containsMarker($texts, $marker)) {
                continue;
            }

            $word = $this->pickVocabularyWord($texts, $marker, $exclusions);
            if ($word === null) {
                continue;
            }

            $imageName = $this->extractSlideImage($zip, $slidePath, $assetsDir, $dedupe);
            if ($imageName === null) {
                continue;
            }

            $imagePath = 'vocab_assets/' . $lessonId . '/' . $imageName;
            if (isset($seenByImage[$imagePath])) {
                continue;
            }

            $seenByImage[$imagePath] = true;
            $rows[] = [
                'word' => $word,
                'image_path' => $imagePath,
                'lesson_id' => $lessonId,
                'grade' => $grade,
                'subject' => $subject,
                'period' => $period,
                'week' => $week,
            ];
        }

        return $rows;
    }

    /**
     * @param  array<int,string>  $extractedWords
     */
    private function purgeStaleUnlinkedVocabularyRows(string $lessonId, string $grade, array $extractedWords): void
    {
        VocabularyItem::query()
            ->where('lesson_id', $lessonId)
            ->where('grade', $grade)
            ->whereNotIn('word', $extractedWords)
            ->where(function ($query): void {
                $query->whereNull('concept_id')->orWhere('concept_id', '');
            })
            ->where(function ($query): void {
                $query->whereNull('revizy_image_file_id')->orWhere('revizy_image_file_id', '');
            })
            ->where(function ($query): void {
                $query->whereNull('revizy_audio_file_id')->orWhere('revizy_audio_file_id', '');
            })
            ->where(function ($query): void {
                $query->whereNull('walidio_image_id')->orWhere('walidio_image_id', '');
            })
            ->where(function ($query): void {
                $query->whereNull('flashcard_id')->orWhere('flashcard_id', '');
            })
            ->whereNull('revizy_skill_id')
            ->whereNull('revizy_unite_id')
            ->delete();
    }

    private function markLessonFilesExtracted(string $lessonId, int $count): void
    {
        FileAsset::query()
            ->whereIn('filename', [$lessonId . '.pptx', $lessonId . '.ppsx'])
            ->update([
                'is_vocab_extracted' => true,
                'vocab_count' => $count,
            ]);
    }

    private function extractLessonKeys(string $lessonId): array
    {
        $parts = explode('_', $lessonId);

        $subject = $parts[0] ?? 'FR';
        $period = $parts[2] ?? 'P1';
        $week = $parts[3] ?? 'SEM1';

        return [$subject, $period, $week];
    }

    private function resolveLevelFromFilename(string $filename): string
    {
        if (preg_match('/_(N[1-6](?:&[1-6])?)_/i', $filename, $matches) === 1) {
            return strtoupper($matches[1]);
        }

        return 'N1';
    }

    private function extractSlideTexts(ZipArchive $zip, string $slidePath): array
    {
        $xml = $zip->getFromName($slidePath);
        if (! is_string($xml) || $xml === '') {
            return [];
        }

        $document = $this->parseXml($xml);
        if ($document === null) {
            return [];
        }

        $document->registerXPathNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $document->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $texts = [];
        $shapes = $document->xpath('//p:sp[p:txBody]') ?: [];

        foreach ($shapes as $shape) {
            $paragraphs = $shape->xpath('.//a:p') ?: [];
            if ($paragraphs === []) {
                continue;
            }

            $paragraphTexts = [];
            foreach ($paragraphs as $paragraph) {
                $paragraphText = $this->extractParagraphText($paragraph);
                if ($paragraphText !== '') {
                    $paragraphTexts[] = $paragraphText;
                }
            }

            if ($paragraphTexts === []) {
                continue;
            }

            // Keep both the combined shape text and each paragraph text:
            // this preserves marker+word slides while also fixing wrapped text fragments.
            $shapeText = $this->cleanVocabularyText(implode(' ', $paragraphTexts));
            if ($shapeText !== '') {
                $texts[] = $shapeText;
            }

            foreach ($paragraphTexts as $paragraphText) {
                $texts[] = $paragraphText;
            }
        }

        return array_values(array_unique($texts));
    }

    private function extractParagraphText(SimpleXMLElement $paragraph): string
    {
        $nodes = $paragraph->xpath('.//a:t') ?: [];
        if ($nodes === []) {
            return '';
        }

        $parts = [];
        foreach ($nodes as $node) {
            $parts[] = (string) $node;
        }

        return $this->cleanVocabularyText(implode('', $parts));
    }

    private function containsMarker(array $texts, string $marker): bool
    {
        foreach ($texts as $text) {
            if (str_contains($this->normalizeText($text), $marker)) {
                return true;
            }
        }

        return false;
    }

    private function pickVocabularyWord(array $texts, string $marker, array $exclusions): ?string
    {
        $candidates = [];

        foreach ($texts as $text) {
            $content = $this->cleanVocabularyText((string) $text);
            $normalized = $this->normalizeText($content);
            $isArticle = $this->isArticleToken($normalized);

            if ($normalized === '' || str_contains($normalized, $marker)) {
                continue;
            }

            if (mb_strlen($content, 'UTF-8') < 2 && ! $isArticle) {
                continue;
            }

            $isExcluded = false;
            foreach ($exclusions as $exclusion) {
                if (str_contains($normalized, $this->normalizeText((string) $exclusion))) {
                    $isExcluded = true;
                    break;
                }
            }

            if ($isExcluded) {
                continue;
            }

            $candidates[] = $content;
        }

        if ($candidates === []) {
            return null;
        }

        $first = $candidates[0];
        $firstNormalized = $this->normalizeText($first);

        if (! $this->isArticleToken($firstNormalized)) {
            return $this->cleanVocabularyText($first);
        }

        $target = null;
        foreach (array_slice($candidates, 1) as $candidate) {
            if (! $this->isArticleToken($this->normalizeText($candidate))) {
                $target = $candidate;
                break;
            }
        }

        if ($target === null) {
            return $this->cleanVocabularyText($first);
        }

        return $this->cleanVocabularyText($this->joinSplitVocabulary($first, $target));
    }

    private function isArticleToken(string $normalized): bool
    {
        return in_array($normalized, [
            'le',
            'la',
            'les',
            'un',
            'une',
            'des',
            'du',
            'l',
            'd',
            "l'",
            "de l'",
            'de la',
        ], true);
    }

    private function joinSplitVocabulary(string $first, string $second): string
    {
        $left = rtrim($first);
        $right = ltrim($second);

        if ($left === '' || $right === '') {
            return trim($left . ' ' . $right);
        }

        if (preg_match("/[’']$/u", $left) === 1) {
            return $left . $right;
        }

        return $left . ' ' . $right;
    }

    private function cleanVocabularyText(string $text): string
    {
        $clean = str_replace(["\u{00A0}", '’'], [' ', "'"], $text);
        $clean = preg_replace("/(?<=\\p{L})\\s*'\\s*(?=\\p{L})/u", "'", $clean) ?? $clean;
        $clean = preg_replace('/\b(\d{1,2})\s+(re|er|e|eme|ème)\b/ui', '$1$2', $clean) ?? $clean;
        $clean = preg_replace('/\s+/u', ' ', $clean) ?? $clean;

        return trim($clean);
    }

    private function extractSlideImage(ZipArchive $zip, string $slidePath, string $assetsDir, array &$dedupe): ?string
    {
        $relsPath = dirname($slidePath) . '/_rels/' . basename($slidePath) . '.rels';
        $xml = $zip->getFromName($relsPath);
        if (! is_string($xml) || $xml === '') {
            return null;
        }

        $rels = $this->parseXml($xml);
        if ($rels === null) {
            return null;
        }

        $rels->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/package/2006/relationships');
        $nodes = $rels->xpath('//r:Relationship') ?: [];

        $imageRels = [];
        $slideLayoutPath = null;
        foreach ($nodes as $node) {
            $type = (string) $node['Type'];
            $target = (string) $node['Target'];
            if ($target !== '' && str_contains($type, '/slideLayout')) {
                $slideLayoutPath = $this->resolveRelativePath(dirname($slidePath), $target);
                continue;
            }

            $id = (string) $node['Id'];
            if ($id === '' || $target === '' || ! str_contains($type, '/image')) {
                continue;
            }

            $imageRels[$id] = $this->resolveRelativePath(dirname($slidePath), $target);
        }

        if ($imageRels === []) {
            return null;
        }

        // Prefer the "main" image on the slide instead of the first relationship.
        // Many slides include small decorative icons (often listed first in .rels),
        // while the vocabulary picture is the largest <p:pic> on the slide.
        $rankedIds = [];

        $slideXml = $zip->getFromName($slidePath);
        if (is_string($slideXml) && $slideXml !== '') {
            $slide = $this->parseXml($slideXml);
            if ($slide !== null) {
                $slide->registerXPathNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
                $slide->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
                $slide->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

                $pics = $slide->xpath('//p:pic') ?: [];
                $candidates = [];
                $order = 0;

                foreach ($pics as $pic) {
                    $order++;

                    $blips = $pic->xpath('.//a:blip') ?: [];
                    if ($blips === []) {
                        continue;
                    }

                    $blip = $blips[0];
                    $attrs = $blip->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                    $rid = isset($attrs['embed']) ? (string) $attrs['embed'] : '';
                    if ($rid === '' || ! array_key_exists($rid, $imageRels)) {
                        continue;
                    }

                    $area = 0;
                    $extNodes = $pic->xpath('.//a:xfrm/a:ext') ?: [];
                    if ($extNodes !== []) {
                        $ext = $extNodes[0];
                        $cx = (int) ($ext['cx'] ?? 0);
                        $cy = (int) ($ext['cy'] ?? 0);
                        if ($cx > 0 && $cy > 0) {
                            $area = $cx * $cy;
                        }
                    }

                    $phNodes = $pic->xpath('.//p:nvPr/p:ph') ?: [];
                    $isPlaceholder = $phNodes !== [];

                    if ($area === 0 && $isPlaceholder && $slideLayoutPath !== null) {
                        $idx = (string) ($phNodes[0]['idx'] ?? '');
                        if ($idx !== '') {
                            $layoutArea = $this->resolvePlaceholderAreaFromLayout($zip, $slideLayoutPath, $idx);
                            if ($layoutArea > 0) {
                                $area = $layoutArea;
                            }
                        }
                    }

                    $candidates[] = [
                        'rid' => $rid,
                        'area' => $area,
                        'is_placeholder' => $isPlaceholder,
                        'order' => $order,
                    ];
                }

                if ($candidates !== []) {
                    usort($candidates, static function (array $a, array $b): int {
                        if (($a['area'] ?? 0) !== ($b['area'] ?? 0)) {
                            return (int) ($b['area'] ?? 0) <=> (int) ($a['area'] ?? 0);
                        }

                        if (($a['is_placeholder'] ?? false) !== ($b['is_placeholder'] ?? false)) {
                            return (int) ($b['is_placeholder'] ?? false) <=> (int) ($a['is_placeholder'] ?? false);
                        }

                        return (int) ($a['order'] ?? 0) <=> (int) ($b['order'] ?? 0);
                    });

                    foreach ($candidates as $candidate) {
                        $rid = (string) ($candidate['rid'] ?? '');
                        if ($rid !== '' && ! in_array($rid, $rankedIds, true)) {
                            $rankedIds[] = $rid;
                        }
                    }
                }
            }
        }

        $tryIds = $rankedIds !== [] ? $rankedIds : array_keys($imageRels);

        foreach ($tryIds as $id) {
            if (! isset($imageRels[$id])) {
                continue;
            }

            $resolved = $imageRels[$id];
            $blob = $zip->getFromName($resolved);
            if (! is_string($blob) || $blob === '') {
                continue;
            }

            $hash = md5($blob);
            if (isset($dedupe[$hash])) {
                return $dedupe[$hash];
            }

            $baseName = basename($resolved);
            $name = $this->uniqueFileName($assetsDir, $baseName, $blob);
            file_put_contents($assetsDir . DIRECTORY_SEPARATOR . $name, $blob);

            $dedupe[$hash] = $name;

            return $name;
        }

        return null;
    }

    private function resolvePlaceholderAreaFromLayout(ZipArchive $zip, string $layoutPath, string $idx): int
    {
        static $cache = [];

        if (isset($cache[$layoutPath][$idx])) {
            return (int) $cache[$layoutPath][$idx];
        }

        $xml = $zip->getFromName($layoutPath);
        if (! is_string($xml) || $xml === '') {
            $cache[$layoutPath][$idx] = 0;
            return 0;
        }

        $layout = $this->parseXml($xml);
        if ($layout === null) {
            $cache[$layoutPath][$idx] = 0;
            return 0;
        }

        $layout->registerXPathNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $layout->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        $paths = [
            sprintf('//p:sp[p:nvSpPr/p:nvPr/p:ph[@type="pic" and @idx="%s"]]/p:spPr/a:xfrm/a:ext', $idx),
            sprintf('//p:pic[p:nvPicPr/p:nvPr/p:ph[@type="pic" and @idx="%s"]]/p:spPr/a:xfrm/a:ext', $idx),
            sprintf('//p:sp[p:nvSpPr/p:nvPr/p:ph[@idx="%s"]]/p:spPr/a:xfrm/a:ext', $idx),
            sprintf('//p:pic[p:nvPicPr/p:nvPr/p:ph[@idx="%s"]]/p:spPr/a:xfrm/a:ext', $idx),
        ];

        $extNodes = [];
        foreach ($paths as $path) {
            $extNodes = $layout->xpath($path) ?: [];
            if ($extNodes !== []) {
                break;
            }
        }

        if ($extNodes === []) {
            $cache[$layoutPath][$idx] = 0;
            return 0;
        }

        $ext = $extNodes[0];
        $cx = (int) ($ext['cx'] ?? 0);
        $cy = (int) ($ext['cy'] ?? 0);

        $area = ($cx > 0 && $cy > 0) ? $cx * $cy : 0;
        $cache[$layoutPath][$idx] = $area;

        return $area;
    }

    private function uniqueFileName(string $dir, string $baseName, string $blob): string
    {
        $candidate = $baseName;
        $path = $dir . DIRECTORY_SEPARATOR . $candidate;

        if (! is_file($path)) {
            return $candidate;
        }

        if (md5_file($path) === md5($blob)) {
            return $candidate;
        }

        $name = pathinfo($baseName, PATHINFO_FILENAME);
        $ext = pathinfo($baseName, PATHINFO_EXTENSION);
        $index = 1;

        do {
            $candidate = $name . '_' . $index . ($ext !== '' ? '.' . $ext : '');
            $path = $dir . DIRECTORY_SEPARATOR . $candidate;
            $index++;
        } while (is_file($path));

        return $candidate;
    }

    private function resolveRelativePath(string $baseDir, string $target): string
    {
        $target = str_replace('\\', '/', $target);
        $parts = explode('/', trim($baseDir . '/' . $target, '/'));

        $resolved = [];
        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($resolved);
                continue;
            }

            $resolved[] = $part;
        }

        return implode('/', $resolved);
    }

    private function parseXml(string $xml): ?SimpleXMLElement
    {
        $prev = libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return $parsed instanceof SimpleXMLElement ? $parsed : null;
    }

    private function normalizeText(string $text): string
    {
        $normalized = mb_strtolower(trim($text), 'UTF-8');
        $normalized = str_replace(['’', '?', '!', '؟'], ["'", '', '', ''], $normalized);
        $normalized = preg_replace('/\s+/u', ' ', $normalized);

        return trim((string) $normalized);
    }

    private function slidePaths(ZipArchive $zip): array
    {
        $paths = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (! is_string($name)) {
                continue;
            }

            if (preg_match('#^ppt/slides/slide\d+\.xml$#', $name) === 1) {
                $paths[] = $name;
            }
        }

        usort($paths, static function (string $a, string $b): int {
            preg_match('/slide(\d+)\.xml$/', $a, $ma);
            preg_match('/slide(\d+)\.xml$/', $b, $mb);

            return ((int) ($ma[1] ?? 0)) <=> ((int) ($mb[1] ?? 0));
        });

        return $paths;
    }

    private function resetDirectory(string $path): void
    {
        if (is_dir($path)) {
            $files = scandir($path);
            if ($files !== false) {
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    $current = $path . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($current)) {
                        $this->deleteDirectory($current);
                    } else {
                        @unlink($current);
                    }
                }
            }
        } else {
            mkdir($path, 0777, true);
        }
    }

    private function deleteDirectory(string $path): void
    {
        $files = scandir($path);
        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $current = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($current)) {
                    $this->deleteDirectory($current);
                } else {
                    @unlink($current);
                }
            }
        }

        @rmdir($path);
    }

    private function lessonAssetsDir(string $lessonId): string
    {
        return rtrim((string) config('raiida.vocab_assets_root'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $lessonId;
    }

    private function fullFilePath(string $relativePath): string
    {
        return rtrim((string) config('raiida.files_root'), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    }
}
