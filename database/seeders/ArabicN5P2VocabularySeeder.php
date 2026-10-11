<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN5P2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N5, Period P2 (الوحدة 2: الواجبات والحقوق والمواطنة الفاعلة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: الفاعل ونائب الفاعل وصياغة المجهول
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفاعل ونائب الفاعل وصياغة المجهول',
                'clean_word' => 'تَعَلُّمُ الفاعل ونائب الفاعل وصياغة المجهول',
                'raw_word' => 'تعلم الفاعل ونائب الفاعل وصياغة المجهول',
                'example_sentence' => 'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM1_S1/image74.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفاعل ونائب الفاعل وصياغة المجهول',
                'clean_word' => 'مُفْرَدَةُ الفاعل ونائب الفاعل وصياغة المجهول',
                'raw_word' => 'مفردة الفاعل ونائب الفاعل وصياغة المجهول',
                'example_sentence' => 'رصيد لغوي حول الفاعل ونائب الفاعل وصياغة المجهول',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM1_S1/image77.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفاعل ونائب الفاعل وصياغة المجهول',
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
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM1_S1/image78.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: المقصور والمنقوص والممدود وتثنيتها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المقصور والمنقوص والممدود وتثنيتها',
                'clean_word' => 'تَعَلُّمُ المقصور والمنقوص والممدود وتثنيتها',
                'raw_word' => 'تعلم المقصور والمنقوص والممدود وتثنيتها',
                'example_sentence' => 'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM2_S1/image33.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المقصور والمنقوص والممدود وتثنيتها',
                'clean_word' => 'مُفْرَدَةُ المقصور والمنقوص والممدود وتثنيتها',
                'raw_word' => 'مفردة المقصور والمنقوص والممدود وتثنيتها',
                'example_sentence' => 'رصيد لغوي حول المقصور والمنقوص والممدود وتثنيتها',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM2_S1/image31.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المقصور والمنقوص والممدود وتثنيتها',
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
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM2_S1/image30.jpg',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: المفعول لأجله وشروطه
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المفعول لأجله وشروطه',
                'clean_word' => 'تَعَلُّمُ المفعول لأجله وشروطه',
                'raw_word' => 'تعلم المفعول لأجله وشروطه',
                'example_sentence' => 'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM3_S1/image84.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المفعول لأجله وشروطه',
                'clean_word' => 'مُفْرَدَةُ المفعول لأجله وشروطه',
                'raw_word' => 'مفردة المفعول لأجله وشروطه',
                'example_sentence' => 'رصيد لغوي حول المفعول لأجله وشروطه',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM3_S1/image85.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المفعول لأجله وشروطه',
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
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM3_S1/image86.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: تنوين المقصور والمنقوص والممدود
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تنوين المقصور والمنقوص والممدود',
                'clean_word' => 'تَعَلُّمُ تنوين المقصور والمنقوص والممدود',
                'raw_word' => 'تعلم تنوين المقصور والمنقوص والممدود',
                'example_sentence' => 'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM4_S1/image32.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تنوين المقصور والمنقوص والممدود',
                'clean_word' => 'مُفْرَدَةُ تنوين المقصور والمنقوص والممدود',
                'raw_word' => 'مفردة تنوين المقصور والمنقوص والممدود',
                'example_sentence' => 'رصيد لغوي حول تنوين المقصور والمنقوص والممدود',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM4_S1/image39.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تنوين المقصور والمنقوص والممدود',
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
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM4_S1/image40.png',
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
                'example_sentence' => 'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM5_S1/image39.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
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
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM5_S1/image41.png',
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
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM5_S1/image14.png',
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
                'example_sentence' => 'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM6_S1/image29.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في الواجبات والحقوق والمواطنة الفاعلة',
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
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM6_S1/image31.png',
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
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P2_SEM6_S1/image32.png',
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
