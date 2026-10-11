<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN2P2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N2, Period P2 (الوحدة 2: الحفلات والأعياد والمناسبات السعيدة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: ج / ح / خ (الجيم والحاء والخاء)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ج / ح / خ',
                'clean_word' => 'تَعَلُّمُ ج / ح / خ',
                'raw_word' => 'تعلم ج / ح / خ',
                'example_sentence' => 'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM1_S1/image60.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ج / ح / خ',
                'clean_word' => 'مُفْرَدَةُ ج / ح / خ',
                'raw_word' => 'مفردة ج / ح / خ',
                'example_sentence' => 'رصيد لغوي حول ج / ح / خ (الجيم والحاء والخاء)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM1_S1/image79.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ج / ح / خ (الجيم والحاء والخاء)',
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
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM1_S1/image71.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: ع / غ (العين والغين)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ع / غ',
                'clean_word' => 'تَعَلُّمُ ع / غ',
                'raw_word' => 'تعلم ع / غ',
                'example_sentence' => 'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM2_S1/image75.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ع / غ',
                'clean_word' => 'مُفْرَدَةُ ع / غ',
                'raw_word' => 'مفردة ع / غ',
                'example_sentence' => 'رصيد لغوي حول ع / غ (العين والغين)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM2_S1/image67.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ع / غ (العين والغين)',
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
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM2_S1/image56.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: ف / ق (الفاء والقاف)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ف / ق',
                'clean_word' => 'تَعَلُّمُ ف / ق',
                'raw_word' => 'تعلم ف / ق',
                'example_sentence' => 'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM3_S1/image58.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ف / ق',
                'clean_word' => 'مُفْرَدَةُ ف / ق',
                'raw_word' => 'مفردة ف / ق',
                'example_sentence' => 'رصيد لغوي حول ف / ق (الفاء والقاف)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM3_S1/image59.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ف / ق (الفاء والقاف)',
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
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM3_S1/image67.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ك / ل (الكاف واللام)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ك / ل',
                'clean_word' => 'تَعَلُّمُ ك / ل',
                'raw_word' => 'تعلم ك / ل',
                'example_sentence' => 'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM4_S1/image57.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ك / ل',
                'clean_word' => 'مُفْرَدَةُ ك / ل',
                'raw_word' => 'مفردة ك / ل',
                'example_sentence' => 'رصيد لغوي حول ك / ل (الكاف واللام)',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM4_S1/image58.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ك / ل (الكاف واللام)',
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
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM4_S1/image59.png',
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
                'example_sentence' => 'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM5_S1/image11.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'raw_word' => 'مفردة أسبوع الدعم والتركيب',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم والتركيب',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM5_S1/image50.png',
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
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM5_S1/image61.png',
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
                'example_sentence' => 'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM6_S1/image35.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحفلات والأعياد والمناسبات السعيدة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الحصيلة',
                'clean_word' => 'مُفْرَدَةُ تقويم الحصيلة',
                'raw_word' => 'مفردة تقويم الحصيلة',
                'example_sentence' => 'رصيد لغوي حول تقويم الحصيلة',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM6_S1/image36.png',
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
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P2_SEM6_S1/image37.png',
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
