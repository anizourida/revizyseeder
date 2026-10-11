<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN6P2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N6, Period P2 (الوحدة 2: المواطنة والسلوك المدني والعيش المشترك).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: إعراب الأفعال الخمسة بثبوت النون وحذفها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'clean_word' => 'تَعَلُّمُ إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'raw_word' => 'تعلم إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM1_S1/image56.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'clean_word' => 'مُفْرَدَةُ إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'raw_word' => 'مفردة إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'example_sentence' => 'رصيد لغوي حول إعراب الأفعال الخمسة بثبوت النون وحذفها',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM1_S1/image57.png',
                'linked_sentences' => [
                    'رصيد لغوي حول إعراب الأفعال الخمسة بثبوت النون وحذفها',
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
                'period' => 'P2',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P2_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM1_S1/image58.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: إعراب المثنى وجمع المذكر والمؤنث السالم
            // ==========================================
            [
                'word' => 'تَعَلُّمُ إعراب المثنى وجمع المذكر والمؤنث السالم',
                'clean_word' => 'تَعَلُّمُ إعراب المثنى وجمع المذكر والمؤنث السالم',
                'raw_word' => 'تعلم إعراب المثنى وجمع المذكر والمؤنث السالم',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM2_S1/image39.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ إعراب المثنى وجمع المذكر والمؤنث السالم',
                'clean_word' => 'مُفْرَدَةُ إعراب المثنى وجمع المذكر والمؤنث السالم',
                'raw_word' => 'مفردة إعراب المثنى وجمع المذكر والمؤنث السالم',
                'example_sentence' => 'رصيد لغوي حول إعراب المثنى وجمع المذكر والمؤنث السالم',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM2_S1/image40.png',
                'linked_sentences' => [
                    'رصيد لغوي حول إعراب المثنى وجمع المذكر والمؤنث السالم',
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
                'period' => 'P2',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P2_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM2_S1/image41.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: اسما الزمان والمكان وصيغهما
            // ==========================================
            [
                'word' => 'تَعَلُّمُ اسما الزمان والمكان وصيغهما',
                'clean_word' => 'تَعَلُّمُ اسما الزمان والمكان وصيغهما',
                'raw_word' => 'تعلم اسما الزمان والمكان وصيغهما',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM3_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ اسما الزمان والمكان وصيغهما',
                'clean_word' => 'مُفْرَدَةُ اسما الزمان والمكان وصيغهما',
                'raw_word' => 'مفردة اسما الزمان والمكان وصيغهما',
                'example_sentence' => 'رصيد لغوي حول اسما الزمان والمكان وصيغهما',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM3_S1/image39.png',
                'linked_sentences' => [
                    'رصيد لغوي حول اسما الزمان والمكان وصيغهما',
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
                'period' => 'P2',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P2_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM3_S1/image38.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ابن وثبوت ألفها وحذفها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ ابن وثبوت ألفها وحذفها',
                'clean_word' => 'تَعَلُّمُ ابن وثبوت ألفها وحذفها',
                'raw_word' => 'تعلم ابن وثبوت ألفها وحذفها',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM4_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ ابن وثبوت ألفها وحذفها',
                'clean_word' => 'مُفْرَدَةُ ابن وثبوت ألفها وحذفها',
                'raw_word' => 'مفردة ابن وثبوت ألفها وحذفها',
                'example_sentence' => 'رصيد لغوي حول ابن وثبوت ألفها وحذفها',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM4_S1/image4.png',
                'linked_sentences' => [
                    'رصيد لغوي حول ابن وثبوت ألفها وحذفها',
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
                'period' => 'P2',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P2_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM4_S1/image3.png',
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
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM5_S1/image36.png',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب',
                'raw_word' => 'مفردة أسبوع الدعم والتركيب',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم والتركيب',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM5_S1/image37.png',
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
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P2_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM5_S1/image38.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 5',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم ختامي للوحدة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم ختامي للوحدة',
                'clean_word' => 'تَعَلُّمُ تقويم ختامي للوحدة',
                'raw_word' => 'تعلم تقويم ختامي للوحدة',
                'example_sentence' => 'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM6_S1/image19.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في المواطنة والسلوك المدني والعيش المشترك',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم ختامي للوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم ختامي للوحدة',
                'raw_word' => 'مفردة تقويم ختامي للوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم ختامي للوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM6_S1/image20.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم ختامي للوحدة',
                ],
            ],
            [
                'word' => 'تَواصُلٌ شَفَهِيٌّ',
                'clean_word' => 'تَواصُلٌ شَفَهِيٌّ',
                'raw_word' => 'تواصل شفهي',
                'example_sentence' => 'مهارة تواصلية وتعبيرية للأسبوع 6',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P2',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P2_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P2_SEM6_S1/image21.png',
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
