<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN1P2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N1, Period P2 (الوحدة 2: المدرسة وفضاءاتها وتنظيم الحياة المدرسية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: ل (اللام)
            // ==========================================
            [
                'word' => 'لَوْحَةٌ',
                'clean_word' => 'لَوْحَةٌ',
                'raw_word' => 'لوحة',
                'example_sentence' => 'لوحة الكتابة والمسح بالطبشور في القسم',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM1_S1/image67.png',
                'linked_sentences' => [
                    'لوحة الكتابة والمسح بالطبشور في القسم',
                ],
            ],
            [
                'word' => 'لِباسٌ',
                'clean_word' => 'لِباسٌ',
                'raw_word' => 'لباس',
                'example_sentence' => 'زي نظيف ومحتشم للمدرسة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM1_S1/image66.png',
                'linked_sentences' => [
                    'زي نظيف ومحتشم للمدرسة',
                ],
            ],
            [
                'word' => 'لَيْمُونٌ',
                'clean_word' => 'لَيْمُونٌ',
                'raw_word' => 'ليمون',
                'example_sentence' => 'فاكهة حمضية مفيدة للصحة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM1_S1/image65.png',
                'linked_sentences' => [
                    'فاكهة حمضية مفيدة للصحة',
                ],
            ],
            // ==========================================
            // الأسبوع 2: ت (التاء)
            // ==========================================
            [
                'word' => 'تِلْمِيذٌ',
                'clean_word' => 'تِلْمِيذٌ',
                'raw_word' => 'تلميذ',
                'example_sentence' => 'متعلم يواظب على دروسه باجتهاد',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM2_S1/image53.png',
                'linked_sentences' => [
                    'متعلم يواظب على دروسه باجتهاد',
                ],
            ],
            [
                'word' => 'تَمْرٌ',
                'clean_word' => 'تَمْرٌ',
                'raw_word' => 'تمر',
                'example_sentence' => 'غذاء صحي وطبيعي يمد بالجسم بالطاقة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM2_S1/image52.png',
                'linked_sentences' => [
                    'غذاء صحي وطبيعي يمد بالجسم بالطاقة',
                ],
            ],
            [
                'word' => 'تُوتٌ',
                'clean_word' => 'تُوتٌ',
                'raw_word' => 'توت',
                'example_sentence' => 'ثمار لذيذة ملونة يحبها الأطفال',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'ثمار لذيذة ملونة يحبها الأطفال',
                ],
            ],
            // ==========================================
            // الأسبوع 3: ن (النون)
            // ==========================================
            [
                'word' => 'نافِذَةٌ',
                'clean_word' => 'نافِذَةٌ',
                'raw_word' => 'نافذة',
                'example_sentence' => 'نافذة القسم للتهوية والإضاءة الطبيعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM3_S1/image69.png',
                'linked_sentences' => [
                    'نافذة القسم للتهوية والإضاءة الطبيعية',
                ],
            ],
            [
                'word' => 'نَظافَةٌ',
                'clean_word' => 'نَظافَةٌ',
                'raw_word' => 'نظافة',
                'example_sentence' => 'الحفاظ على طهارة الجسم والمكان',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM3_S1/image61.png',
                'linked_sentences' => [
                    'الحفاظ على طهارة الجسم والمكان',
                ],
            ],
            [
                'word' => 'نَمْلَةٌ',
                'clean_word' => 'نَمْلَةٌ',
                'raw_word' => 'نملة',
                'example_sentence' => 'حشرة نشيطة تضرب بها الأمثال في الجد',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM3_S1/image60.png',
                'linked_sentences' => [
                    'حشرة نشيطة تضرب بها الأمثال في الجد',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ض (الضاد)
            // ==========================================
            [
                'word' => 'ضَفْدَعٌ',
                'clean_word' => 'ضَفْدَعٌ',
                'raw_word' => 'ضفدع',
                'example_sentence' => 'حيوان برمائي يعيش قرب الماء',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM4_S1/image64.png',
                'linked_sentences' => [
                    'حيوان برمائي يعيش قرب الماء',
                ],
            ],
            [
                'word' => 'ضَوْءٌ',
                'clean_word' => 'ضَوْءٌ',
                'raw_word' => 'ضوء',
                'example_sentence' => 'نور ينير القسم والشارع',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM4_S1/image63.png',
                'linked_sentences' => [
                    'نور ينير القسم والشارع',
                ],
            ],
            [
                'word' => 'ضِرْسٌ',
                'clean_word' => 'ضِرْسٌ',
                'raw_word' => 'ضرس',
                'example_sentence' => 'سن في الفم ننظفه بالسواك والمعجون',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM4_S1/image62.png',
                'linked_sentences' => [
                    'سن في الفم ننظفه بالسواك والمعجون',
                ],
            ],
            // ==========================================
            // الأسبوع 5: ع (العين)
            // ==========================================
            [
                'word' => 'عَلَمٌ',
                'clean_word' => 'عَلَمٌ',
                'raw_word' => 'علم',
                'example_sentence' => 'راية الوطن المغربي الحمراء بنجمتها الخضراء',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM5_S1/image83.png',
                'linked_sentences' => [
                    'راية الوطن المغربي الحمراء بنجمتها الخضراء',
                ],
            ],
            [
                'word' => 'عَيْنٌ',
                'clean_word' => 'عَيْنٌ',
                'raw_word' => 'عين',
                'example_sentence' => 'حاسة البصر نرى بها الجمال والكتب',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM5_S1/image63.png',
                'linked_sentences' => [
                    'حاسة البصر نرى بها الجمال والكتب',
                ],
            ],
            [
                'word' => 'عُشٌّ',
                'clean_word' => 'عُشٌّ',
                'raw_word' => 'عش',
                'example_sentence' => 'مسكن الطيور وصغارها على الشجرة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM5_S1/image84.png',
                'linked_sentences' => [
                    'مسكن الطيور وصغارها على الشجرة',
                ],
            ],
            // ==========================================
            // الأسبوع 6: إدماج ودعم توليفي
            // ==========================================
            [
                'word' => 'دَعْمٌ',
                'clean_word' => 'دَعْمٌ',
                'raw_word' => 'دعم',
                'example_sentence' => 'تثبيت الحروف والمقاطع المكتسبة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM6_S1/image30.png',
                'linked_sentences' => [
                    'تثبيت الحروف والمقاطع المكتسبة',
                ],
            ],
            [
                'word' => 'حَصيلَةٌ',
                'clean_word' => 'حَصيلَةٌ',
                'raw_word' => 'حصيلة',
                'example_sentence' => 'مجموع الكلمات المقروءة بطلاقة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P2_SEM6_S1/image18.jpg',
                'linked_sentences' => [
                    'مجموع الكلمات المقروءة بطلاقة',
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
