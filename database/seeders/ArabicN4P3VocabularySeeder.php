<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN4P3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N4, Period P3 (الوحدة 3: الهوايات والفنون والإبداع التعبيري).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: الفعل اللازم والمتعدي والمفعول به
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفعل اللازم والمتعدي والمفعول به',
                'clean_word' => 'تَعَلُّمُ الفعل اللازم والمتعدي والمفعول به',
                'raw_word' => 'تعلم الفعل اللازم والمتعدي والمفعول به',
                'example_sentence' => 'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM1_S1/image67.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفعل اللازم والمتعدي والمفعول به',
                'clean_word' => 'مُفْرَدَةُ الفعل اللازم والمتعدي والمفعول به',
                'raw_word' => 'مفردة الفعل اللازم والمتعدي والمفعول به',
                'example_sentence' => 'رصيد لغوي حول الفعل اللازم والمتعدي والمفعول به',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM1_S1/image68.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفعل اللازم والمتعدي والمفعول به',
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
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM1_S1/image69.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: المصدر الثلاثي وأوزانه
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المصدر الثلاثي وأوزانه',
                'clean_word' => 'تَعَلُّمُ المصدر الثلاثي وأوزانه',
                'raw_word' => 'تعلم المصدر الثلاثي وأوزانه',
                'example_sentence' => 'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM2_S1/image23.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المصدر الثلاثي وأوزانه',
                'clean_word' => 'مُفْرَدَةُ المصدر الثلاثي وأوزانه',
                'raw_word' => 'مفردة المصدر الثلاثي وأوزانه',
                'example_sentence' => 'رصيد لغوي حول المصدر الثلاثي وأوزانه',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM2_S1/image49.jpg',
                'linked_sentences' => [
                    'رصيد لغوي حول المصدر الثلاثي وأوزانه',
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
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM2_S1/image63.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: النعت الحقيقي وتطابقه
            // ==========================================
            [
                'word' => 'تَعَلُّمُ النعت الحقيقي وتطابقه',
                'clean_word' => 'تَعَلُّمُ النعت الحقيقي وتطابقه',
                'raw_word' => 'تعلم النعت الحقيقي وتطابقه',
                'example_sentence' => 'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM3_S1/image66.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ النعت الحقيقي وتطابقه',
                'clean_word' => 'مُفْرَدَةُ النعت الحقيقي وتطابقه',
                'raw_word' => 'مفردة النعت الحقيقي وتطابقه',
                'example_sentence' => 'رصيد لغوي حول النعت الحقيقي وتطابقه',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM3_S1/image47.png',
                'linked_sentences' => [
                    'رصيد لغوي حول النعت الحقيقي وتطابقه',
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
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM3_S1/image48.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الهمزة المتطرفة على السطر والحرف
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الهمزة المتطرفة على السطر والحرف',
                'clean_word' => 'تَعَلُّمُ الهمزة المتطرفة على السطر والحرف',
                'raw_word' => 'تعلم الهمزة المتطرفة على السطر والحرف',
                'example_sentence' => 'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM4_S1/image78.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الهمزة المتطرفة على السطر والحرف',
                'clean_word' => 'مُفْرَدَةُ الهمزة المتطرفة على السطر والحرف',
                'raw_word' => 'مفردة الهمزة المتطرفة على السطر والحرف',
                'example_sentence' => 'رصيد لغوي حول الهمزة المتطرفة على السطر والحرف',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM4_S1/image79.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الهمزة المتطرفة على السطر والحرف',
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
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM4_S1/image58.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم الظواهر اللغوية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ دعم الظواهر اللغوية',
                'clean_word' => 'تَعَلُّمُ دعم الظواهر اللغوية',
                'raw_word' => 'تعلم دعم الظواهر اللغوية',
                'example_sentence' => 'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM5_S1/image70.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ دعم الظواهر اللغوية',
                'clean_word' => 'مُفْرَدَةُ دعم الظواهر اللغوية',
                'raw_word' => 'مفردة دعم الظواهر اللغوية',
                'example_sentence' => 'رصيد لغوي حول دعم الظواهر اللغوية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM5_S1/image66.png',
                'linked_sentences' => [
                    'رصيد لغوي حول دعم الظواهر اللغوية',
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
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM5_S1/image71.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 5',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم الحصيلة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم الحصيلة',
                'clean_word' => 'تَعَلُّمُ تقويم الحصيلة',
                'raw_word' => 'تعلم تقويم الحصيلة',
                'example_sentence' => 'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM6_S1/image54.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الهوايات والفنون والإبداع التعبيري',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الحصيلة',
                'clean_word' => 'مُفْرَدَةُ تقويم الحصيلة',
                'raw_word' => 'مفردة تقويم الحصيلة',
                'example_sentence' => 'رصيد لغوي حول تقويم الحصيلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM6_S1/image56.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم الحصيلة',
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
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P3_SEM6_S1/image57.png',
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
