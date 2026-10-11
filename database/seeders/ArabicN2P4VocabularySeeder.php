<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN2P4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N2, Period P4 (الوحدة 4: القرية والمدينة والمرافق العامة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: أسماء الإشارة (هذا، هذه، هؤلاء)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسماء الإشارة',
                'clean_word' => 'تَعَلُّمُ أسماء الإشارة',
                'raw_word' => 'تعلم أسماء الإشارة',
                'example_sentence' => 'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM1_S1/image19.png',
                'linked_sentences' => [
                    'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسماء الإشارة',
                'clean_word' => 'مُفْرَدَةُ أسماء الإشارة',
                'raw_word' => 'مفردة أسماء الإشارة',
                'example_sentence' => 'رصيد لغوي حول أسماء الإشارة (هذا، هذه، هؤلاء)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM1_S1/image16.jpg',
                'linked_sentences' => [
                    'رصيد لغوي حول أسماء الإشارة (هذا، هذه، هؤلاء)',
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
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM1_S1/image11.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الأسماء الموصولة (الذي، التي)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الأسماء الموصولة',
                'clean_word' => 'تَعَلُّمُ الأسماء الموصولة',
                'raw_word' => 'تعلم الأسماء الموصولة',
                'example_sentence' => 'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM2_S1/image26.png',
                'linked_sentences' => [
                    'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الأسماء الموصولة',
                'clean_word' => 'مُفْرَدَةُ الأسماء الموصولة',
                'raw_word' => 'مفردة الأسماء الموصولة',
                'example_sentence' => 'رصيد لغوي حول الأسماء الموصولة (الذي، التي)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM2_S1/image25.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الأسماء الموصولة (الذي، التي)',
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
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM2_S1/image19.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الجملة الاسمية البسيطة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الجملة الاسمية البسيطة',
                'clean_word' => 'تَعَلُّمُ الجملة الاسمية البسيطة',
                'raw_word' => 'تعلم الجملة الاسمية البسيطة',
                'example_sentence' => 'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM3_S1/image19.png',
                'linked_sentences' => [
                    'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الجملة الاسمية البسيطة',
                'clean_word' => 'مُفْرَدَةُ الجملة الاسمية البسيطة',
                'raw_word' => 'مفردة الجملة الاسمية البسيطة',
                'example_sentence' => 'رصيد لغوي حول الجملة الاسمية البسيطة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM3_S1/image16.jpg',
                'linked_sentences' => [
                    'رصيد لغوي حول الجملة الاسمية البسيطة',
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
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM3_S1/image11.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الجملة الفعلية البسيطة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الجملة الفعلية البسيطة',
                'clean_word' => 'تَعَلُّمُ الجملة الفعلية البسيطة',
                'raw_word' => 'تعلم الجملة الفعلية البسيطة',
                'example_sentence' => 'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM4_S1/image27.png',
                'linked_sentences' => [
                    'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الجملة الفعلية البسيطة',
                'clean_word' => 'مُفْرَدَةُ الجملة الفعلية البسيطة',
                'raw_word' => 'مفردة الجملة الفعلية البسيطة',
                'example_sentence' => 'رصيد لغوي حول الجملة الفعلية البسيطة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM4_S1/image26.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الجملة الفعلية البسيطة',
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
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM4_S1/image25.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم التراكيب الشفهية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ دعم التراكيب الشفهية',
                'clean_word' => 'تَعَلُّمُ دعم التراكيب الشفهية',
                'raw_word' => 'تعلم دعم التراكيب الشفهية',
                'example_sentence' => 'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM5_S1/image11.png',
                'linked_sentences' => [
                    'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ دعم التراكيب الشفهية',
                'clean_word' => 'مُفْرَدَةُ دعم التراكيب الشفهية',
                'raw_word' => 'مفردة دعم التراكيب الشفهية',
                'example_sentence' => 'رصيد لغوي حول دعم التراكيب الشفهية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM5_S1/image65.png',
                'linked_sentences' => [
                    'رصيد لغوي حول دعم التراكيب الشفهية',
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
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM5_S1/image53.png',
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
                'example_sentence' => 'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM6_S1/image31.png',
                'linked_sentences' => [
                    'مفهوم معرفي في القرية والمدينة والمرافق العامة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم ختامي',
                'clean_word' => 'مُفْرَدَةُ تقويم ختامي',
                'raw_word' => 'مفردة تقويم ختامي',
                'example_sentence' => 'رصيد لغوي حول تقويم ختامي',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM6_S1/image33.png',
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
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P4_SEM6_S1/image21.png',
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
