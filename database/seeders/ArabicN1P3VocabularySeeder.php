<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN1P3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N1, Period P3 (الوحدة 3: الهندام والنظافة والتغذية والصحة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: ح (الحاء)
            // ==========================================
            [
                'word' => 'حِمامٌ',
                'clean_word' => 'حِمامٌ',
                'raw_word' => 'حمام',
                'example_sentence' => 'الاغتسال بالماء والصابون للنظافة والانتعاش',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM1_S1/image71.png',
                'linked_sentences' => [
                    'الاغتسال بالماء والصابون للنظافة والانتعاش',
                ],
            ],
            [
                'word' => 'حَلِيبٌ',
                'clean_word' => 'حَلِيبٌ',
                'raw_word' => 'حليب',
                'example_sentence' => 'مشروب صحي يقوي العظام والأسنان',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM1_S1/image70.png',
                'linked_sentences' => [
                    'مشروب صحي يقوي العظام والأسنان',
                ],
            ],
            [
                'word' => 'حِذاءٌ',
                'clean_word' => 'حِذاءٌ',
                'raw_word' => 'حذاء',
                'example_sentence' => 'نعل نظيف نرتديه للمشي والمدرسة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM1_S1/image69.png',
                'linked_sentences' => [
                    'نعل نظيف نرتديه للمشي والمدرسة',
                ],
            ],
            // ==========================================
            // الأسبوع 2: ص (الصاد)
            // ==========================================
            [
                'word' => 'صابُونٌ',
                'clean_word' => 'صابُونٌ',
                'raw_word' => 'صابون',
                'example_sentence' => 'مادة رغوية لغسل اليدين والوجه',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM2_S1/image15.png',
                'linked_sentences' => [
                    'مادة رغوية لغسل اليدين والوجه',
                ],
            ],
            [
                'word' => 'صَحْنٌ',
                'clean_word' => 'صَحْنٌ',
                'raw_word' => 'صحن',
                'example_sentence' => 'إناء نضع فيه الطعام الصحي',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM2_S1/image108.png',
                'linked_sentences' => [
                    'إناء نضع فيه الطعام الصحي',
                ],
            ],
            [
                'word' => 'صَباحٌ',
                'clean_word' => 'صَباحٌ',
                'raw_word' => 'صباح',
                'example_sentence' => 'أول النهار نستيقظ فيه بنشاط',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM2_S1/image109.png',
                'linked_sentences' => [
                    'أول النهار نستيقظ فيه بنشاط',
                ],
            ],
            // ==========================================
            // الأسبوع 3: س (السين)
            // ==========================================
            [
                'word' => 'سِواكٌ',
                'clean_word' => 'سِواكٌ',
                'raw_word' => 'سواك',
                'example_sentence' => 'عود طبيعي لتنظيف الأسنان وتطييب الفم',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM3_S1/image57.png',
                'linked_sentences' => [
                    'عود طبيعي لتنظيف الأسنان وتطييب الفم',
                ],
            ],
            [
                'word' => 'سَرِيرٌ',
                'clean_word' => 'سَرِيرٌ',
                'raw_word' => 'سرير',
                'example_sentence' => 'مكان النوم المريح والراحة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM3_S1/image56.png',
                'linked_sentences' => [
                    'مكان النوم المريح والراحة',
                ],
            ],
            [
                'word' => 'سَلَّمَ',
                'clean_word' => 'سَلَّمَ',
                'raw_word' => 'سلم',
                'example_sentence' => 'حيّا أصدقاءه بأدب واحترام',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM3_S1/image55.png',
                'linked_sentences' => [
                    'حيّا أصدقاءه بأدب واحترام',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ذ (الذال)
            // ==========================================
            [
                'word' => 'ذِئْبٌ',
                'clean_word' => 'ذِئْبٌ',
                'raw_word' => 'ذئب',
                'example_sentence' => 'حيوان بري سريع ونبيه',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM4_S1/image15.png',
                'linked_sentences' => [
                    'حيوان بري سريع ونبيه',
                ],
            ],
            [
                'word' => 'ذُبابَةٌ',
                'clean_word' => 'ذُبابَةٌ',
                'raw_word' => 'ذبابة',
                'example_sentence' => 'حشرة ضارة يجب إبعادها عن الطعام',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM4_S1/image108.png',
                'linked_sentences' => [
                    'حشرة ضارة يجب إبعادها عن الطعام',
                ],
            ],
            [
                'word' => 'ذُرَةٌ',
                'clean_word' => 'ذُرَةٌ',
                'raw_word' => 'ذرة',
                'example_sentence' => 'نبات أصفر لذيذ وصحي',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM4_S1/image109.png',
                'linked_sentences' => [
                    'نبات أصفر لذيذ وصحي',
                ],
            ],
            // ==========================================
            // الأسبوع 5: ز (الزاي)
            // ==========================================
            [
                'word' => 'زَيْتُونٌ',
                'clean_word' => 'زَيْتُونٌ',
                'raw_word' => 'زيتون',
                'example_sentence' => 'ثمار مباركة ينتج منها الزيت الصحي',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM5_S1/image45.png',
                'linked_sentences' => [
                    'ثمار مباركة ينتج منها الزيت الصحي',
                ],
            ],
            [
                'word' => 'زَرافَةٌ',
                'clean_word' => 'زَرافَةٌ',
                'raw_word' => 'زرافة',
                'example_sentence' => 'حيوان طويل العنق لطيف',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM5_S1/image28.png',
                'linked_sentences' => [
                    'حيوان طويل العنق لطيف',
                ],
            ],
            [
                'word' => 'زَهْرَةٌ',
                'clean_word' => 'زَهْرَةٌ',
                'raw_word' => 'زهرة',
                'example_sentence' => 'وردة عطرة تزين الحديقة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM5_S1/image59.png',
                'linked_sentences' => [
                    'وردة عطرة تزين الحديقة',
                ],
            ],
            // ==========================================
            // الأسبوع 6: إدماج ودعم توليفي
            // ==========================================
            [
                'word' => 'وِقايَةٌ',
                'clean_word' => 'وِقايَةٌ',
                'raw_word' => 'وقاية',
                'example_sentence' => 'حماية الجسم من الأمراض بالنظافة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM6_S1/image63.png',
                'linked_sentences' => [
                    'حماية الجسم من الأمراض بالنظافة',
                ],
            ],
            [
                'word' => 'صِحَّةٌ',
                'clean_word' => 'صِحَّةٌ',
                'raw_word' => 'صحة',
                'example_sentence' => 'سلامة البدن والعقل',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P3_SEM6_S1/image64.png',
                'linked_sentences' => [
                    'سلامة البدن والعقل',
                ],
            ],
        ];

        $now = now();

        foreach ($items as $itemData) {
            $linkedSentences = $itemData['linked_sentences'] ?? [];
            unset($itemData['linked_sentences']);

            $itemData['extracted_at'] = $now;

            $vocab = ArabicVocabularyItem::query()->updateOrCreate(
                [
                    'word' => $itemData['word'],
                    'lesson_id' => $itemData['lesson_id'],
                    'grade' => $itemData['grade'],
                ],
                $itemData
            );

            // Record linked sentences into vocabulary_sentences
            foreach ($linkedSentences as $sentence) {
                DB::table('vocabulary_sentences')->updateOrInsert(
                    [
                        'word' => $itemData['word'],
                        'sentence' => $sentence,
                        'lesson_id' => $itemData['lesson_id'],
                        'grade' => $itemData['grade'],
                    ],
                    [
                        'vocabulary_item_id' => null,
                        'base_word' => $itemData['raw_word'],
                        'subject' => 'AR',
                        'period' => $itemData['period'],
                        'week' => $itemData['week'],
                        'source_type' => 'slide_sentence',
                        'image_path' => $itemData['image_path'] ?? null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
