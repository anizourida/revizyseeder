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
    protected const ARABIC_DIACRITICS_REGEX = '/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}\x{0640}]/u';

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
                $savedItem = ArabicVocabularyItem::query()->updateOrCreate(
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

                if (! empty($item['linked_sentences'])) {
                    foreach ($item['linked_sentences'] as $linkedSent) {
                        try {
                            \Illuminate\Support\Facades\DB::table('vocabulary_sentences')->updateOrInsert(
                                [
                                    'word' => $item['word'],
                                    'sentence' => $linkedSent,
                                    'lesson_id' => $lessonId,
                                    'grade' => $grade,
                                ],
                                [
                                    'vocabulary_item_id' => $savedItem->id,
                                    'base_word' => $item['raw_word'],
                                    'subject' => 'AR',
                                    'period' => $period,
                                    'week' => $week,
                                    'source_type' => 'slide_sentence',
                                    'image_path' => $item['image_path'] ?? null,
                                    'updated_at' => now(),
                                    'created_at' => now(),
                                ]
                            );
                        } catch (\Throwable $e) {
                            // Suppress non-critical sentence insertion collisions
                        }
                    }
                }
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
        'جميعا', 'جميعاً', 'معا', 'معاً', 'معي', 'رددوا معي', 'رددوا', 'ينطق الأستاذ',
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

        // In N1 and N2 (including combined levels like N1&2), weekly vocabulary words are exclusively taught in Session 1 (S1).
        // Sessions S2-S6 cover story characters, narrative structure, phonics drills, and evaluation.
        if (preg_match('/_S([2-6])\b/i', $lessonId)) {
            $isN1orN2 = preg_match('/N[12](?:[&_et]+[12])?/i', $grade) || preg_match('/_N[12](?:[&_et]+[12])?_/i', $lessonId);
            if ($isN1orN2) {
                return [];
            }
        }

        // Count media frequencies across all slides to distinguish unique content images from recurrent template chrome/mascots
        $mediaFrequency = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $entryName = $stat['name'] ?? '';
            if (str_starts_with($entryName, 'ppt/slides/_rels/') && str_ends_with($entryName, '.rels')) {
                $relsXml = $zip->getFromName($entryName);
                if (is_string($relsXml)) {
                    if (preg_match_all('/Target="([^"]*media[^"]*)"/i', $relsXml, $m)) {
                        foreach ($m[1] as $target) {
                            $base = basename($target);
                            $mediaFrequency[$base] = ($mediaFrequency[$base] ?? 0) + 1;
                        }
                    }
                }
            }
        }

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

            // A: Check for المعجم المساعد / مُعْجَمي (reading text glossaries)
            $mojamiItems = $this->extractMojamiItems($texts, $slideNum);
            if (! empty($mojamiItems)) {
                $imageName = $this->extractSlideImage($zip, $slidePath, $assetsDir, $dedupeImages, $mediaFrequency);
                $imagePath = $imageName ? 'vocab_assets/ar/' . $lessonId . '/' . $imageName : null;
                foreach ($mojamiItems as $mItem) {
                    $raw = $this->stripArabicDiacritics($mItem['word']);
                    if ($raw === '' || mb_strlen($raw, 'UTF-8') < 2 || $this->isNavToken($raw)) {
                        continue;
                    }
                    if (! isset($seenWords[$raw])) {
                        $seenWords[$raw] = count($extracted);
                        $extracted[] = [
                            'word' => $mItem['word'],
                            'raw_word' => $raw,
                            'example_sentence' => $mItem['meaning'],
                            'strategy' => 'المعجم المساعد',
                            'slide_index' => $slideNum,
                            'image_path' => $imagePath,
                        ];
                    }
                }
                continue;
            }

            // B: Check for شبكة المفردات (Word Network)
            $networkItem = $this->extractWordNetworkItem($texts, $slideNum);
            if ($networkItem !== null) {
                $raw = $this->stripArabicDiacritics($networkItem['word']);
                if ($raw !== '' && mb_strlen($raw, 'UTF-8') >= 2 && ! $this->isNavToken($raw)) {
                    $imageName = $this->extractSlideImage($zip, $slidePath, $assetsDir, $dedupeImages, $mediaFrequency);
                    $imagePath = $imageName ? 'vocab_assets/ar/' . $lessonId . '/' . $imageName : null;
                    if (! isset($seenWords[$raw])) {
                        $seenWords[$raw] = count($extracted);
                        $extracted[] = [
                            'word' => $networkItem['word'],
                            'raw_word' => $raw,
                            'example_sentence' => $networkItem['example_sentence'],
                            'strategy' => 'شبكة المفردات',
                            'slide_index' => $slideNum,
                            'image_path' => $imagePath,
                        ];
                    } else {
                        $eIdx = $seenWords[$raw];
                        if (empty($extracted[$eIdx]['example_sentence']) && ! empty($networkItem['example_sentence'])) {
                            $extracted[$eIdx]['example_sentence'] = $networkItem['example_sentence'];
                        }
                        if (empty($extracted[$eIdx]['image_path']) && $imagePath !== null) {
                            $extracted[$eIdx]['image_path'] = $imagePath;
                        }
                    }
                }
                continue;
            }

            // C: Check for خريطة الكلمة (Word Map)
            $mapItem = $this->extractWordMapItem($texts, $slideNum);
            if ($mapItem !== null) {
                $raw = $this->stripArabicDiacritics($mapItem['word']);
                if ($raw !== '' && mb_strlen($raw, 'UTF-8') >= 2 && ! $this->isNavToken($raw)) {
                    $imageName = $this->extractSlideImage($zip, $slidePath, $assetsDir, $dedupeImages, $mediaFrequency);
                    $imagePath = $imageName ? 'vocab_assets/ar/' . $lessonId . '/' . $imageName : null;
                    if (! isset($seenWords[$raw])) {
                        $seenWords[$raw] = count($extracted);
                        $extracted[] = [
                            'word' => $mapItem['word'],
                            'raw_word' => $raw,
                            'example_sentence' => $mapItem['example_sentence'],
                            'strategy' => 'خريطة الكلمة',
                            'slide_index' => $slideNum,
                            'image_path' => $imagePath,
                        ];
                    } else {
                        $eIdx = $seenWords[$raw];
                        if (empty($extracted[$eIdx]['example_sentence']) && ! empty($mapItem['example_sentence'])) {
                            $extracted[$eIdx]['example_sentence'] = $mapItem['example_sentence'];
                        }
                    }
                }
                continue;
            }

            // D: Dedicated flashcard / word slide
            $detectedItem = $this->detectVocabularySlide($texts, $announcedWords);
            if ($detectedItem !== null) {
                $word = $detectedItem['word'];
                $rawWord = $this->stripArabicDiacritics($word);

                if ($rawWord === '' || mb_strlen($rawWord, 'UTF-8') < 2 || $this->isNavToken($rawWord)) {
                    continue;
                }

                $imageName = $this->extractSlideImage($zip, $slidePath, $assetsDir, $dedupeImages, $mediaFrequency);
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
                    'strategy' => $activeStrategy ?? 'معجم مصور',
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
                    'strategy' => $activeStrategy ?? 'المفردات',
                    'slide_index' => null,
                    'image_path' => null,
                ];
            }
        }

        // Pass 4: Extract practice sentences across all slides and link to vocabulary words
        $lessonSentences = $this->extractLessonPracticeSentences($zip, $slides);
        foreach ($extracted as $i => $item) {
            $matchedSentences = $this->findMatchingSentencesForWord($item['raw_word'], $lessonSentences);
            if (! empty($matchedSentences)) {
                $currSent = $item['example_sentence'] ?? '';
                $isGenericPrompt = str_contains($currSent, 'رددوا :') || str_contains($currSent, 'رَدِّدوا :') || str_contains($currSent, 'رَدِّدوا');
                if (empty($currSent) || $isGenericPrompt || in_array($item['strategy'], ['معجم مصور', 'المفردات'], true)) {
                    $extracted[$i]['example_sentence'] = $matchedSentences[0];
                }
                $extracted[$i]['linked_sentences'] = $matchedSentences;
            }
        }

        return $extracted;
    }

    /**
     * Check if slide is a vocabulary banner / list slide.
     */
    protected function isValidVocabularyWord(string $word): bool
    {
        if (str_contains($word, '[') || str_contains($word, ']') || str_contains($word, '') || str_contains($word, '؟') || str_contains($word, '?')) {
            return false;
        }

        if (preg_match('/[\d\+\=\/]/u', $word)) {
            return false;
        }

        $raw = $this->stripArabicDiacritics($word);
        if (mb_strlen($raw, 'UTF-8') < 3 || count(explode(' ', $raw)) > 3) {
            return false;
        }

        $unAl = preg_replace('/^ال/u', '', $raw);

        $forbidden = [
            // Pedagogical stages & lesson structure
            'توقع', 'تسميع', 'تحقق', 'ملاحظه', 'فهم', 'استثمار', 'تقويم', 'دعم', 'تشخيص', 'انطلاق',
            'بناء', 'تثبيت', 'تطبيق', 'انتاج', 'قاعده', 'استنتاج', 'تذكر', 'تقديم', 'اهداف', 'هدف',
            'كفايه', 'كفايات', 'تخطيط', 'تدبير', 'توجيه', 'شرح', 'حصص', 'حصه', 'اسبوع', 'وحده',

            // Sight words (الكلمات البصرية)
            'انا', 'نحن', 'انت', 'انتما', 'انتم', 'انتن', 'هو', 'هي', 'هما', 'هم', 'هن',
            'هذا', 'هذه', 'ذلك', 'تلك', 'هولاء', 'اولئك', 'الذي', 'التي', 'الذين', 'اللواتي', 'اللاتي',
            'اسمي', 'ندي', 'مجد', 'نعم', 'لا',

            // Story elements & character labels
            'عنوان', 'حكايه', 'قصه', 'شخصيه', 'شخصيات', 'مكان', 'امكنه', 'زمان', 'ازمنه', 'حدث', 'احداث',
            'بدايه', 'تحول', 'مشكل', 'حل', 'نهايه', 'سرد', 'حوار', 'عناصر', 'بنيه', 'دودو', 'مراد', 'معلمه',
            'فراشه', 'متعلمون', 'اطفال',

            // Phonics & letter names
            'حرف', 'حروف', 'صوت', 'اصوات', 'مقطع', 'مقاطع', 'تنوين', 'مد', 'سكون', 'فتحه', 'ضمه', 'كسره', 'شده',
            'هجاء', 'تهجئه', 'وعي', 'صوتي', 'الفبائي', 'طلاقه', 'دو', 'دود', 'ديد', 'دي', 'دا',
            'الف', 'باء', 'تاء', 'ثاء', 'جيم', 'حاء', 'خاء', 'دال', 'ذال', 'راء', 'زاي', 'سين', 'شين',
            'صاد', 'ضاد', 'طاء', 'ظاء', 'عين', 'غين', 'فاء', 'قاف', 'كاف', 'لام', 'ميم', 'نون', 'هاء', 'واو', 'ياء', 'همزه',

            // Teacher commands & classroom interaction
            'جماعه', 'فردي', 'فرديا', 'ثنائي', 'ثنائيا', 'تناوب', 'بالتناوب', 'اقراوا', 'اقرؤوا', 'مثلي',
            'رددوا', 'نردد', 'تعلمنا', 'استمعوا', 'انتبهوا', 'خذوا', 'ضعوا', 'افتحوا', 'اغلقوا', 'ارفعوا',
            'اكتبوا', 'صححوا', 'انجزوا', 'شاهدوا', 'لاحظوا', 'عبروا', 'اشيروا', 'صلوا', 'احيطوا', 'لونوا',
            'رتبوا', 'اختاروا', 'اذكروا', 'تذكروا', 'مرحبا', 'احسنتم', 'ممتاز', 'جيد', 'الواح', 'كراسه',
            'دفتر البحث', 'دفاتر', 'سبوره', 'مقاعد', 'فئه', 'مجموعه',

            // Lexical & metalanguage tokens
            'كلمه', 'كلمات', 'جمله', 'جمل', 'نص', 'نصوص', 'فقره', 'فقرات', 'سؤال', 'اسئله', 'جواب', 'اجوبه',
            'معجم', 'مفردات', 'معني', 'مرادف', 'ضد', 'عائله', 'شبكه', 'خريطه', 'اشتقاق', 'مساعد', 'استراتيجيه',
            'مفتاح', 'مفاتيح', 'انشوده', 'نشيد', 'تمرين', 'تفريغ'
        ];

        foreach ($forbidden as $f) {
            if ($raw === $f || $unAl === $f || str_starts_with($raw, $f . ' ') || str_starts_with($unAl, $f . ' ')) {
                return false;
            }
        }

        return ! $this->isNavToken($raw);
    }

    protected function isVocabularyHeaderSlide(array $texts): bool
    {
        $full = implode(' ', $texts);
        $fullRaw = $this->stripArabicDiacritics($full);

        // Skip static overview / schedule / phonics / song slides
        if ($this->containsAny($fullRaw, [
            'تنظيم حصص', 'هيكله حصه', 'شروط الحصول', 'عند نهايه الحصة', 'عند نهايه الحصه',
            'توجيهات و شرح', 'كلمات بصريه', 'الصوت [', 'حرف ', 'انشوده الحروف', 'نشيد'
        ])) {
            return false;
        }

        // Must not be sight words slide (e.g. أنا – اسمي – ندى – مجد)
        if (str_contains($fullRaw, 'انا') && str_contains($fullRaw, 'اسمي') && str_contains($fullRaw, 'ندي')) {
            return false;
        }

        // Find a text line containing at least 3 distinct valid words separated by dashes
        foreach ($texts as $t) {
            $rawT = $this->stripArabicDiacritics($t);
            if (in_array($rawT, ['معجم', 'المعجم', 'نشاط اعتيادي', 'استماع وتحدث', 'اختتام الحصة', 'قراءة كتابة', 'قراءة/ كتابة'])) {
                continue;
            }

            $parts = preg_split('/[–—\-]+|\x{0640}{3,}/u', $t);
            $validParts = 0;
            foreach ($parts as $p) {
                $cp = trim(preg_replace('/^(?:معجم|المعجم|مرافق المدرسة|معجم المدرسة|الأنشطة المدرسية|المدرسة والأدوات المدرسية|المدرسة|ورشة المعجم)[^:]*:\s*/u', '', $p));
                $rawP = $this->stripArabicDiacritics($cp);
                if ($this->isValidVocabularyWord($rawP)) {
                    $validParts++;
                }
            }
            if ($validParts >= 3) {
                return true;
            }
        }

        return false;
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
                if ($cleanedWord !== '' && $this->isValidVocabularyWord($cleanedWord)) {
                    $words[] = $cleanedWord;
                }
            }
        }

        return array_values(array_unique($words));
    }

    /**
     * Extract word tokens from a vocabulary header slide.
     */
    protected function extractWordsFromHeaderSlide(array $texts): array
    {
        $words = [];

        foreach ($texts as $t) {
            $rawT = $this->stripArabicDiacritics($t);
            if (in_array($rawT, ['معجم', 'المعجم', 'نشاط اعتيادي', 'استماع وتحدث', 'اختتام الحصة', 'قراءة كتابة', 'قراءة/ كتابة'])) {
                continue;
            }

            $parts = preg_split('/[–—\-]+|\x{0640}{3,}/u', $t);
            $candidateWords = [];
            foreach ($parts as $part) {
                $p = $this->cleanArabicBoundary((string) $part);
                $p = preg_replace('/^(?:معجم|المعجم|معــــــــــجم|مـــعــجـــم|مفردات|الأسرة والعائلة|المدرسة والأدوات المدرسية|معجم المدرسة والأدوات المدرسية|معجم المدرسة|مرافق المدرسة|معجم مرافق المدرسة|معجم الأنشطة المدرسية|الأنشطة المدرسية)[^:]*:\s*/u', '', $p);
                $p = preg_replace('/\s*(?:معجم|المعجم|معــــــــــجم|مـــعــجـــم|مفردات)$/u', '', $p);
                $p = $this->cleanArabicBoundary($p);

                if ($this->isValidVocabularyWord($p)) {
                    $candidateWords[] = $p;
                }
            }

            if (count($candidateWords) >= 3) {
                foreach ($candidateWords as $cw) {
                    $words[] = $cw;
                }
            }
        }

        return array_values(array_unique($words));
    }

    protected function cleanArabicBoundary(string $text): string
    {
        $cleaned = preg_replace('/^[\s:؛\.\-–—ـ\r\n\t]+|[\s:؛\.\-–—ـ\r\n\t]+$/u', '', $text) ?? $text;
        $cleaned = preg_replace('/[\s\.\d]+$/u', '', $cleaned) ?? $cleaned;

        return trim($cleaned);
    }


    /**
     * Extract glossary items from المعجم المساعد or مُعْجَمي slides (Reading text vocabulary).
     */
    protected function extractMojamiItems(array $texts, int $slideNum): array
    {
        $full = implode(' ', $texts);
        if (! (str_contains($full, 'مُعْجَمي') || str_contains($full, 'معجمي') || str_contains($full, 'المعجم المساعد'))) {
            return [];
        }

        // Avoid pure nav slides
        if (count($texts) <= 5 && $this->containsAny($full, ['تنظيم حصص', 'هيكلة حصة'])) {
            return [];
        }

        $items = [];
        $cleaned = preg_replace('/(?:مُعْجَمي|معجمي|المعجم المساعد)\s*:\s*/u', '', $full);
        preg_match_all('/(?:^|[\-\.؛،\r\n])\s*([^\s:\-\.،]+(?:\s+[^\s:\-\.،]+)?)\s*:\s*([^:\-\.؛\r\n]+)/u', $cleaned, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $w = $this->cleanArabicBoundary(trim($m[1], "- \t\n\r\0\x0B"));
            $meaning = $this->cleanArabicBoundary(trim($m[2], "- \t\n\r\0\x0B"));
            $wRaw = $this->stripArabicDiacritics($w);

            if ($wRaw === '' || mb_strlen($wRaw, 'UTF-8') < 2 || $this->isNavToken($wRaw)) {
                continue;
            }

            if ($this->containsAny($wRaw, ['حصة', 'نشاط', 'دفاتر', 'كراسة', 'استماع', 'قراءة'])) {
                continue;
            }

            if (count(explode(' ', $wRaw)) <= 3 && mb_strlen($meaning, 'UTF-8') >= 2) {
                $items[] = [
                    'word' => $w,
                    'meaning' => $meaning,
                ];
            }
        }

        return $items;
    }

    /**
     * Extract target word and network words from شبكة المفردات slides.
     */
    protected function extractWordNetworkItem(array $texts, int $slideNum): ?array
    {
        $full = implode(' ', $texts);
        if (! (str_contains($full, 'شبكة المفردات') || str_contains($full, 'شبكة الكلمة') || str_contains($full, 'شَبَكَةِ كَلِمَةِ') || str_contains($full, 'شبكة كلمة') || str_contains($full, 'شبكة'))) {
            return null;
        }

        $targetWord = null;
        if (preg_match('/(?:لِشَبَكَةِ|شَبَكَةِ|شبكة)\s+(?:كَلِمَةِ|كلمة)?\s*["«\'\s]*([^"»\'\s\.\-–]+)["»\']?/u', $full, $m)) {
            $candidate = $this->cleanArabicBoundary($m[1]);
            if ($this->isValidVocabularyWord($candidate)) {
                $targetWord = $candidate;
            }
        }

        if ($targetWord === null && preg_match('/شبكة[^\"]*\"([^\"]+)\"/u', $full, $m)) {
            $candidate = $this->cleanArabicBoundary($m[1]);
            if ($this->isValidVocabularyWord($candidate)) {
                $targetWord = $candidate;
            }
        }

        if ($targetWord === null) {
            return null;
        }

        // Collect other non-nav words as the network
        $networkWords = [];
        $targetRaw = $this->stripArabicDiacritics($targetWord);
        foreach ($texts as $t) {
            $cleanedT = $this->cleanArabicBoundary($t);
            $rawT = $this->stripArabicDiacritics($cleanedT);
            if ($rawT === '' || $rawT === $targetRaw || $this->isNavToken($rawT)) {
                continue;
            }
            if ($this->containsAny($rawT, ['شبكة', 'تمرين', 'نموذج', 'يقول', 'كلمة', 'مفردات', 'نصحح', 'يقرا', 'يقرأ'])) {
                continue;
            }
            if (count(explode(' ', $rawT)) <= 2 && mb_strlen($rawT, 'UTF-8') >= 2) {
                $networkWords[] = $cleanedT;
            }
        }

        $exampleSentence = ! empty($networkWords)
            ? 'شبكة المفردات: ' . implode('، ', array_unique($networkWords))
            : null;

        return [
            'word' => $targetWord,
            'example_sentence' => $exampleSentence,
        ];
    }

    /**
     * Extract target word from خريطة الكلمة slides.
     */
    protected function extractWordMapItem(array $texts, int $slideNum): ?array
    {
        $full = implode(' ', $texts);
        if (! (str_contains($full, 'خريطة الكلمة') || str_contains($full, 'خريطة كلمة') || str_contains($full, 'خَريطَةُ') || str_contains($full, 'خريطة'))) {
            return null;
        }

        $targetWord = null;
        if (preg_match('/(?:خَريطَةُ|خريطة)\s+(?:كَلِمَةِ|كلمة)?\s*["«\'\s]*([^"»\'\s\.\-–]+)["»\']?/u', $full, $m)) {
            $candidate = $this->cleanArabicBoundary($m[1]);
            if ($this->isValidVocabularyWord($candidate)) {
                $targetWord = $candidate;
            }
        }

        if ($targetWord === null && preg_match('/خريطة[^\"]*\"([^\"]+)\"/u', $full, $m)) {
            $candidate = $this->cleanArabicBoundary($m[1]);
            if ($this->isValidVocabularyWord($candidate)) {
                $targetWord = $candidate;
            }
        }

        if ($targetWord === null) {
            return null;
        }

        return [
            'word' => $targetWord,
            'example_sentence' => 'خريطة الكلمة (النوع، المرادف، الضد، الجملة)',
        ];
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
                        'word' => (mb_strlen($tClean) >= mb_strlen($vocalized)) ? $tClean : $vocalized,
                        'example_sentence' => $sentence,
                    ];
                }
            }
        }

        // If explicit announced words list was found, do not add loose heuristic matches
        if ($announcedWords !== []) {
            return null;
        }

        // Only allow heuristic extraction if the slide explicitly contains an authentic vocabulary banner (excluding solitary footer navbar tokens)
        $hasExplicitVocabBanner = false;
        foreach ($texts as $text) {
            $t = $this->stripArabicDiacritics(trim($text));
            if (in_array($t, ['معجم', 'المعجم', 'معــــــــــجم', 'مـــعــجـــم', 'مفردات'], true)) {
                continue;
            }
            if (str_starts_with($t, 'معجم ') || str_starts_with($t, 'المعجم ') || str_starts_with($t, 'مفردات ') || str_contains($t, 'ورشة المعجم')) {
                $hasExplicitVocabBanner = true;
                break;
            }
        }
        if (! $hasExplicitVocabBanner) {
            return null;
        }

        // Pattern B: Look for prompt cues like "الكلمة الأولى هي X - رددوا : X" or "رددوا : X" or "هذه X. رددوا: X"
        foreach ($texts as $t) {
            // Regex for "الكلمة ... هي (Word)"
            if (preg_match('/(?:الكلمة\s+(?:الأولى|الثانية|الثالثة|الرابعة|الخامسة|الموالية|التالية)\s+هي\s+)([^\.\-؛:،]+)/u', $t, $m)) {
                $word = $this->cleanArabicBoundary((string) $m[1]);
                if ($this->isValidVocabularyWord($word)) {
                    $raw = $this->stripArabicDiacritics($word);
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
                if ($this->isValidVocabularyWord($candidate)) {
                    $candRaw = $this->stripArabicDiacritics($candidate);
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
                if ($this->isValidVocabularyWord($candidate)) {
                    $candRaw = $this->stripArabicDiacritics($candidate);
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
        if (str_starts_with($normalized, 'حرف ') || str_starts_with($normalized, 'نردد ') || str_starts_with($normalized, 'تعلمنا ') || str_starts_with($normalized, 'انشوده') || str_starts_with($normalized, 'انشودة') || str_starts_with($normalized, 'نشيد') || str_starts_with($normalized, 'تفريغ') || str_starts_with($normalized, 'المقاعد')) {
            return true;
        }

        if (in_array($normalized, ['معي', 'معا', 'جماعة', 'الكلمة', 'الكلمات', 'الجملة', 'الجمل', 'النص', 'الفقرة', 'جميعا', 'جميع', 'كل', 'بعدي', 'تفريغ', 'التفريغ', 'انت', 'أنت', 'هذا دودو', 'ما رايك', 'تمرينات', 'النشيد', 'معي؟', '2+1'], true)) {
            return true;
        }

        $wCount = count(explode(' ', $normalized));
        foreach (self::NAV_TOKENS as $nav) {
            $navNorm = $this->stripArabicDiacritics($nav);
            if ($normalized === $navNorm) {
                return true;
            }
            if ($wCount <= 3 && (str_starts_with($normalized, $navNorm . ' ') || str_ends_with($normalized, ' ' . $navNorm))) {
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
     * Extract practice and application sentences across all slides of the lesson.
     *
     * @return array<int, array{sentence: string, raw: string, slide_index: int}>
     */
    /**
     * Extract practice and application sentences across all slides of the lesson.
     *
     * @return array<int, array{sentence: string, raw: string, slide_index: int}>
     */
    protected function extractLessonPracticeSentences(ZipArchive $zip, array $slides): array
    {
        $sentences = [];

        foreach ($slides as $idx => $slidePath) {
            $slideNum = $idx + 1;
            $texts = $this->extractSlideTexts($zip, $slidePath);
            if ($texts === []) {
                continue;
            }

            foreach ($texts as $t) {
                $rawT = $this->stripArabicDiacritics($t);
                if ($this->isNavToken($rawT)) {
                    continue;
                }

                $candidates = [];

                // Pattern 1: Prompt target repetition: 'لديَّ محفظة. رددوا: لديَّ محفظة.'
                if (preg_match('/(?:رددوا|رَدِّدوا|ردد)\s*[:\s]+([^.،؛\-\x{2013}\x{2014}\x{0640}\r\n\?؟]+)/u', $t, $m)) {
                    $candidates[] = $m[1];
                }

                // Pattern 2: Before prompt: 'الدفتر والمقلمة والأقلام الملونة أدوات مدرسية – رددوا'
                if (preg_match('/([^.،؛?؟\-\x{2013}\x{2014}\x{0640}\r\n]{5,})\s*(?:[\-\x{2013}\x{2014}\x{0640}.]+|\s+)\s*(?:رددوا|رَدِّدوا|ردد)/u', $t, $m)) {
                    $candidates[] = $m[1];
                }

                // Pattern 3: Demonstrative application: 'هذه مقلمة' or 'هذا دفتر'
                if (preg_match('/^(?:هذه|هذا)\s+([^.،؛?؟\-\x{2013}\x{2014}\x{0640}\r\n]{3,})/u', $t, $m)) {
                    $candidates[] = $m[0];
                }

                foreach ($candidates as $cand) {
                    $c = $this->cleanArabicBoundary((string) $cand);
                    $c = preg_replace('/^(?:معا|جميعا|الآن|نردد معا|رددوا|رَدِّدوا)\s*[:\s]*/u', '', $c);
                    $c = $this->cleanArabicBoundary($c);
                    $raw = $this->stripArabicDiacritics($c);

                    // Discard if contains list dashes
                    if (str_contains($c, '–') || str_contains($c, '—') || str_contains($c, 'ــــ')) {
                        continue;
                    }

                    // Discard story titles, comprehension expectations, and teacher directives
                    $forbidden = [
                        'عنوان', 'حكاية', 'حكايتنا', 'اول يوم في المدرسه', 'اري في الصوره', 'اتوقع', 'توقع',
                        'تسميع', 'تحقق', 'للتحدث عن', 'سوف نتعلم', 'ستتعلمون', 'سنتعلم', 'استراحه', 'سنردد',
                        'نردد معا', 'ضعوا', 'خذوا', 'الكراسه', 'اللوحه', 'اشير', 'تقول', 'ثنائيات', 'اشاره',
                        'حرف', 'انشوده', 'نشيد', 'المقاطع', 'شخصيه', 'شخصيات', 'الفقره', 'تمرين', 'صوت',
                        'مجموعه من الكلمات'
                    ];
                    if ($this->containsAny($raw, $forbidden)) {
                        continue;
                    }

                    $cleanWords = preg_split('/[\s\.\-،؛:]+/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
                    $uniqueWords = array_unique($cleanWords ?: []);
                    if (count($uniqueWords) <= 1) {
                        continue;
                    }

                    $wordCount = count($cleanWords);
                    if ($wordCount >= 2 && $wordCount <= 12) {
                        $sentences[$raw] = [
                            'sentence' => $c,
                            'raw' => $raw,
                            'slide_index' => $slideNum,
                        ];
                    }
                }
            }
        }

        return array_values($sentences);
    }

    /**
     * Find best matching practice sentences for a specific vocabulary word.
     *
     * @param  array<int, array{sentence: string, raw: string, slide_index: int}>  $sentences
     * @return array<int, string>
     */
    protected function findMatchingSentencesForWord(string $rawWord, array $sentences): array
    {
        $rawWord = $this->stripArabicDiacritics($rawWord);
        $targetTokens = preg_split('/[\s\.\-،؛:–—ـ!?؟\(\)]+/u', $rawWord, -1, PREG_SPLIT_NO_EMPTY);
        $targetCleanTokens = array_map(fn ($w) => preg_replace('/^(?:وال|بال|فال|كال|لل|ال|[وبفكل])/u', '', $w), $targetTokens);

        if ($targetCleanTokens === []) {
            return [];
        }

        $matches = [];
        foreach ($sentences as $s) {
            $sRaw = $s['raw'];
            $sText = $s['sentence'];

            $sentTokens = preg_split('/[\s\.\-،؛:–—ـ!?؟\(\)]+/u', $sRaw, -1, PREG_SPLIT_NO_EMPTY);
            $sentCleanTokens = array_map(fn ($w) => preg_replace('/^(?:وال|بال|فال|كال|لل|ال|[وبفكل])/u', '', $w), $sentTokens);

            $matched = false;
            if (count($targetCleanTokens) === 1) {
                $target = $targetCleanTokens[0];
                $isFeminine = str_ends_with($target, 'ه') || str_ends_with($target, 'ة');
                $base = $isFeminine ? mb_substr($target, 0, -1, 'UTF-8') : $target;
                $suffixes = $isFeminine
                    ? ['تي', 'تنا', 'تك', 'تها', 'تهم', 'ته']
                    : ['ي', 'نا', 'ك', 'ها', 'هم', 'ه'];

                foreach ($sentCleanTokens as $tok) {
                    if ($tok === $target) {
                        $matched = true;
                        break;
                    }
                    foreach ($suffixes as $suf) {
                        if ($tok === $base . $suf) {
                            $matched = true;
                            break 2;
                        }
                    }
                }
            } else {
                $targetJoined = implode(' ', $targetCleanTokens);
                $sentJoined = implode(' ', $sentCleanTokens);
                $matched = str_contains($sentJoined, $targetJoined);
            }

            if ($matched) {
                $matches[] = $sText;
            }
        }

        // Prioritize action/application sentences like 'لديَّ محفظة', 'في المحفظة...', 'أحمل...' over plain demonstratives
        usort($matches, function ($a, $b) {
            $rawA = $this->stripArabicDiacritics($a);
            $rawB = $this->stripArabicDiacritics($b);
            $scoreA = 0;
            $scoreB = 0;
            $verbs = ['لدي', 'في', 'علي', 'احمل', 'شاهد', 'يشاهد', 'يفوز', 'يرتدي', 'يجلس', 'هذه', 'هذا'];
            foreach ($verbs as $v) {
                if (str_starts_with($rawA, $v . ' ') || str_contains($rawA, ' ' . $v . ' ')) {
                    $weight = ($v === 'لدي') ? 25 : (($v === 'في' || $v === 'احمل') ? 20 : 10);
                    $scoreA += $weight;
                }
                if (str_starts_with($rawB, $v . ' ') || str_contains($rawB, ' ' . $v . ' ')) {
                    $weight = ($v === 'لدي') ? 25 : (($v === 'في' || $v === 'احمل') ? 20 : 10);
                    $scoreB += $weight;
                }
            }
            if ($scoreA !== $scoreB) {
                return $scoreB <=> $scoreA;
            }

            return mb_strlen($a, 'UTF-8') <=> mb_strlen($b, 'UTF-8');
        });

        return array_values(array_unique($matches));
    }

    /**
     * Strip Arabic vowels and diacritics.
     */
    public function stripArabicDiacritics(string $text): string
    {
        $clean = preg_replace(self::ARABIC_DIACRITICS_REGEX, '', $text) ?? $text;
        $clean = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $clean);
        $clean = str_replace(['ى'], 'ي', $clean);
        $clean = str_replace(['ة'], 'ه', $clean);
        $clean = str_replace(['ـ'], '', $clean);
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
    protected function extractSlideImage(
        ZipArchive $zip,
        string $slidePath,
        string $assetsDir,
        array &$dedupe,
        array $mediaFrequency = []
    ): ?string {
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

                    $resolved = $imageRels[$rid];
                    $baseName = basename($resolved);
                    $freq = $mediaFrequency[$baseName] ?? 1;

                    $ph = $pic->xpath('.//p:nvPr/p:ph') ?: [];
                    $cName = (string) ($pic->xpath('.//p:cNvPr/@name')[0] ?? '');
                    $cDescr = (string) ($pic->xpath('.//p:cNvPr/@descr')[0] ?? '');

                    $score = 0;
                    // Primary placeholder images
                    if ($ph !== [] || str_contains($cName, 'réservé') || str_contains($cName, 'Placeholder')) {
                        $score += 20000;
                    }

                    // Vocabulary illustrations are unique or near-unique (freq 1-3).
                    // Template icons/buttons/recurrent characters (like cartoon teachers) appear on 4+ slides.
                    if ($freq <= 1) {
                        $score += 15000;
                    } elseif ($freq <= 3) {
                        $score += 8000;
                    } elseif ($freq >= 10) {
                        $score -= 30000;
                    } elseif ($freq >= 4) {
                        $score -= 10000;
                    }

                    if (str_contains($cDescr, 'fourniture') || str_contains($cDescr, 'bureau') || str_contains($cDescr, 'Approvisionnement')) {
                        $score += 5000;
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
                        'score' => $score,
                        'area' => $area,
                    ];
                }

                if ($candidates !== []) {
                    usort($candidates, static function ($a, $b) {
                        if ($a['score'] !== $b['score']) {
                            return $b['score'] <=> $a['score'];
                        }

                        return ($b['area'] ?? 0) <=> ($a['area'] ?? 0);
                    });
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

        if (preg_match('/_(N[1-6](?:[&_et]+[1-6])?)_/i', $lessonId, $m)) {
            $grade = strtoupper(str_replace(['_', 'ET', 'et'], ['&', '&', '&'], $m[1]));
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

                if (in_array($ext, ['pptx', 'ppsx', 'ppt'], true) && ! str_starts_with($filename, '~$')) {
                    $path = $item->getPathname();

                    if ($grade !== null) {
                        $gradeNum = ltrim($grade, 'Nn');
                        $matchesGrade = str_contains($path, "niveau_{$gradeNum}")
                            || str_contains($filename, "_N{$gradeNum}_")
                            || str_contains($filename, "_N{$gradeNum}&")
                            || str_contains($filename, "&{$gradeNum}_")
                            || str_contains($filename, "_N{$gradeNum}et")
                            || str_contains($filename, "et{$gradeNum}_");
                        if (! $matchesGrade) {
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
