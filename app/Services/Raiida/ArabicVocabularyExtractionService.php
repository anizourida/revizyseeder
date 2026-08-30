<?php

namespace App\Services\Raiida;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;
use ZipArchive;

class ArabicVocabularyExtractionService
{
    /**
     * Arabic diacritics unicode regex.
     */
    protected const ARABIC_DIACRITICS_REGEX = '/[\x{064B}-\x{065F}\x{0670}\x{0671}\x{06D6}-\x{06ED}\x{0640}]/u';

    /**
     * Extract vocabulary for a specific lesson file or lesson ID.
     */
    public function extractLesson(string $lessonIdOrPath, bool $force = false): array
    {
        $filePath = $this->resolvePresentationFilePath($lessonIdOrPath);
        if (! $filePath || ! is_file($filePath)) {
            return [
                'success' => false,
                'lesson_id' => $lessonIdOrPath,
                'count' => 0,
                'error' => "Presentation file not found for '{$lessonIdOrPath}'",
            ];
        }

        $lessonId = pathinfo($filePath, PATHINFO_FILENAME);
        // Clean any leading tilde or temp prefix
        $lessonId = ltrim($lessonId, '~');
        $lessonId = trim($lessonId);

        [$grade, $period, $week] = $this->parseLessonMetadata($lessonId, $filePath);

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [
                'success' => false,
                'lesson_id' => $lessonId,
                'count' => 0,
                'error' => "Failed to open ZIP archive for '{$filePath}'",
            ];
        }

        $assetsDir = $this->lessonAssetsDir($lessonId);
        if (! is_dir($assetsDir)) {
            @mkdir($assetsDir, 0777, true);
        }

