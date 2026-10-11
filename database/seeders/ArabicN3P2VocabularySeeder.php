<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN3P2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N3, Period P2 (الوحدة 2: الحياة التعاونية والعمل الجماعي التطوعي).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: النواسخ الفعلية: كان، صار، أصبح
            // ==========================================
            [
                'word' => 'تَعَلُّمُ النواسخ الفعلية: كان، صار، أصبح',
                'clean_word' => 'تَعَلُّمُ النواسخ الفعلية: كان، صار، أصبح',
                'raw_word' => 'تعلم النواسخ الفعلية: كان، صار، أصبح',
                'example_sentence' => 'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM1_S1/image46.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ النواسخ الفعلية: كان، صار، أصبح',
                'clean_word' => 'مُفْرَدَةُ النواسخ الفعلية: كان، صار، أصبح',
                'raw_word' => 'مفردة النواسخ الفعلية: كان، صار، أصبح',
                'example_sentence' => 'رصيد لغوي حول النواسخ الفعلية: كان، صار، أصبح',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM1_S1/image47.png',
                'linked_sentences' => [
                    'رصيد لغوي حول النواسخ الفعلية: كان، صار، أصبح',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 1',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM1_S1/image48.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الفعل الصحيح: أنواعه المبسطة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفعل الصحيح: أنواعه المبسطة',
                'clean_word' => 'تَعَلُّمُ الفعل الصحيح: أنواعه المبسطة',
                'raw_word' => 'تعلم الفعل الصحيح: أنواعه المبسطة',
                'example_sentence' => 'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM2_S1/image44.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفعل الصحيح: أنواعه المبسطة',
                'clean_word' => 'مُفْرَدَةُ الفعل الصحيح: أنواعه المبسطة',
                'raw_word' => 'مفردة الفعل الصحيح: أنواعه المبسطة',
                'example_sentence' => 'رصيد لغوي حول الفعل الصحيح: أنواعه المبسطة',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM2_S1/image45.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفعل الصحيح: أنواعه المبسطة',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 2',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: عناصر الجملة الاسمية مع الناسخ
            // ==========================================
            [
                'word' => 'تَعَلُّمُ عناصر الجملة الاسمية مع الناسخ',
                'clean_word' => 'تَعَلُّمُ عناصر الجملة الاسمية مع الناسخ',
                'raw_word' => 'تعلم عناصر الجملة الاسمية مع الناسخ',
                'example_sentence' => 'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM3_S1/image27.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ عناصر الجملة الاسمية مع الناسخ',
                'clean_word' => 'مُفْرَدَةُ عناصر الجملة الاسمية مع الناسخ',
                'raw_word' => 'مفردة عناصر الجملة الاسمية مع الناسخ',
                'example_sentence' => 'رصيد لغوي حول عناصر الجملة الاسمية مع الناسخ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM3_S1/image45.png',
                'linked_sentences' => [
                    'رصيد لغوي حول عناصر الجملة الاسمية مع الناسخ',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 3',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM3_S1/image46.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: أسلوب النهي والنداء في التعاون
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسلوب النهي والنداء في التعاون',
                'clean_word' => 'تَعَلُّمُ أسلوب النهي والنداء في التعاون',
                'raw_word' => 'تعلم أسلوب النهي والنداء في التعاون',
                'example_sentence' => 'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM4_S1/image46.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسلوب النهي والنداء في التعاون',
                'clean_word' => 'مُفْرَدَةُ أسلوب النهي والنداء في التعاون',
                'raw_word' => 'مفردة أسلوب النهي والنداء في التعاون',
                'example_sentence' => 'رصيد لغوي حول أسلوب النهي والنداء في التعاون',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM4_S1/image45.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسلوب النهي والنداء في التعاون',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 4',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM4_S1/image52.png',
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
                'example_sentence' => 'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM5_S1/image45.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'raw_word' => 'مفردة أسبوع الدعم والتركيب',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم والتركيب',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM5_S1/image46.png',
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
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM5_S1/image47.jpeg',
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
                'example_sentence' => 'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM6_S1/image37.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الحياة التعاونية والعمل الجماعي التطوعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم الوحدة',
                'raw_word' => 'مفردة تقويم الوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم الوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM6_S1/image20.jpg',
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
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P2_SEM6_S1/image22.png',
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
