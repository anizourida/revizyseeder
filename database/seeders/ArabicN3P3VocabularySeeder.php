<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN3P3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N3, Period P3 (الوحدة 3: الوقاية من الأخطار والسلامة البدنية والمرورية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: النواسخ الحرفية: إن، أن، كأن
            // ==========================================
            [
                'word' => 'تَعَلُّمُ النواسخ الحرفية: إن، أن، كأن',
                'clean_word' => 'تَعَلُّمُ النواسخ الحرفية: إن، أن، كأن',
                'raw_word' => 'تعلم النواسخ الحرفية: إن، أن، كأن',
                'example_sentence' => 'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM1_S1/image48.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ النواسخ الحرفية: إن، أن، كأن',
                'clean_word' => 'مُفْرَدَةُ النواسخ الحرفية: إن، أن، كأن',
                'raw_word' => 'مفردة النواسخ الحرفية: إن، أن، كأن',
                'example_sentence' => 'رصيد لغوي حول النواسخ الحرفية: إن، أن، كأن',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM1_S1/image47.png',
                'linked_sentences' => [
                    'رصيد لغوي حول النواسخ الحرفية: إن، أن، كأن',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 1',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM1_S1/image49.jpeg',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الفعل المعتل في السياق
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفعل المعتل في السياق',
                'clean_word' => 'تَعَلُّمُ الفعل المعتل في السياق',
                'raw_word' => 'تعلم الفعل المعتل في السياق',
                'example_sentence' => 'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM2_S1/image43.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفعل المعتل في السياق',
                'clean_word' => 'مُفْرَدَةُ الفعل المعتل في السياق',
                'raw_word' => 'مفردة الفعل المعتل في السياق',
                'example_sentence' => 'رصيد لغوي حول الفعل المعتل في السياق',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM2_S1/image44.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفعل المعتل في السياق',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 2',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM2_S1/image45.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الهمزة المتوسطة على الواو والياء
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الهمزة المتوسطة على الواو والياء',
                'clean_word' => 'تَعَلُّمُ الهمزة المتوسطة على الواو والياء',
                'raw_word' => 'تعلم الهمزة المتوسطة على الواو والياء',
                'example_sentence' => 'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM3_S1/image52.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الهمزة المتوسطة على الواو والياء',
                'clean_word' => 'مُفْرَدَةُ الهمزة المتوسطة على الواو والياء',
                'raw_word' => 'مفردة الهمزة المتوسطة على الواو والياء',
                'example_sentence' => 'رصيد لغوي حول الهمزة المتوسطة على الواو والياء',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM3_S1/image53.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الهمزة المتوسطة على الواو والياء',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 3',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM3_S1/image54.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الاستفهام والأمر في إرشادات السلامة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الاستفهام والأمر في إرشادات السلامة',
                'clean_word' => 'تَعَلُّمُ الاستفهام والأمر في إرشادات السلامة',
                'raw_word' => 'تعلم الاستفهام والأمر في إرشادات السلامة',
                'example_sentence' => 'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM4_S1/image17.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الاستفهام والأمر في إرشادات السلامة',
                'clean_word' => 'مُفْرَدَةُ الاستفهام والأمر في إرشادات السلامة',
                'raw_word' => 'مفردة الاستفهام والأمر في إرشادات السلامة',
                'example_sentence' => 'رصيد لغوي حول الاستفهام والأمر في إرشادات السلامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM4_S1/image49.jpeg',
                'linked_sentences' => [
                    'رصيد لغوي حول الاستفهام والأمر في إرشادات السلامة',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 4',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM4_S1/image50.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم التعلمات
            // ==========================================
            [
                'word' => 'تَعَلُّمُ دعم التعلمات',
                'clean_word' => 'تَعَلُّمُ دعم التعلمات',
                'raw_word' => 'تعلم دعم التعلمات',
                'example_sentence' => 'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM5_S1/image35.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ دعم التعلمات',
                'clean_word' => 'مُفْرَدَةُ دعم التعلمات',
                'raw_word' => 'مفردة دعم التعلمات',
                'example_sentence' => 'رصيد لغوي حول دعم التعلمات',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM5_S1/image36.png',
                'linked_sentences' => [
                    'رصيد لغوي حول دعم التعلمات',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 5',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM5_S1/image37.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 5',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم ختامي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم ختامي',
                'clean_word' => 'تَعَلُّمُ تقويم ختامي',
                'raw_word' => 'تعلم تقويم ختامي',
                'example_sentence' => 'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM6_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الوقاية من الأخطار والسلامة البدنية والمرورية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم ختامي',
                'clean_word' => 'مُفْرَدَةُ تقويم ختامي',
                'raw_word' => 'مفردة تقويم ختامي',
                'example_sentence' => 'رصيد لغوي حول تقويم ختامي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM6_S1/image41.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم ختامي',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 6',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P3_SEM6_S1/image43.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 6',
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
