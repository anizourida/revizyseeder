<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN6P5VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N6, Period P5 (الوحدة 5: تحديات العالم المعاصر والسلم العالمي).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: أحكام العدد والمعدود من 1 إلى 99 وما بعدها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'clean_word' => 'تَعَلُّمُ أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'raw_word' => 'تعلم أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'example_sentence' => 'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM1_S1/image38.png',
                'linked_sentences' => [
                    'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'clean_word' => 'مُفْرَدَةُ أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'raw_word' => 'مفردة أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'example_sentence' => 'رصيد لغوي حول أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM1_S1/image39.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أحكام العدد والمعدود من 1 إلى 99 وما بعدها',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 1',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM1_S1/image40.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: جمع التكسير وصيغ منتهى الجموع
            // ==========================================
            [
                'word' => 'تَعَلُّمُ جمع التكسير وصيغ منتهى الجموع',
                'clean_word' => 'تَعَلُّمُ جمع التكسير وصيغ منتهى الجموع',
                'raw_word' => 'تعلم جمع التكسير وصيغ منتهى الجموع',
                'example_sentence' => 'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM2_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ جمع التكسير وصيغ منتهى الجموع',
                'clean_word' => 'مُفْرَدَةُ جمع التكسير وصيغ منتهى الجموع',
                'raw_word' => 'مفردة جمع التكسير وصيغ منتهى الجموع',
                'example_sentence' => 'رصيد لغوي حول جمع التكسير وصيغ منتهى الجموع',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM2_S1/image41.png',
                'linked_sentences' => [
                    'رصيد لغوي حول جمع التكسير وصيغ منتهى الجموع',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 2',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM2_S1/image42.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الاستثناء الشامل والإعراب التوليفي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الاستثناء الشامل والإعراب التوليفي',
                'clean_word' => 'تَعَلُّمُ الاستثناء الشامل والإعراب التوليفي',
                'raw_word' => 'تعلم الاستثناء الشامل والإعراب التوليفي',
                'example_sentence' => 'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM3_S1/image39.png',
                'linked_sentences' => [
                    'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الاستثناء الشامل والإعراب التوليفي',
                'clean_word' => 'مُفْرَدَةُ الاستثناء الشامل والإعراب التوليفي',
                'raw_word' => 'مفردة الاستثناء الشامل والإعراب التوليفي',
                'example_sentence' => 'رصيد لغوي حول الاستثناء الشامل والإعراب التوليفي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM3_S1/image40.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الاستثناء الشامل والإعراب التوليفي',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 3',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM3_S1/image41.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: روائز التقويم الإجمالي لنهاية المرحلة الابتدائية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'clean_word' => 'تَعَلُّمُ روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'raw_word' => 'تعلم روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'example_sentence' => 'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM4_S1/image20.png',
                'linked_sentences' => [
                    'مفهوم معرفي في تحديات العالم المعاصر والسلم العالمي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'clean_word' => 'مُفْرَدَةُ روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'raw_word' => 'مفردة روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'example_sentence' => 'رصيد لغوي حول روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM4_S1/image19.jpg',
                'linked_sentences' => [
                    'رصيد لغوي حول روائز التقويم الإجمالي لنهاية المرحلة الابتدائية',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 4',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P5_SEM4_S1/image22.png',
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
