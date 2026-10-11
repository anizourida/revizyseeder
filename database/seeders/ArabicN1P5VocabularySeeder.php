<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN1P5VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N1, Period P5 (الوحدة 5: البيئة والطبيعة والحيوانات).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: هـ (الهاء)
            // ==========================================
            [
                'word' => 'هِلالٌ',
                'clean_word' => 'هِلالٌ',
                'raw_word' => 'هلال',
                'example_sentence' => 'قمر في أول الشهر يزين السماء ليلاً',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM1_S1/image50.png',
                'linked_sentences' => [
                    'قمر في أول الشهر يزين السماء ليلاً',
                ],
            ],
            [
                'word' => 'هَدِيَّةٌ',
                'clean_word' => 'هَدِيَّةٌ',
                'raw_word' => 'هدية',
                'example_sentence' => 'تحفة محبة نسعد بها أصدقاءنا',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM1_S1/image49.png',
                'linked_sentences' => [
                    'تحفة محبة نسعد بها أصدقاءنا',
                ],
            ],
            [
                'word' => 'هاتِفٌ',
                'clean_word' => 'هاتِفٌ',
                'raw_word' => 'هاتف',
                'example_sentence' => 'وسيلة اتصال وتواصل حديثة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM1_S1/image48.png',
                'linked_sentences' => [
                    'وسيلة اتصال وتواصل حديثة',
                ],
            ],
            // ==========================================
            // الأسبوع 2: خ (الخاء)
            // ==========================================
            [
                'word' => 'خَرُوفٌ',
                'clean_word' => 'خَرُوفٌ',
                'raw_word' => 'خروف',
                'example_sentence' => 'حيوان أليف يربى في المزرعة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM2_S1/image52.png',
                'linked_sentences' => [
                    'حيوان أليف يربى في المزرعة',
                ],
            ],
            [
                'word' => 'خُبْزٌ',
                'clean_word' => 'خُبْزٌ',
                'raw_word' => 'خبز',
                'example_sentence' => 'غذاء رئيسي يخبز من القمح',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM2_S1/image47.png',
                'linked_sentences' => [
                    'غذاء رئيسي يخبز من القمح',
                ],
            ],
            [
                'word' => 'خَيْمَةٌ',
                'clean_word' => 'خَيْمَةٌ',
                'raw_word' => 'خيمة',
                'example_sentence' => 'مسكن متنقل في الطبيعة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'مسكن متنقل في الطبيعة',
                ],
            ],
            // ==========================================
            // الأسبوع 3: غ (الغين)
            // ==========================================
            [
                'word' => 'غابَةٌ',
                'clean_word' => 'غابَةٌ',
                'raw_word' => 'غابة',
                'example_sentence' => 'رئة الأرض المليئة بالأشجار والحيوانات',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM3_S1/image15.png',
                'linked_sentences' => [
                    'رئة الأرض المليئة بالأشجار والحيوانات',
                ],
            ],
            [
                'word' => 'غَزالٌ',
                'clean_word' => 'غَزالٌ',
                'raw_word' => 'غزال',
                'example_sentence' => 'حيوان رشيق وجميل في البراري',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM3_S1/image69.png',
                'linked_sentences' => [
                    'حيوان رشيق وجميل في البراري',
                ],
            ],
            [
                'word' => 'غَيْمَةٌ',
                'clean_word' => 'غَيْمَةٌ',
                'raw_word' => 'غيمة',
                'example_sentence' => 'سحابة ماطرة تسقي الزرع',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM3_S1/image75.png',
                'linked_sentences' => [
                    'سحابة ماطرة تسقي الزرع',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ق (القاف)
            // ==========================================
            [
                'word' => 'قِطٌّ',
                'clean_word' => 'قِطٌّ',
                'raw_word' => 'قط',
                'example_sentence' => 'حيوان أليف لطيف يحب المداعبة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM4_S1/image15.png',
                'linked_sentences' => [
                    'حيوان أليف لطيف يحب المداعبة',
                ],
            ],
            [
                'word' => 'قَمَرٌ',
                'clean_word' => 'قَمَرٌ',
                'raw_word' => 'قمر',
                'example_sentence' => 'كوكب منير يضيء عتمة الليل',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM4_S1/image94.png',
                'linked_sentences' => [
                    'كوكب منير يضيء عتمة الليل',
                ],
            ],
            [
                'word' => 'قَلْبٌ',
                'clean_word' => 'قَلْبٌ',
                'raw_word' => 'قلب',
                'example_sentence' => 'عضو ينبض بالحياة والمحبة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P5_SEM4_S1/image61.png',
                'linked_sentences' => [
                    'عضو ينبض بالحياة والمحبة',
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
