<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN1P4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N1, Period P4 (الوحدة 4: الحي والقرية والفضاءات المجاورة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: ط (الطاء)
            // ==========================================
            [
                'word' => 'طَريقٌ',
                'clean_word' => 'طَريقٌ',
                'raw_word' => 'طريق',
                'example_sentence' => 'مسار ممهد يسير فيه المارة والسيارات',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM1_S1/image53.png',
                'linked_sentences' => [
                    'مسار ممهد يسير فيه المارة والسيارات',
                ],
            ],
            [
                'word' => 'طَبِيبٌ',
                'clean_word' => 'طَبِيبٌ',
                'raw_word' => 'طبيب',
                'example_sentence' => 'معالج يفحص المرضى بالعيادة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM1_S1/image51.png',
                'linked_sentences' => [
                    'معالج يفحص المرضى بالعيادة',
                ],
            ],
            [
                'word' => 'طَيّارٌ',
                'clean_word' => 'طَيّارٌ',
                'raw_word' => 'طيار',
                'example_sentence' => 'قائد الطائرة في السماء',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM1_S1/image47.png',
                'linked_sentences' => [
                    'قائد الطائرة في السماء',
                ],
            ],
            // ==========================================
            // الأسبوع 2: ظ (الظاء)
            // ==========================================
            [
                'word' => 'ظَرْفٌ',
                'clean_word' => 'ظَرْفٌ',
                'raw_word' => 'ظرف',
                'example_sentence' => 'غلاف ورقي للرسائل البريدية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM2_S1/image107.png',
                'linked_sentences' => [
                    'غلاف ورقي للرسائل البريدية',
                ],
            ],
            [
                'word' => 'ظِلٌّ',
                'clean_word' => 'ظِلٌّ',
                'raw_word' => 'ظل',
                'example_sentence' => 'مساحة تحمي من حرارة الشمس',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM2_S1/image108.png',
                'linked_sentences' => [
                    'مساحة تحمي من حرارة الشمس',
                ],
            ],
            [
                'word' => 'ظَبْيٌ',
                'clean_word' => 'ظَبْيٌ',
                'raw_word' => 'ظبي',
                'example_sentence' => 'غزال رشيق وسريع في البرية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM2_S1/image109.png',
                'linked_sentences' => [
                    'غزال رشيق وسريع في البرية',
                ],
            ],
            // ==========================================
            // الأسبوع 3: و (الواو)
            // ==========================================
            [
                'word' => 'وَرْدَةٌ',
                'clean_word' => 'وَرْدَةٌ',
                'raw_word' => 'وردة',
                'example_sentence' => 'زهرة جميلة المنظر زكية الرائحة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM3_S1/image101.png',
                'linked_sentences' => [
                    'زهرة جميلة المنظر زكية الرائحة',
                ],
            ],
            [
                'word' => 'وَطَنٌ',
                'clean_word' => 'وَطَنٌ',
                'raw_word' => 'وطن',
                'example_sentence' => 'الأرض الحبيبة التي نعيش فيها بأمان',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM3_S1/image65.png',
                'linked_sentences' => [
                    'الأرض الحبيبة التي نعيش فيها بأمان',
                ],
            ],
            [
                'word' => 'وِسامٌ',
                'clean_word' => 'وِسامٌ',
                'raw_word' => 'وسام',
                'example_sentence' => 'شعار التكريم والتقدير',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM3_S1/image64.png',
                'linked_sentences' => [
                    'شعار التكريم والتقدير',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ي (الياء)
            // ==========================================
            [
                'word' => 'يَدٌ',
                'clean_word' => 'يَدٌ',
                'raw_word' => 'يد',
                'example_sentence' => 'طرف في الجسم نكتب ونعمل به',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM4_S1/image15.png',
                'linked_sentences' => [
                    'طرف في الجسم نكتب ونعمل به',
                ],
            ],
            [
                'word' => 'يَمامَةٌ',
                'clean_word' => 'يَمامَةٌ',
                'raw_word' => 'يمامة',
                'example_sentence' => 'حمامة وديعة ترمز للسلام',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM4_S1/image113.jpeg',
                'linked_sentences' => [
                    'حمامة وديعة ترمز للسلام',
                ],
            ],
            [
                'word' => 'ياسَمِينٌ',
                'clean_word' => 'ياسَمِينٌ',
                'raw_word' => 'ياسمين',
                'example_sentence' => 'زهر أبيض جميل الرائحة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM4_S1/image88.jpg',
                'linked_sentences' => [
                    'زهر أبيض جميل الرائحة',
                ],
            ],
            // ==========================================
            // الأسبوع 5: ك (الكاف)
            // ==========================================
            [
                'word' => 'كِتابٌ',
                'clean_word' => 'كِتابٌ',
                'raw_word' => 'كتاب',
                'example_sentence' => 'مجلد يجمع المعارف والقصص المشوقة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM5_S1/image67.png',
                'linked_sentences' => [
                    'مجلد يجمع المعارف والقصص المشوقة',
                ],
            ],
            [
                'word' => 'كُرَةٌ',
                'clean_word' => 'كُرَةٌ',
                'raw_word' => 'كرة',
                'example_sentence' => 'أداة للعب الرياضي في الملعب',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM5_S1/image52.png',
                'linked_sentences' => [
                    'أداة للعب الرياضي في الملعب',
                ],
            ],
            [
                'word' => 'كَلْبٌ',
                'clean_word' => 'كَلْبٌ',
                'raw_word' => 'كلب',
                'example_sentence' => 'حيوان وفي يحرس البيت',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM5_S1/image68.png',
                'linked_sentences' => [
                    'حيوان وفي يحرس البيت',
                ],
            ],
            // ==========================================
            // الأسبوع 6: إدماج ودعم توليفي
            // ==========================================
            [
                'word' => 'حَيٌّ',
                'clean_word' => 'حَيٌّ',
                'raw_word' => 'حي',
                'example_sentence' => 'تجمع سكني جميل في المدينة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM6_S1/image45.png',
                'linked_sentences' => [
                    'تجمع سكني جميل في المدينة',
                ],
            ],
            [
                'word' => 'قَرْيَةٌ',
                'clean_word' => 'قَرْيَةٌ',
                'raw_word' => 'قرية',
                'example_sentence' => 'فضاء ريفي هادئ نقي الهواء',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P4_SEM6_S1/image49.png',
                'linked_sentences' => [
                    'فضاء ريفي هادئ نقي الهواء',
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
