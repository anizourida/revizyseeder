<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN4P4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N4, Period P4 (الوحدة 4: الفلاحة والصناعة والتجارة والابتكار الإنتاجي).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: المفعول المطلق وأنواعه
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المفعول المطلق وأنواعه',
                'clean_word' => 'تَعَلُّمُ المفعول المطلق وأنواعه',
                'raw_word' => 'تعلم المفعول المطلق وأنواعه',
                'example_sentence' => 'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM1_S1/image76.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المفعول المطلق وأنواعه',
                'clean_word' => 'مُفْرَدَةُ المفعول المطلق وأنواعه',
                'raw_word' => 'مفردة المفعول المطلق وأنواعه',
                'example_sentence' => 'رصيد لغوي حول المفعول المطلق وأنواعه',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM1_S1/image77.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المفعول المطلق وأنواعه',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 1',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM1_S1/image78.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: المفعول فيه (ظرفا الزمان والمكان)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المفعول فيه',
                'clean_word' => 'تَعَلُّمُ المفعول فيه',
                'raw_word' => 'تعلم المفعول فيه',
                'example_sentence' => 'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM2_S1/image65.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المفعول فيه',
                'clean_word' => 'مُفْرَدَةُ المفعول فيه',
                'raw_word' => 'مفردة المفعول فيه',
                'example_sentence' => 'رصيد لغوي حول المفعول فيه (ظرفا الزمان والمكان)',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المفعول فيه (ظرفا الزمان والمكان)',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 2',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM2_S1/image47.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الاسم المجرور بالكسرة والياء
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الاسم المجرور بالكسرة والياء',
                'clean_word' => 'تَعَلُّمُ الاسم المجرور بالكسرة والياء',
                'raw_word' => 'تعلم الاسم المجرور بالكسرة والياء',
                'example_sentence' => 'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM3_S1/image39.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الاسم المجرور بالكسرة والياء',
                'clean_word' => 'مُفْرَدَةُ الاسم المجرور بالكسرة والياء',
                'raw_word' => 'مفردة الاسم المجرور بالكسرة والياء',
                'example_sentence' => 'رصيد لغوي حول الاسم المجرور بالكسرة والياء',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM3_S1/image76.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الاسم المجرور بالكسرة والياء',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 3',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM3_S1/image77.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: حذف ألف الإشارة وألف ابن
            // ==========================================
            [
                'word' => 'تَعَلُّمُ حذف ألف الإشارة وألف ابن',
                'clean_word' => 'تَعَلُّمُ حذف ألف الإشارة وألف ابن',
                'raw_word' => 'تعلم حذف ألف الإشارة وألف ابن',
                'example_sentence' => 'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM4_S1/image39.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ حذف ألف الإشارة وألف ابن',
                'clean_word' => 'مُفْرَدَةُ حذف ألف الإشارة وألف ابن',
                'raw_word' => 'مفردة حذف ألف الإشارة وألف ابن',
                'example_sentence' => 'رصيد لغوي حول حذف ألف الإشارة وألف ابن',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM4_S1/image38.png',
                'linked_sentences' => [
                    'رصيد لغوي حول حذف ألف الإشارة وألف ابن',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 4',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM4_S1/image37.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: أسبوع الدعم السنوي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسبوع الدعم السنوي',
                'clean_word' => 'تَعَلُّمُ أسبوع الدعم السنوي',
                'raw_word' => 'تعلم أسبوع الدعم السنوي',
                'example_sentence' => 'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM5_S1/image74.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم السنوي',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم السنوي',
                'raw_word' => 'مفردة أسبوع الدعم السنوي',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم السنوي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM5_S1/image75.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسبوع الدعم السنوي',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 5',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM5_S1/image76.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 5',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم شامل
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم شامل',
                'clean_word' => 'تَعَلُّمُ تقويم شامل',
                'raw_word' => 'تعلم تقويم شامل',
                'example_sentence' => 'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM6_S1/image49.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الفلاحة والصناعة والتجارة والابتكار الإنتاجي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم شامل',
                'clean_word' => 'مُفْرَدَةُ تقويم شامل',
                'raw_word' => 'مفردة تقويم شامل',
                'example_sentence' => 'رصيد لغوي حول تقويم شامل',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM6_S1/image50.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم شامل',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 6',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P4_SEM6_S1/image51.png',
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
