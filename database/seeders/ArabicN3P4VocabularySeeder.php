<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN3P4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N3, Period P4 (الوحدة 4: الحرف والمهن والأنشطة الاقتصادية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: ظرفا الزمان والمكان
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ظرفا الزمان والمكان',
                'clean_word' => 'تَعَلُّمُ ظرفا الزمان والمكان',
                'raw_word' => 'تعلم ظرفا الزمان والمكان',
                'example_sentence' => 'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM1_S1/image59.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ظرفا الزمان والمكان',
                'clean_word' => 'مُفْرَدَةُ ظرفا الزمان والمكان',
                'raw_word' => 'مفردة ظرفا الزمان والمكان',
                'example_sentence' => 'رصيد لغوي حول ظرفا الزمان والمكان',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM1_S1/image62.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ظرفا الزمان والمكان',
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
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM1_S1/image63.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الفعل المبني للمجهول المبسط
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفعل المبني للمجهول المبسط',
                'clean_word' => 'تَعَلُّمُ الفعل المبني للمجهول المبسط',
                'raw_word' => 'تعلم الفعل المبني للمجهول المبسط',
                'example_sentence' => 'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM2_S1/image43.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفعل المبني للمجهول المبسط',
                'clean_word' => 'مُفْرَدَةُ الفعل المبني للمجهول المبسط',
                'raw_word' => 'مفردة الفعل المبني للمجهول المبسط',
                'example_sentence' => 'رصيد لغوي حول الفعل المبني للمجهول المبسط',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM2_S1/image44.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفعل المبني للمجهول المبسط',
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
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM2_S1/image45.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: اسم الفاعل المشتق من الثلاثي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ اسم الفاعل المشتق من الثلاثي',
                'clean_word' => 'تَعَلُّمُ اسم الفاعل المشتق من الثلاثي',
                'raw_word' => 'تعلم اسم الفاعل المشتق من الثلاثي',
                'example_sentence' => 'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM3_S1/image51.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ اسم الفاعل المشتق من الثلاثي',
                'clean_word' => 'مُفْرَدَةُ اسم الفاعل المشتق من الثلاثي',
                'raw_word' => 'مفردة اسم الفاعل المشتق من الثلاثي',
                'example_sentence' => 'رصيد لغوي حول اسم الفاعل المشتق من الثلاثي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM3_S1/image52.png',
                'linked_sentences' => [
                    'رصيد لغوي حول اسم الفاعل المشتق من الثلاثي',
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
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM3_S1/image53.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الألف اللينة في الأفعال والأسماء
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الألف اللينة في الأفعال والأسماء',
                'clean_word' => 'تَعَلُّمُ الألف اللينة في الأفعال والأسماء',
                'raw_word' => 'تعلم الألف اللينة في الأفعال والأسماء',
                'example_sentence' => 'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM4_S1/image34.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الألف اللينة في الأفعال والأسماء',
                'clean_word' => 'مُفْرَدَةُ الألف اللينة في الأفعال والأسماء',
                'raw_word' => 'مفردة الألف اللينة في الأفعال والأسماء',
                'example_sentence' => 'رصيد لغوي حول الألف اللينة في الأفعال والأسماء',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM4_S1/image33.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الألف اللينة في الأفعال والأسماء',
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
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM4_S1/image32.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: أسبوع الدعم
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسبوع الدعم',
                'clean_word' => 'تَعَلُّمُ أسبوع الدعم',
                'raw_word' => 'تعلم أسبوع الدعم',
                'example_sentence' => 'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM5_S1/image26.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم',
                'raw_word' => 'مفردة أسبوع الدعم',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM5_S1/image28.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسبوع الدعم',
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
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM5_S1/image29.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 5',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم الوحدة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم الوحدة',
                'clean_word' => 'تَعَلُّمُ تقويم الوحدة',
                'raw_word' => 'تعلم تقويم الوحدة',
                'example_sentence' => 'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM6_S1/image26.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحرف والمهن والأنشطة الاقتصادية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم الوحدة',
                'raw_word' => 'مفردة تقويم الوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم الوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM6_S1/image23.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم الوحدة',
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
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P4_SEM6_S1/image27.png',
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
