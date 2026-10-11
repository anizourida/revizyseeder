<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN4P2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N4, Period P2 (الوحدة 2: المواطنة والسلوك المدني والمشاركة المجتمعية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: الفعل المعتل: المثال والأجوف والناقص
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفعل المعتل: المثال والأجوف والناقص',
                'clean_word' => 'تَعَلُّمُ الفعل المعتل: المثال والأجوف والناقص',
                'raw_word' => 'تعلم الفعل المعتل: المثال والأجوف والناقص',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM1_S1/image44.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفعل المعتل: المثال والأجوف والناقص',
                'clean_word' => 'مُفْرَدَةُ الفعل المعتل: المثال والأجوف والناقص',
                'raw_word' => 'مفردة الفعل المعتل: المثال والأجوف والناقص',
                'example_sentence' => 'رصيد لغوي حول الفعل المعتل: المثال والأجوف والناقص',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM1_S1/image66.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفعل المعتل: المثال والأجوف والناقص',
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
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM1_S1/image67.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: النواسخ الحرفية ومعانيها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ النواسخ الحرفية ومعانيها',
                'clean_word' => 'تَعَلُّمُ النواسخ الحرفية ومعانيها',
                'raw_word' => 'تعلم النواسخ الحرفية ومعانيها',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ النواسخ الحرفية ومعانيها',
                'clean_word' => 'مُفْرَدَةُ النواسخ الحرفية ومعانيها',
                'raw_word' => 'مفردة النواسخ الحرفية ومعانيها',
                'example_sentence' => 'رصيد لغوي حول النواسخ الحرفية ومعانيها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM2_S1/image45.png',
                'linked_sentences' => [
                    'رصيد لغوي حول النواسخ الحرفية ومعانيها',
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
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM2_S1/image64.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الفاعل ظاهر وضمير
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الفاعل ظاهر وضمير',
                'clean_word' => 'تَعَلُّمُ الفاعل ظاهر وضمير',
                'raw_word' => 'تعلم الفاعل ظاهر وضمير',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM3_S1/image49.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الفاعل ظاهر وضمير',
                'clean_word' => 'مُفْرَدَةُ الفاعل ظاهر وضمير',
                'raw_word' => 'مفردة الفاعل ظاهر وضمير',
                'example_sentence' => 'رصيد لغوي حول الفاعل ظاهر وضمير',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM3_S1/image48.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الفاعل ظاهر وضمير',
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
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM3_S1/image47.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الهمزة المتوسطة على الألف والواو
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الهمزة المتوسطة على الألف والواو',
                'clean_word' => 'تَعَلُّمُ الهمزة المتوسطة على الألف والواو',
                'raw_word' => 'تعلم الهمزة المتوسطة على الألف والواو',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM4_S1/image58.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الهمزة المتوسطة على الألف والواو',
                'clean_word' => 'مُفْرَدَةُ الهمزة المتوسطة على الألف والواو',
                'raw_word' => 'مفردة الهمزة المتوسطة على الألف والواو',
                'example_sentence' => 'رصيد لغوي حول الهمزة المتوسطة على الألف والواو',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM4_S1/image56.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الهمزة المتوسطة على الألف والواو',
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
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM4_S1/image68.png',
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
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM5_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'raw_word' => 'مفردة أسبوع الدعم والتركيب',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم والتركيب',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM5_S1/image42.png',
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
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM5_S1/image43.png',
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
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM6_S1/image33.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والمشاركة المجتمعية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم الوحدة',
                'raw_word' => 'مفردة تقويم الوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم الوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM6_S1/image34.png',
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
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P2_SEM6_S1/image14.png',
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
