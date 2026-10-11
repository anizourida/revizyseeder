<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN5P4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N5, Period P4 (الوحدة 4: الابتكار والاختراع والصناعة المتقدمة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: اسم الآلة وصيغه القياسية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ اسم الآلة وصيغه القياسية',
                'clean_word' => 'تَعَلُّمُ اسم الآلة وصيغه القياسية',
                'raw_word' => 'تعلم اسم الآلة وصيغه القياسية',
                'example_sentence' => 'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM1_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ اسم الآلة وصيغه القياسية',
                'clean_word' => 'مُفْرَدَةُ اسم الآلة وصيغه القياسية',
                'raw_word' => 'مفردة اسم الآلة وصيغه القياسية',
                'example_sentence' => 'رصيد لغوي حول اسم الآلة وصيغه القياسية',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM1_S1/image41.png',
                'linked_sentences' => [
                    'رصيد لغوي حول اسم الآلة وصيغه القياسية',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 1',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM1_S1/image42.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الحال المفردة والجملة الحالية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الحال المفردة والجملة الحالية',
                'clean_word' => 'تَعَلُّمُ الحال المفردة والجملة الحالية',
                'raw_word' => 'تعلم الحال المفردة والجملة الحالية',
                'example_sentence' => 'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM2_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الحال المفردة والجملة الحالية',
                'clean_word' => 'مُفْرَدَةُ الحال المفردة والجملة الحالية',
                'raw_word' => 'مفردة الحال المفردة والجملة الحالية',
                'example_sentence' => 'رصيد لغوي حول الحال المفردة والجملة الحالية',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM2_S1/image39.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الحال المفردة والجملة الحالية',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 2',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM2_S1/image40.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: اسم التفضيل على وزن أفعل
            // ==========================================
            [
                'word' => 'تَعَلُّمُ اسم التفضيل على وزن أفعل',
                'clean_word' => 'تَعَلُّمُ اسم التفضيل على وزن أفعل',
                'raw_word' => 'تعلم اسم التفضيل على وزن أفعل',
                'example_sentence' => 'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM3_S1/image71.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ اسم التفضيل على وزن أفعل',
                'clean_word' => 'مُفْرَدَةُ اسم التفضيل على وزن أفعل',
                'raw_word' => 'مفردة اسم التفضيل على وزن أفعل',
                'example_sentence' => 'رصيد لغوي حول اسم التفضيل على وزن أفعل',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM3_S1/image72.jpeg',
                'linked_sentences' => [
                    'رصيد لغوي حول اسم التفضيل على وزن أفعل',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 3',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM3_S1/image73.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الألف اللينة في الأفعال والأسماء والحروف
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الألف اللينة في الأفعال والأسماء والحروف',
                'clean_word' => 'تَعَلُّمُ الألف اللينة في الأفعال والأسماء والحروف',
                'raw_word' => 'تعلم الألف اللينة في الأفعال والأسماء والحروف',
                'example_sentence' => 'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM4_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الألف اللينة في الأفعال والأسماء والحروف',
                'clean_word' => 'مُفْرَدَةُ الألف اللينة في الأفعال والأسماء والحروف',
                'raw_word' => 'مفردة الألف اللينة في الأفعال والأسماء والحروف',
                'example_sentence' => 'رصيد لغوي حول الألف اللينة في الأفعال والأسماء والحروف',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM4_S1/image41.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الألف اللينة في الأفعال والأسماء والحروف',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 4',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM4_S1/image42.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: أسبوع الدعم والتركيب
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسبوع الدعم والتركيب',
                'clean_word' => 'تَعَلُّمُ أسبوع الدعم والتركيب',
                'raw_word' => 'تعلم أسبوع الدعم والتركيب',
                'example_sentence' => 'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM5_S1/image31.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'raw_word' => 'مفردة أسبوع الدعم والتركيب',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم والتركيب',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM5_S1/image34.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسبوع الدعم والتركيب',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 5',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM5_S1/image35.png',
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
                'example_sentence' => 'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM6_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الابتكار والاختراع والصناعة المتقدمة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم الوحدة',
                'raw_word' => 'مفردة تقويم الوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم الوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM6_S1/image40.png',
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
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P4_SEM6_S1/image41.png',
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
