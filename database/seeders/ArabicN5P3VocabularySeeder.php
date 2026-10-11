<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN5P3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N5, Period P3 (الوحدة 3: وسائل الاتصال والتواصل الرقمي والحديث).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: صياغة اسمي الفاعل والمفعول
            // ==========================================
            [
                'word' => 'تَعَلُّمُ صياغة اسمي الفاعل والمفعول',
                'clean_word' => 'تَعَلُّمُ صياغة اسمي الفاعل والمفعول',
                'raw_word' => 'تعلم صياغة اسمي الفاعل والمفعول',
                'example_sentence' => 'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM1_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ صياغة اسمي الفاعل والمفعول',
                'clean_word' => 'مُفْرَدَةُ صياغة اسمي الفاعل والمفعول',
                'raw_word' => 'مفردة صياغة اسمي الفاعل والمفعول',
                'example_sentence' => 'رصيد لغوي حول صياغة اسمي الفاعل والمفعول',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM1_S1/image41.png',
                'linked_sentences' => [
                    'رصيد لغوي حول صياغة اسمي الفاعل والمفعول',
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
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM1_S1/image15.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: المفعول فيه وحكمه الإعرابي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ المفعول فيه وحكمه الإعرابي',
                'clean_word' => 'تَعَلُّمُ المفعول فيه وحكمه الإعرابي',
                'raw_word' => 'تعلم المفعول فيه وحكمه الإعرابي',
                'example_sentence' => 'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM2_S1/image39.png',
                'linked_sentences' => [
                    'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ المفعول فيه وحكمه الإعرابي',
                'clean_word' => 'مُفْرَدَةُ المفعول فيه وحكمه الإعرابي',
                'raw_word' => 'مفردة المفعول فيه وحكمه الإعرابي',
                'example_sentence' => 'رصيد لغوي حول المفعول فيه وحكمه الإعرابي',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM2_S1/image40.png',
                'linked_sentences' => [
                    'رصيد لغوي حول المفعول فيه وحكمه الإعرابي',
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
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM2_S1/image14.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: نصب الفعل المضارع والجملة المؤولة
            // ==========================================
            [
                'word' => 'تَعَلُّمُ نصب الفعل المضارع والجملة المؤولة',
                'clean_word' => 'تَعَلُّمُ نصب الفعل المضارع والجملة المؤولة',
                'raw_word' => 'تعلم نصب الفعل المضارع والجملة المؤولة',
                'example_sentence' => 'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM3_S1/image81.png',
                'linked_sentences' => [
                    'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ نصب الفعل المضارع والجملة المؤولة',
                'clean_word' => 'مُفْرَدَةُ نصب الفعل المضارع والجملة المؤولة',
                'raw_word' => 'مفردة نصب الفعل المضارع والجملة المؤولة',
                'example_sentence' => 'رصيد لغوي حول نصب الفعل المضارع والجملة المؤولة',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM3_S1/image82.png',
                'linked_sentences' => [
                    'رصيد لغوي حول نصب الفعل المضارع والجملة المؤولة',
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
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM3_S1/image83.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: همزتا الوصل والقطع في الأفعال والأسماء
            // ==========================================
            [
                'word' => 'تَعَلُّمُ همزتا الوصل والقطع في الأفعال والأسماء',
                'clean_word' => 'تَعَلُّمُ همزتا الوصل والقطع في الأفعال والأسماء',
                'raw_word' => 'تعلم همزتا الوصل والقطع في الأفعال والأسماء',
                'example_sentence' => 'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM4_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ همزتا الوصل والقطع في الأفعال والأسماء',
                'clean_word' => 'مُفْرَدَةُ همزتا الوصل والقطع في الأفعال والأسماء',
                'raw_word' => 'مفردة همزتا الوصل والقطع في الأفعال والأسماء',
                'example_sentence' => 'رصيد لغوي حول همزتا الوصل والقطع في الأفعال والأسماء',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM4_S1/image48.jpeg',
                'linked_sentences' => [
                    'رصيد لغوي حول همزتا الوصل والقطع في الأفعال والأسماء',
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
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM4_S1/image49.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم الظواهر التركيبية والصرفية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ دعم الظواهر التركيبية والصرفية',
                'clean_word' => 'تَعَلُّمُ دعم الظواهر التركيبية والصرفية',
                'raw_word' => 'تعلم دعم الظواهر التركيبية والصرفية',
                'example_sentence' => 'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM5_S1/image14.png',
                'linked_sentences' => [
                    'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ دعم الظواهر التركيبية والصرفية',
                'clean_word' => 'مُفْرَدَةُ دعم الظواهر التركيبية والصرفية',
                'raw_word' => 'مفردة دعم الظواهر التركيبية والصرفية',
                'example_sentence' => 'رصيد لغوي حول دعم الظواهر التركيبية والصرفية',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM5_S1/image42.png',
                'linked_sentences' => [
                    'رصيد لغوي حول دعم الظواهر التركيبية والصرفية',
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
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM5_S1/image46.jpeg',
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
                'example_sentence' => 'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM6_S1/image30.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في وسائل الاتصال والتواصل الرقمي والحديث',
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
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM6_S1/image31.png',
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
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P3_SEM6_S1/image33.png',
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
