<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN5P5VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N5, Period P5 (الوحدة 5: البيئة والتنمية المستدامة والطاقات المتجددة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: المستثنى بـ (إلا، غير، سوى)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المستثنى بـ',
                'clean_word' => 'تَعَلُّمُ المستثنى بـ',
                'raw_word' => 'تعلم المستثنى ب',
                'example_sentence' => 'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM1_S1/image50.png',
                'linked_sentences' => [
                    'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المستثنى بـ',
                'clean_word' => 'مُفْرَدَةُ المستثنى بـ',
                'raw_word' => 'مفردة المستثنى ب',
                'example_sentence' => 'رصيد لغوي حول المستثنى بـ (إلا، غير، سوى)',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM1_S1/image49.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المستثنى بـ (إلا، غير، سوى)',
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
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM1_S1/image58.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: المنادى وحكمه الإعرابي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المنادى وحكمه الإعرابي',
                'clean_word' => 'تَعَلُّمُ المنادى وحكمه الإعرابي',
                'raw_word' => 'تعلم المنادى وحكمه الإعرابي',
                'example_sentence' => 'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM2_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المنادى وحكمه الإعرابي',
                'clean_word' => 'مُفْرَدَةُ المنادى وحكمه الإعرابي',
                'raw_word' => 'مفردة المنادى وحكمه الإعرابي',
                'example_sentence' => 'رصيد لغوي حول المنادى وحكمه الإعرابي',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM2_S1/image44.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المنادى وحكمه الإعرابي',
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
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM2_S1/image50.jpeg',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: العدد والمعدود (المفرد والمركب)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ العدد والمعدود',
                'clean_word' => 'تَعَلُّمُ العدد والمعدود',
                'raw_word' => 'تعلم العدد والمعدود',
                'example_sentence' => 'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM3_S1/image69.png',
                'linked_sentences' => [
                    'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ العدد والمعدود',
                'clean_word' => 'مُفْرَدَةُ العدد والمعدود',
                'raw_word' => 'مفردة العدد والمعدود',
                'example_sentence' => 'رصيد لغوي حول العدد والمعدود (المفرد والمركب)',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM3_S1/image70.png',
                'linked_sentences' => [
                    'رصيد لغوي حول العدد والمعدود (المفرد والمركب)',
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
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM3_S1/image49.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: تقويم سنوي شامل للظواهر اللغوية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم سنوي شامل للظواهر اللغوية',
                'clean_word' => 'تَعَلُّمُ تقويم سنوي شامل للظواهر اللغوية',
                'raw_word' => 'تعلم تقويم سنوي شامل للظواهر اللغوية',
                'example_sentence' => 'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM4_S1/image21.png',
                'linked_sentences' => [
                    'مفهوم معرفي في البيئة والتنمية المستدامة والطاقات المتجددة',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم سنوي شامل للظواهر اللغوية',
                'clean_word' => 'مُفْرَدَةُ تقويم سنوي شامل للظواهر اللغوية',
                'raw_word' => 'مفردة تقويم سنوي شامل للظواهر اللغوية',
                'example_sentence' => 'رصيد لغوي حول تقويم سنوي شامل للظواهر اللغوية',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM4_S1/image20.jpg',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم سنوي شامل للظواهر اللغوية',
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
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P5_SEM4_S1/image23.png',
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
