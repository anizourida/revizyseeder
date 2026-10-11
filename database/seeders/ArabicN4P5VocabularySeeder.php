<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN4P5VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N4, Period P5 (الوحدة 5: الغابة والبيئة الطبيعية وحمايتها).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: جزم المضارع الصحيح بـ (لم، لا الناهية)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ جزم المضارع الصحيح بـ',
                'clean_word' => 'تَعَلُّمُ جزم المضارع الصحيح بـ',
                'raw_word' => 'تعلم جزم المضارع الصحيح ب',
                'example_sentence' => 'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM1_S1/image76.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ جزم المضارع الصحيح بـ',
                'clean_word' => 'مُفْرَدَةُ جزم المضارع الصحيح بـ',
                'raw_word' => 'مفردة جزم المضارع الصحيح ب',
                'example_sentence' => 'رصيد لغوي حول جزم المضارع الصحيح بـ (لم، لا الناهية)',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM1_S1/image78.png',
                'linked_sentences' => [
                    'رصيد لغوي حول جزم المضارع الصحيح بـ (لم، لا الناهية)',
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
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM1_S1/image79.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: نصب المضارع بأن ولن
            // ==========================================
            [
                'word' => 'تَعَلُّمُ نصب المضارع بأن ولن',
                'clean_word' => 'تَعَلُّمُ نصب المضارع بأن ولن',
                'raw_word' => 'تعلم نصب المضارع بأن ولن',
                'example_sentence' => 'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM2_S1/image23.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ نصب المضارع بأن ولن',
                'clean_word' => 'مُفْرَدَةُ نصب المضارع بأن ولن',
                'raw_word' => 'مفردة نصب المضارع بأن ولن',
                'example_sentence' => 'رصيد لغوي حول نصب المضارع بأن ولن',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM2_S1/image65.png',
                'linked_sentences' => [
                    'رصيد لغوي حول نصب المضارع بأن ولن',
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
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الحال المفردة وتطابقها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الحال المفردة وتطابقها',
                'clean_word' => 'تَعَلُّمُ الحال المفردة وتطابقها',
                'raw_word' => 'تعلم الحال المفردة وتطابقها',
                'example_sentence' => 'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM3_S1/image74.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الحال المفردة وتطابقها',
                'clean_word' => 'مُفْرَدَةُ الحال المفردة وتطابقها',
                'raw_word' => 'مفردة الحال المفردة وتطابقها',
                'example_sentence' => 'رصيد لغوي حول الحال المفردة وتطابقها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM3_S1/image75.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الحال المفردة وتطابقها',
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
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM3_S1/image76.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: تقويم ختامي للحصيلة السنوية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم ختامي للحصيلة السنوية',
                'clean_word' => 'تَعَلُّمُ تقويم ختامي للحصيلة السنوية',
                'raw_word' => 'تعلم تقويم ختامي للحصيلة السنوية',
                'example_sentence' => 'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM4_S1/image21.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الغابة والبيئة الطبيعية وحمايتها',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم ختامي للحصيلة السنوية',
                'clean_word' => 'مُفْرَدَةُ تقويم ختامي للحصيلة السنوية',
                'raw_word' => 'مفردة تقويم ختامي للحصيلة السنوية',
                'example_sentence' => 'رصيد لغوي حول تقويم ختامي للحصيلة السنوية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM4_S1/image58.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم ختامي للحصيلة السنوية',
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
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P5_SEM4_S1/image13.png',
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
