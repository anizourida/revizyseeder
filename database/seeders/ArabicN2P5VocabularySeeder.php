<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN2P5VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N2, Period P5 (الوحدة 5: عالم الحيوان والطبيعة الحية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: علامات الترقيم (النقطة، الفاصلة، علامة الاستفهام)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ علامات الترقيم',
                'clean_word' => 'تَعَلُّمُ علامات الترقيم',
                'raw_word' => 'تعلم علامات الترقيم',
                'example_sentence' => 'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM1_S1/image48.png',
                'linked_sentences' => [
                    'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ علامات الترقيم',
                'clean_word' => 'مُفْرَدَةُ علامات الترقيم',
                'raw_word' => 'مفردة علامات الترقيم',
                'example_sentence' => 'رصيد لغوي حول علامات الترقيم (النقطة، الفاصلة، علامة الاستفهام)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM1_S1/image18.png',
                'linked_sentences' => [
                    'رصيد لغوي حول علامات الترقيم (النقطة، الفاصلة، علامة الاستفهام)',
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
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM1_S1/image15.jpg',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: أسلوب التعجب والنداء
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسلوب التعجب والنداء',
                'clean_word' => 'تَعَلُّمُ أسلوب التعجب والنداء',
                'raw_word' => 'تعلم أسلوب التعجب والنداء',
                'example_sentence' => 'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM2_S1/image48.png',
                'linked_sentences' => [
                    'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسلوب التعجب والنداء',
                'clean_word' => 'مُفْرَدَةُ أسلوب التعجب والنداء',
                'raw_word' => 'مفردة أسلوب التعجب والنداء',
                'example_sentence' => 'رصيد لغوي حول أسلوب التعجب والنداء',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM2_S1/image16.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسلوب التعجب والنداء',
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
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM2_S1/image15.jpg',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: النهي والأمر المبسط
            // ==========================================
            [
                'word' => 'تَعَلُّمُ النهي والأمر المبسط',
                'clean_word' => 'تَعَلُّمُ النهي والأمر المبسط',
                'raw_word' => 'تعلم النهي والأمر المبسط',
                'example_sentence' => 'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM3_S1/image30.png',
                'linked_sentences' => [
                    'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ النهي والأمر المبسط',
                'clean_word' => 'مُفْرَدَةُ النهي والأمر المبسط',
                'raw_word' => 'مفردة النهي والأمر المبسط',
                'example_sentence' => 'رصيد لغوي حول النهي والأمر المبسط',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM3_S1/image29.png',
                'linked_sentences' => [
                    'رصيد لغوي حول النهي والأمر المبسط',
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
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM3_S1/image28.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: تقويم ودعم ختامي للطلاقة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم ودعم ختامي للطلاقة',
                'clean_word' => 'تَعَلُّمُ تقويم ودعم ختامي للطلاقة',
                'raw_word' => 'تعلم تقويم ودعم ختامي للطلاقة',
                'example_sentence' => 'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM4_S1/image34.png',
                'linked_sentences' => [
                    'مفهوم معرفي في عالم الحيوان والطبيعة الحية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم ودعم ختامي للطلاقة',
                'clean_word' => 'مُفْرَدَةُ تقويم ودعم ختامي للطلاقة',
                'raw_word' => 'مفردة تقويم ودعم ختامي للطلاقة',
                'example_sentence' => 'رصيد لغوي حول تقويم ودعم ختامي للطلاقة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM4_S1/image33.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم ودعم ختامي للطلاقة',
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
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P5_SEM4_S1/image40.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
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