        try {
            $extractedItems = $this->extractVocabularyRowsFromZip(
                $zip,
                $lessonId,
                $grade,
                $period,
                $week,
                $assetsDir
            );

            $extractedAt = now();
            $savedCount = 0;

            foreach ($extractedItems as $item) {
                ArabicVocabularyItem::query()->updateOrCreate(
                    [
                        'word' => $item['word'],
                        'lesson_id' => $lessonId,
                        'grade' => $grade,
                    ],
                    [
                        'raw_word' => $item['raw_word'],
                        'example_sentence' => $item['example_sentence'] ?? null,
                        'strategy' => $item['strategy'] ?? null,
                        'subject' => 'AR',
                        'period' => $period,
                        'week' => $week,
                        'slide_index' => $item['slide_index'] ?? null,
                        'image_path' => $item['image_path'] ?? null,
                        'extracted_at' => $extractedAt,
                    ]
                );
                $savedCount++;
            }

            return [
                'success' => true,
                'lesson_id' => $lessonId,
                'grade' => $grade,
                'period' => $period,
                'week' => $week,
                'count' => $savedCount,
                'items' => $extractedItems,
            ];
        } finally {
            $zip->close();
        }
    }

    /**
     * Run batch extraction across Arabic lesson files.
     *
     * @param  array{grade?:?string,period?:?string,week?:?string,lesson_id?:?string,limit?:int,force?:bool}  $options
     */
    public function runBatchExtraction(array $options = []): array
    {
        $gradeFilter = $options['grade'] ?? null;
        $periodFilter = $options['period'] ?? null;
        $weekFilter = $options['week'] ?? null;
        $lessonIdFilter = $options['lesson_id'] ?? null;
        $limit = (int) ($options['limit'] ?? 0);
        $force = (bool) ($options['force'] ?? false);

        $arRoot = rtrim((string) config('raiida.files_root', base_path('files')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'AR';
        if (! is_dir($arRoot)) {
            return [
                'total' => 0,
                'processed' => 0,
                'failed' => 0,
                'extracted_total' => 0,
                'error' => "Arabic files root directory not found: {$arRoot}",
            ];
        }

        $files = $this->collectArabicFiles($arRoot, $gradeFilter, $periodFilter, $weekFilter, $lessonIdFilter);

        $summary = [
            'total' => count($files),
            'processed' => 0,
            'failed' => 0,
            'extracted_total' => 0,
            'errors' => [],
        ];

        $count = 0;
        foreach ($files as $file) {
            if ($limit > 0 && $count >= $limit) {
                break;
            }

            $lessonId = pathinfo($file, PATHINFO_FILENAME);
            if (! $force) {
                $alreadyExtracted = ArabicVocabularyItem::query()
                    ->where('lesson_id', $lessonId)
                    ->exists();
                if ($alreadyExtracted) {
                    continue;
                }
            }

            try {
                $res = $this->extractLesson($file, $force);
                if ($res['success']) {
                    $summary['processed']++;
                    $summary['extracted_total'] += (int) ($res['count'] ?? 0);
                } else {
                    $summary['failed']++;
                    $summary['errors'][] = [
                        'file' => basename($file),
                        'error' => $res['error'] ?? 'Unknown error',
                    ];
                }
            } catch (Throwable $e) {
                $summary['failed']++;
                $summary['errors'][] = [
                    'file' => basename($file),
                    'error' => $e->getMessage(),
                ];
                Log::warning('Arabic vocabulary extraction error', [
                    'file' => $file,
                    'error' => $e->getMessage(),
                ]);
            }

            $count++;
        }

        return $summary;
    }

    /**
     * Preview vocabulary from a presentation file without writing to DB.
     */
    public function previewLesson(string $filePath): array
    {
        if (! is_file($filePath)) {
            return [];
        }

        $lessonId = pathinfo($filePath, PATHINFO_FILENAME);
        [$grade, $period, $week] = $this->parseLessonMetadata($lessonId, $filePath);

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ar_vocab_prev_' . md5($filePath . microtime(true));
        @mkdir($tempDir, 0777, true);

        try {
            return $this->extractVocabularyRowsFromZip(
                $zip,
                $lessonId,
                $grade,
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
    }

    /**
     * Core slide extraction logic.
     */
    protected const NAV_TOKENS = [
        'نشاط اعتيادي', 'استماع وتحدث', 'استـماع وتحدث', 'اختتام الحصة', 'اختــتام الحــصة',
        'افتتاح الحصة', 'معجم', 'المعجم', 'معــــــــــجم', 'مـــعــجـــم', 'قراءة', 'كتابة',
        'قراءة/ كتابة', 'قراءة 1', 'قراءة 2', 'قراءة ح1', 'قراءة ح2', 'إنتاج كتابي', 'إنتاج كتابي ح1',
        'إنتاج كتابي الحصة 1', 'إنتاج كتابي الحصة 2', 'املاء', 'إملاء', 'صرف وتحويل', 'صرف و تحويل',
        'تراكيب', 'مشروع الوحدة', 'تقويم ودعم', 'ترحيب', 'هيكلة حصة اليوم', 'تنظيم حصص الأسبوع',
        'تنظيم حصص الاسبوع', 'هيكله حصه اليوم', 'تنظيم حصص', 'هيكله حصص', 'دليل تمرير', 'اليوم',
        'السلسلة', 'السلسله', 'خاص بالفئة', 'خاص بالفئه', 'شروط الحصول على نجمة التميز',
        'شروط الحصول على نجمه التميز', 'ماذا تعلمتم', 'واجباتكم المنزلية', 'واجباتكم المنزليه',
        'من يقرأ', 'من يقرأ؟', 'من يقرأ ؟', 'من يقرا', 'من يقرا؟', 'من يقرا ؟', 'من يقرؤها؟',
        'من يردد', 'من يردد؟', 'من يردد ؟', 'من يكرر؟', 'سأقرأ أولاً', 'سأقرأ اولا', 'ساقرا اولا',
        'من يقرأ مثلي؟', 'من يقرا مثلي؟', 'سأقرأ النص', 'ساقرا النص', 'تابعوا معي',
        'ورشة القراءة', 'ورشة القراءة 1', 'ورشة القراءة 2', 'ورشة الكتابة', 'مراجعة وتوليف',
        'أحسنتم', 'أحسنتم.', 'أحسنتم .', 'ممتاز', 'جيد', 'صححوا', 'ارفعوا الألواح',
        'مرحبا بكم', 'على الألواح', 'علي الالواح', 'الآن', 'الان', 'في ثنائيات', 'في البداية',
        'في البدايه', 'أولا', 'اولا', 'ثانيا', 'ثالثا', 'رابعا', 'الوسط', 'خذوا كراساتكم',
        'خذوا الكراسة', 'خذوا الكراسه', 'الصفحة', 'الصفحه', 'ص', 'دفاتر البحث', 'السبورة',
        'السبوره', 'انتبهوا للتصحيح', 'نصحح', 'وانجزوا نشاط', 'وأنجزوا نشاط',
    ];

    /**
     * Core slide extraction logic.
     */
    protected function extractVocabularyRowsFromZip(
        ZipArchive $zip,
        string $lessonId,
        string $grade,
        string $period,
        string $week,
        string $assetsDir
    ): array {
        $slides = $this->slidePaths($zip);
        $extracted = [];
        $seenWords = [];
        $dedupeImages = [];

        // Track global vocabulary words announced in list/banner slides
        $announcedWords = [];
        $activeStrategy = null;

        // Pass 1: Scan for banner vocabulary lists, strategies, and introductory slides
        foreach ($slides as $idx => $slidePath) {
            $texts = $this->extractSlideTexts($zip, $slidePath);
            if ($texts === []) {
                continue;
            }

            $fullText = implode(' ', $texts);
            $fullRaw = $this->stripArabicDiacritics($fullText);

            if ($this->containsAny($fullText, ['إستراتيجية', 'استراتيجية', 'الاشتقاق', 'الصفة المضافة', 'خريطة الكلمة', 'شبكة المفردات', 'المعجم المساعد'])) {
                if (str_contains($fullText, 'الاشتقاق')) {
                    $activeStrategy = 'الاشتقاق';
                } elseif (str_contains($fullText, 'الصفة المضافة')) {
                    $activeStrategy = 'الصفة المضافة';
                } elseif (str_contains($fullText, 'خريطة الكلمة')) {
                    $activeStrategy = 'خريطة الكلمة';
                } elseif (str_contains($fullText, 'شبكة المفردات')) {
                    $activeStrategy = 'شبكة المفردات';
                } elseif (str_contains($fullText, 'المعجم المساعد')) {
                    $activeStrategy = 'المعجم المساعد';
                }
            }

            // Skip schedule / overview slides
            if ($this->containsAny($fullRaw, ['تنظيم حصص', 'هيكله حصه', 'شروط الحصول', 'عند نهايه الحصة', 'عند نهايه الحصه', 'تمرير الجزء'])) {
                continue;
            }

            // Detect vocabulary announcement slides like: "المعجم: تعاونَ - رافقَ - مُتَحَمِّسَةٌ"
            if ($this->isVocabularyHeaderSlide($texts)) {
                $wordsFromHeader = $this->extractWordsFromHeaderSlide($texts);
                foreach ($wordsFromHeader as $w) {
                    $normW = $this->stripArabicDiacritics($w);
                    if ($normW !== '' && mb_strlen($normW, 'UTF-8') >= 2 && ! $this->isNavToken($normW)) {
                        $announcedWords[$normW] = $w;
                    }
                }
            }

            // Detect contextual vocabulary introduction slides (N3 to N6)
            if ($this->isContextualVocabIntroSlide($fullRaw)) {
                $wordsFromIntro = $this->extractWordsFromIntroSlide($texts);
                foreach ($wordsFromIntro as $w) {
                    $normW = $this->stripArabicDiacritics($w);
                    if ($normW !== '' && mb_strlen($normW, 'UTF-8') >= 2 && ! $this->isNavToken($normW)) {
                        $announcedWords[$normW] = $w;
                    }
                }
            }
        }

        // Pass 2: Extract specific vocabulary word slides with images & sentences
        foreach ($slides as $idx => $slidePath) {
            $slideNum = $idx + 1;
            $texts = $this->extractSlideTexts($zip, $slidePath);
            if ($texts === []) {
                continue;
            }

            $detectedItem = $this->detectVocabularySlide($texts, $announcedWords);
            if ($detectedItem !== null) {
                $word = $detectedItem['word'];
                $rawWord = $this->stripArabicDiacritics($word);

                if ($rawWord === '' || mb_strlen($rawWord, 'UTF-8') < 2 || $this->isNavToken($rawWord)) {
                    continue;
                }

                $imageName = $this->extractSlideImage($zip, $slidePath, $assetsDir, $dedupeImages);
                $imagePath = $imageName ? 'vocab_assets/ar/' . $lessonId . '/' . $imageName : null;

                if (isset($seenWords[$rawWord])) {
                    $existingIdx = $seenWords[$rawWord];
                    if (empty($extracted[$existingIdx]['image_path']) && $imagePath !== null) {
                        $extracted[$existingIdx]['image_path'] = $imagePath;
                    }
                    if (empty($extracted[$existingIdx]['example_sentence']) && ! empty($detectedItem['example_sentence'])) {
                        $extracted[$existingIdx]['example_sentence'] = $detectedItem['example_sentence'];
                    }
                    continue;
                }

                $seenWords[$rawWord] = count($extracted);
                $extracted[] = [
                    'word' => $word,
                    'raw_word' => $rawWord,
                    'example_sentence' => $detectedItem['example_sentence'] ?? null,
                    'strategy' => $activeStrategy,
                    'slide_index' => $slideNum,
                    'image_path' => $imagePath,
                ];
            }
        }

        // Pass 3: For announced words that didn't get dedicated single slides (especially in N3-N6)
        foreach ($announcedWords as $raw => $vocalized) {
            if (! isset($seenWords[$raw])) {
                $seenWords[$raw] = count($extracted);
                $extracted[] = [
                    'word' => $vocalized,
                    'raw_word' => $raw,
                    'example_sentence' => null,
                    'strategy' => $activeStrategy,
                    'slide_index' => null,
                    'image_path' => null,
                ];
            }
        }

        return $extracted;
    }

    /**
     * Check if slide is a vocabulary banner / list slide.
     */
    protected function isVocabularyHeaderSlide(array $texts): bool
    {
        $firstTexts = array_slice($texts, 0, 4);
        $hasHeaderToken = false;
        foreach ($firstTexts as $text) {
            $t = $this->stripArabicDiacritics($text);
            if (in_array($t, ['معجم', 'المعجم', 'معــــــــــجم', 'مـــعــجـــم', 'مفردات', 'المفردات', 'الرصيد المعجمي'], true)) {
                $hasHeaderToken = true;
                break;
            }
        }

        if (! $hasHeaderToken) {
            return false;
        }

        $fullText = implode(' ', $texts);

        return $this->containsAny($fullText, ['-', '–', '—', 'ــــ', '،', '/']);
    }

    /**
     * Check if slide is a contextual vocabulary introduction slide (e.g. N3-N6).
     */
    protected function isContextualVocabIntroSlide(string $fullRawText): bool
    {
        $cues = [
            'مفردات ستساعدكم على الفهم',
            'مفردات ستساعدكم على فهم النص',
            'مفردات ستساعدكم على فهم',
            'مفردات ستساعدكم',
            'مفردات ستفيدنا في فهم النص',
            'مفردات ستفيدنا',
            'مفردات تفيدنا في فهم النص',
            'مفردات تفيدنا في الفهم',
            'مفردات تفيدنا',
            'هذه مفردات ستساعدكم على فهم النص',
            'هذه مفردات ستساعدكم',
            'معاني المفردات التالية',
            'معاني المفردات التاليه',
            'معاني المفردات',
            'مفردات المعجم التي تعلمناها',
            'ستتعلمون اليوم مفردات جديدة',
            'ستتعلمون اليوم مفردات جديده',
            'سنتعرف مفردات ستفيدنا في فهم النص',
            'سنتعرف مفردات تفيدنا في فهم النص',
            'سنتعرف مفردات',
            'سنتعرف على مفردات',
        ];

        return $this->containsAny($fullRawText, $cues);
    }

    /**
     * Extract word tokens from a contextual intro slide.
     */
    protected function extractWordsFromIntroSlide(array $texts): array
    {
        $words = [];
        foreach ($texts as $text) {
            $cleaned = trim($text);
            $raw = $this->stripArabicDiacritics($cleaned);

            if ($this->isNavToken($raw)) {
                continue;
            }

            if ($this->isContextualVocabIntroSlide($raw)) {
                continue;
            }

            // Exclude full sentences or prompt directives
            if ($this->containsAny($raw, ['قبل', 'نص', 'فهم', 'مفردات', 'استماع', 'حصه', 'قراءه', 'ساقرا', 'سأقرأ', 'من يقرأ', 'من يقرا', 'من يختار', 'ارفعوا', 'نتذكر', 'البدايه', 'البداية'])) {
                continue;
            }

            $wordCount = count(explode(' ', $raw));
            if ($wordCount >= 1 && $wordCount <= 3 && mb_strlen($raw, 'UTF-8') >= 2 && ! is_numeric($raw)) {
                $cleanedWord = preg_replace('/^(?:معجم|المعجم|معــــــــــجم|مفردات)\s+/u', '', $cleaned);
                $cleanedWord = preg_replace('/\s+(?:معجم|المعجم|معــــــــــجم|مفردات)$/u', '', $cleanedWord);
                $cleanedWord = $this->cleanArabicBoundary($cleanedWord);
                if ($cleanedWord !== '' && ! $this->isNavToken($cleanedWord)) {
                    $words[] = $cleanedWord;
                }
            }
        }

        return array_values(array_unique($words));
    }

    protected function cleanArabicBoundary(string $text): string
    {
        $cleaned = preg_replace('/^[\s:؛\.\-–—\r\n\t]+|[\s:؛\.\-–—\r\n\t]+$/u', '', $text) ?? $text;

        return trim($cleaned);
    }

    /**
     * Extract word tokens from a vocabulary header slide.
     */
    protected function extractWordsFromHeaderSlide(array $texts): array
    {
        $words = [];
        $fullText = implode(' ', $texts);

        $parts = preg_split('/[\-–—،,\/:\r\n\t]+|ــــ+/u', $fullText);
        foreach ($parts as $part) {
            $p = $this->cleanArabicBoundary((string) $part);
            $p = preg_replace('/^(?:معجم|المعجم|معــــــــــجم|مـــعــجـــم|مفردات|الأسرة والعائلة)\s+/u', '', $p);
            $p = preg_replace('/\s+(?:معجم|المعجم|معــــــــــجم|مـــعــجـــم|مفردات)$/u', '', $p);
            $p = $this->cleanArabicBoundary($p);

            $pStripped = $this->stripArabicDiacritics($p);
            if ($pStripped === '' || $this->isNavToken($pStripped) || is_numeric($pStripped)) {
                continue;
            }

            if ($this->containsAny($pStripped, ['حصه', 'اسبوع', 'نشاط', 'استماع', 'قراءه', 'استراتيجيه', 'إستراتيجية'])) {
                continue;
            }

            if (count(explode(' ', $pStripped)) <= 2 && mb_strlen($pStripped, 'UTF-8') >= 2) {
                $words[] = $p;
            }
        }

        return array_values(array_unique($words));
    }

    /**
     * Detect if a slide presents a specific vocabulary word.
     */
    protected function detectVocabularySlide(array $texts, array $announcedWords): ?array
    {
        // Pattern A: Match against announced vocabulary words
        foreach ($announcedWords as $raw => $vocalized) {
            foreach ($texts as $t) {
                $tClean = $this->cleanArabicBoundary((string) $t);
                $tRaw = $this->stripArabicDiacritics($tClean);
                if ($tRaw === $raw && mb_strlen($tRaw, 'UTF-8') >= 2 && ! $this->isNavToken($tRaw)) {
                    $sentence = $this->findExampleSentence($texts, $raw);

                    return [
                        'word' => $tClean,
                        'example_sentence' => $sentence,
                    ];
                }
            }
        }

        // Pattern B: Look for prompt cues like "الكلمة الأولى هي X - رددوا : X" or "رددوا : X" or "هذه X. رددوا: X"
        foreach ($texts as $t) {
            // Regex for "الكلمة ... هي (Word)"
            if (preg_match('/(?:الكلمة\s+(?:الأولى|الثانية|الثالثة|الرابعة|الخامسة|الموالية|التالية)\s+هي\s+)([^\.\-؛:،]+)/u', $t, $m)) {
                $word = $this->cleanArabicBoundary((string) $m[1]);
                $raw = $this->stripArabicDiacritics($word);
                if (mb_strlen($raw, 'UTF-8') >= 2 && ! $this->isNavToken($raw)) {
                    $sentence = $this->findExampleSentence($texts, $raw);

                    return [
                        'word' => $word,
                        'example_sentence' => $sentence,
                    ];
                }
            }

            // Regex for "رددوا\s*[:\s]+([^\.\-؛:،]+)"
            if (preg_match('/(?:رددوا|رَدِّدوا|ردد)\s*[:\s]+([^\.\-؛:،]+)/u', $t, $m)) {
                $candidate = $this->cleanArabicBoundary((string) $m[1]);
                $candRaw = $this->stripArabicDiacritics($candidate);
                if (count(explode(' ', $candRaw)) <= 2 && mb_strlen($candRaw, 'UTF-8') >= 2 && ! $this->isNavToken($candRaw)) {
                    $sentence = $this->findExampleSentence($texts, $candRaw);

                    return [
                        'word' => $candidate,
                        'example_sentence' => $sentence,
                    ];
                }
            }

            // Regex for "هذه (Word) ــــ (Word)"
            if (preg_match('/^هذه\s+([^\.\-؛:،]+)/u', $t, $m)) {
                $candidate = $this->cleanArabicBoundary((string) $m[1]);
                $candRaw = $this->stripArabicDiacritics($candidate);
                if (count(explode(' ', $candRaw)) <= 2 && mb_strlen($candRaw, 'UTF-8') >= 2 && ! $this->isNavToken($candRaw)) {
                    $sentence = $this->findExampleSentence($texts, $candRaw);

                    return [
                        'word' => $candidate,
                        'example_sentence' => $sentence,
                    ];
                }
            }
        }

        return null;
    }

    protected function isNavToken(string $rawText): bool
    {
        $normalized = $this->stripArabicDiacritics($rawText);
        if (in_array($normalized, ['معي', 'معا', 'جماعة', 'الكلمة', 'الكلمات', 'الجملة', 'الجمل', 'النص', 'الفقرة'], true)) {
            return true;
        }

        foreach (self::NAV_TOKENS as $nav) {
            $navNorm = $this->stripArabicDiacritics($nav);
            if ($normalized === $navNorm || str_starts_with($normalized, $navNorm . ' ') || str_ends_with($normalized, ' ' . $navNorm)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find an example sentence for the vocabulary word within the slide text.
     */
    protected function findExampleSentence(array $texts, string $rawWord): ?string
    {
        foreach ($texts as $text) {
            $t = trim($text);
            $raw = $this->stripArabicDiacritics($t);
            $wordCount = count(explode(' ', $raw));

            if ($wordCount >= 3 && $wordCount <= 20 && str_contains($raw, $rawWord)) {
                // Filter out teacher instructional phrases
                if (! $this->containsAny($raw, ['انتبهوا', 'خذوا', 'سأقرأ', 'ينطق الأستاذ', 'ارفعوا الألواح', 'صححوا', 'شروط الحصول', 'افتتاح الحصة', 'اختتام الحصة'])) {
                    return $t;
                }
            }
        }

        return null;
    }

    /**
     * Strip Arabic vowels and diacritics.
     */
    public function stripArabicDiacritics(string $text): string
    {
        $clean = preg_replace(self::ARABIC_DIACRITICS_REGEX, '', $text) ?? $text;
        $clean = str_replace(['أ', 'إ', 'آ'], 'ا', $clean);
        $clean = str_replace(['ى'], 'ي', $clean);
        $clean = str_replace(['ة'], 'ه', $clean);
        $clean = preg_replace('/\s+/u', ' ', $clean) ?? $clean;

        return trim($clean);
    }

    /**
     * Extract slide text from slide XML.
     */
    protected function extractSlideTexts(ZipArchive $zip, string $slidePath): array
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
            foreach ($paragraphs as $p) {
                $nodes = $p->xpath('.//a:t') ?: [];
                $pTexts = [];
                foreach ($nodes as $node) {
                    $val = (string) $node;
                    if ($val !== '') {
                        $pTexts[] = $val;
                    }
                }
                $joined = trim(implode('', $pTexts));
                if ($joined !== '') {
                    $texts[] = $joined;
                }
            }
        }

        return array_values(array_unique($texts));
    }

    /**
     * Extract the main image on the slide.
     */
    protected function extractSlideImage(ZipArchive $zip, string $slidePath, string $assetsDir, array &$dedupe): ?string
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
        foreach ($nodes as $node) {
            $type = (string) $node['Type'];
            $target = (string) $node['Target'];
            $id = (string) $node['Id'];

            if ($id !== '' && $target !== '' && str_contains($type, '/image')) {
                $imageRels[$id] = $this->resolveRelativePath(dirname($slidePath), $target);
            }
        }

        if ($imageRels === []) {
            return null;
        }

        // Rank pictures by bounding area on the slide
        $slideXml = $zip->getFromName($slidePath);
        $rankedIds = [];

        if (is_string($slideXml) && $slideXml !== '') {
            $slideDoc = $this->parseXml($slideXml);
            if ($slideDoc !== null) {
                $slideDoc->registerXPathNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
                $slideDoc->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
                $slideDoc->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

                $pics = $slideDoc->xpath('//p:pic') ?: [];
                $candidates = [];

                foreach ($pics as $pic) {
                    $blips = $pic->xpath('.//a:blip') ?: [];
                    if ($blips === []) {
                        continue;
                    }

                    $attrs = $blips[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                    $rid = isset($attrs['embed']) ? (string) $attrs['embed'] : '';
                    if ($rid === '' || ! array_key_exists($rid, $imageRels)) {
                        continue;
                    }

                    $area = 0;
                    $extNodes = $pic->xpath('.//a:xfrm/a:ext') ?: [];
                    if ($extNodes !== []) {
                        $cx = (int) ($extNodes[0]['cx'] ?? 0);
                        $cy = (int) ($extNodes[0]['cy'] ?? 0);
                        $area = $cx * $cy;
                    }

                    $candidates[] = [
                        'rid' => $rid,
                        'area' => $area,
                    ];
                }

                if ($candidates !== []) {
                    usort($candidates, static fn ($a, $b) => ($b['area'] ?? 0) <=> ($a['area'] ?? 0));
                    foreach ($candidates as $cand) {
                        $rid = (string) $cand['rid'];
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
            $fileName = $this->uniqueFileName($assetsDir, $baseName, $blob);
            file_put_contents($assetsDir . DIRECTORY_SEPARATOR . $fileName, $blob);

            $dedupe[$hash] = $fileName;

            return $fileName;
        }

        return null;
    }

    /**
     * Resolve presentation path by lesson ID or full path.
     */
    protected function resolvePresentationFilePath(string $lessonIdOrPath): ?string
    {
        if (is_file($lessonIdOrPath)) {
            return $lessonIdOrPath;
        }

        $lessonId = trim($lessonIdOrPath);
        $arRoot = rtrim((string) config('raiida.files_root', base_path('files')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'AR';

        $extensions = ['.pptx', '.ppsx', '.ppt'];
        foreach ($extensions as $ext) {
            $candidateName = $lessonId . $ext;
            $matches = $this->findFileRecursive($arRoot, $candidateName);
            if ($matches !== []) {
                return $matches[0];
            }
        }

        return null;
    }

    /**
     * Parse Grade, Period, Week from filename or path.
     */
    protected function parseLessonMetadata(string $lessonId, string $filePath): array
    {
        $grade = 'N1';
        $period = 'P1';
        $week = 'SEM1';

        if (preg_match('/_(N[1-6])_/i', $lessonId, $m)) {
            $grade = strtoupper($m[1]);
        } elseif (preg_match('/niveau_([1-6])/i', $filePath, $m)) {
            $grade = 'N' . $m[1];
        }

        if (preg_match('/_(P[1-5])_/i', $lessonId, $m)) {
            $period = strtoupper($m[1]);
        } elseif (preg_match('/periode_([1-5])/i', $filePath, $m)) {
            $period = 'P' . $m[1];
        }

        if (preg_match('/_(SEM[1-6])/i', $lessonId, $m)) {
            $week = strtoupper($m[1]);
        } elseif (preg_match('/semaine_([1-6])/i', $filePath, $m)) {
            $week = 'SEM' . $m[1];
        }

        return [$grade, $period, $week];
    }

    protected function collectArabicFiles(string $arRoot, ?string $grade, ?string $period, ?string $week, ?string $lessonId): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($arRoot, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $ext = strtolower($item->getExtension());
                $filename = $item->getFilename();

                if (in_array($ext, ['pptx', 'ppsx'], true) && ! str_starts_with($filename, '~$')) {
                    $path = $item->getPathname();

                    if ($grade !== null) {
                        $gradeNum = ltrim($grade, 'Nn');
                        if (! str_contains($path, "niveau_{$gradeNum}") && ! str_contains($filename, "_N{$gradeNum}_")) {
                            continue;
                        }
                    }

                    if ($period !== null) {
                        $pNum = ltrim($period, 'Pp');
                        if (! str_contains($path, "periode_{$pNum}") && ! str_contains($filename, "_P{$pNum}_")) {
                            continue;
                        }
                    }

                    if ($week !== null) {
                        $wNum = preg_replace('/\D/', '', $week);
                        if (! str_contains($path, "semaine_{$wNum}") && ! str_contains($filename, "_SEM{$wNum}_")) {
                            continue;
                        }
                    }

                    if ($lessonId !== null && $lessonId !== '') {
                        if (! str_contains($filename, $lessonId)) {
                            continue;
                        }
                    }

                    $files[] = $path;
                }
            }
        }

        sort($files);

        return $files;
    }

    protected function findFileRecursive(string $dir, string $filename): array
    {
        $matches = [];
        if (! is_dir($dir)) {
            return $matches;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strcasecmp($file->getFilename(), $filename) === 0) {
                $matches[] = $file->getPathname();
            }
        }

        return $matches;
    }

    protected function slidePaths(ZipArchive $zip): array
    {
        $paths = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (is_string($name) && preg_match('#^ppt/slides/slide\d+\.xml$#', $name)) {
                $paths[] = $name;
            }
        }

        usort($paths, static function ($a, $b) {
            preg_match('/slide(\d+)\.xml$/', $a, $ma);
            preg_match('/slide(\d+)\.xml$/', $b, $mb);

            return ((int) ($ma[1] ?? 0)) <=> ((int) ($mb[1] ?? 0));
        });

        return $paths;
    }

    protected function parseXml(string $xml): ?SimpleXMLElement
    {
        $prev = libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return $parsed instanceof SimpleXMLElement ? $parsed : null;
    }

    protected function resolveRelativePath(string $baseDir, string $target): string
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

    protected function uniqueFileName(string $dir, string $baseName, string $blob): string
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

    protected function lessonAssetsDir(string $lessonId): string
    {
        return public_path('vocab_assets' . DIRECTORY_SEPARATOR . 'ar' . DIRECTORY_SEPARATOR . $lessonId);
    }

    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function deleteDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $files = scandir($path);
        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $cur = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($cur)) {
                    $this->deleteDirectory($cur);
                } else {
                    @unlink($cur);
                }
            }
        }

        @rmdir($path);
    }
}
