<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN2P3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N2, Period P3 (الوحدة 3: الصحة والتغذية والرياضة البدنية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: التنوين: الفتح والضم والكسر
            // ==========================================
            [
                'word' => 'تَعَلُّمُ التنوين: الفتح والضم والكسر',
                'clean_word' => 'تَعَلُّمُ التنوين: الفتح والضم والكسر',
                'raw_word' => 'تعلم التنوين: الفتح والضم والكسر',
                'example_sentence' => 'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM1_S1/image57.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ التنوين: الفتح والضم والكسر',
                'clean_word' => 'مُفْرَدَةُ التنوين: الفتح والضم والكسر',
                'raw_word' => 'مفردة التنوين: الفتح والضم والكسر',
                'example_sentence' => 'رصيد لغوي حول التنوين: الفتح والضم والكسر',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM1_S1/image58.png',
                'linked_sentences' => [
                    'رصيد لغوي حول التنوين: الفتح والضم والكسر',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 1',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM1_S1/image68.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الهمزة المتوسطة على الألف
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الهمزة المتوسطة على الألف',
                'clean_word' => 'تَعَلُّمُ الهمزة المتوسطة على الألف',
                'raw_word' => 'تعلم الهمزة المتوسطة على الألف',
                'example_sentence' => 'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM2_S1/image28.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الهمزة المتوسطة على الألف',
                'clean_word' => 'مُفْرَدَةُ الهمزة المتوسطة على الألف',
                'raw_word' => 'مفردة الهمزة المتوسطة على الألف',
                'example_sentence' => 'رصيد لغوي حول الهمزة المتوسطة على الألف',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM2_S1/image27.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الهمزة المتوسطة على الألف',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 2',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM2_S1/image26.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: التاء المربوطة والمبسوطة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ التاء المربوطة والمبسوطة',
                'clean_word' => 'تَعَلُّمُ التاء المربوطة والمبسوطة',
                'raw_word' => 'تعلم التاء المربوطة والمبسوطة',
                'example_sentence' => 'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM3_S1/image25.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ التاء المربوطة والمبسوطة',
                'clean_word' => 'مُفْرَدَةُ التاء المربوطة والمبسوطة',
                'raw_word' => 'مفردة التاء المربوطة والمبسوطة',
                'example_sentence' => 'رصيد لغوي حول التاء المربوطة والمبسوطة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM3_S1/image19.png',
                'linked_sentences' => [
                    'رصيد لغوي حول التاء المربوطة والمبسوطة',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 3',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM3_S1/image16.jpg',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ال الشمسية وال القمرية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ال الشمسية وال القمرية',
                'clean_word' => 'تَعَلُّمُ ال الشمسية وال القمرية',
                'raw_word' => 'تعلم ال الشمسية وال القمرية',
                'example_sentence' => 'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM4_S1/image44.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ال الشمسية وال القمرية',
                'clean_word' => 'مُفْرَدَةُ ال الشمسية وال القمرية',
                'raw_word' => 'مفردة ال الشمسية وال القمرية',
                'example_sentence' => 'رصيد لغوي حول ال الشمسية وال القمرية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM4_S1/image43.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ال الشمسية وال القمرية',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 4',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM4_S1/image33.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم الظواهر القرائية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ دعم الظواهر القرائية',
                'clean_word' => 'تَعَلُّمُ دعم الظواهر القرائية',
                'raw_word' => 'تعلم دعم الظواهر القرائية',
                'example_sentence' => 'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM5_S1/image11.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ دعم الظواهر القرائية',
                'clean_word' => 'مُفْرَدَةُ دعم الظواهر القرائية',
                'raw_word' => 'مفردة دعم الظواهر القرائية',
                'example_sentence' => 'رصيد لغوي حول دعم الظواهر القرائية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM5_S1/image58.png',
                'linked_sentences' => [
                    'رصيد لغوي حول دعم الظواهر القرائية',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 5',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM5_S1/image45.png',
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
                'example_sentence' => 'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM6_S1/image11.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الصحة والتغذية والرياضة البدنية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم الوحدة',
                'raw_word' => 'مفردة تقويم الوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم الوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM6_S1/image62.png',
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
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P3_SEM6_S1/image63.png',
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
