<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN6P3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N6, Period P3 (الوحدة 3: العلم والتكنولوجيا والذكاء الاصطناعي).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: نصب المضارع والجملة المؤولة بأدوات النصب
            // ==========================================
            [
                'word' => 'تَعَلُّمُ نصب المضارع والجملة المؤولة بأدوات النصب',
                'clean_word' => 'تَعَلُّمُ نصب المضارع والجملة المؤولة بأدوات النصب',
                'raw_word' => 'تعلم نصب المضارع والجملة المؤولة بأدوات النصب',
                'example_sentence' => 'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM1_S1/image33.png',
                'linked_sentences' => [
                    'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ نصب المضارع والجملة المؤولة بأدوات النصب',
                'clean_word' => 'مُفْرَدَةُ نصب المضارع والجملة المؤولة بأدوات النصب',
                'raw_word' => 'مفردة نصب المضارع والجملة المؤولة بأدوات النصب',
                'example_sentence' => 'رصيد لغوي حول نصب المضارع والجملة المؤولة بأدوات النصب',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM1_S1/image32.png',
                'linked_sentences' => [
                    'رصيد لغوي حول نصب المضارع والجملة المؤولة بأدوات النصب',
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
                'period' => 'P3',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P3_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM1_S1/image31.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط
            // ==========================================
            [
                'word' => 'تَعَلُّمُ جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'clean_word' => 'تَعَلُّمُ جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'raw_word' => 'تعلم جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'example_sentence' => 'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM2_S1/image4.png',
                'linked_sentences' => [
                    'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'clean_word' => 'مُفْرَدَةُ جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'raw_word' => 'مفردة جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'example_sentence' => 'رصيد لغوي حول جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM2_S1/image3.png',
                'linked_sentences' => [
                    'رصيد لغوي حول جزم المضارع الصحيح وجزم فعلين بأسلوب الشرط',
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
                'period' => 'P3',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P3_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM2_S1/image2.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: اسم التفضيل وشروطه الصرفية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ اسم التفضيل وشروطه الصرفية',
                'clean_word' => 'تَعَلُّمُ اسم التفضيل وشروطه الصرفية',
                'raw_word' => 'تعلم اسم التفضيل وشروطه الصرفية',
                'example_sentence' => 'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM3_S1/image46.png',
                'linked_sentences' => [
                    'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ اسم التفضيل وشروطه الصرفية',
                'clean_word' => 'مُفْرَدَةُ اسم التفضيل وشروطه الصرفية',
                'raw_word' => 'مفردة اسم التفضيل وشروطه الصرفية',
                'example_sentence' => 'رصيد لغوي حول اسم التفضيل وشروطه الصرفية',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM3_S1/image47.png',
                'linked_sentences' => [
                    'رصيد لغوي حول اسم التفضيل وشروطه الصرفية',
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
                'period' => 'P3',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P3_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM3_S1/image48.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الألف اللينة مقصورة وممدودة في الأفعال والحروف
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'clean_word' => 'تَعَلُّمُ الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'raw_word' => 'تعلم الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'example_sentence' => 'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM4_S1/image4.png',
                'linked_sentences' => [
                    'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'clean_word' => 'مُفْرَدَةُ الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'raw_word' => 'مفردة الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'example_sentence' => 'رصيد لغوي حول الألف اللينة مقصورة وممدودة في الأفعال والحروف',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM4_S1/image3.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الألف اللينة مقصورة وممدودة في الأفعال والحروف',
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
                'period' => 'P3',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P3_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM4_S1/image2.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم الظواهر اللغوية الإعدادية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ دعم الظواهر اللغوية الإعدادية',
                'clean_word' => 'تَعَلُّمُ دعم الظواهر اللغوية الإعدادية',
                'raw_word' => 'تعلم دعم الظواهر اللغوية الإعدادية',
                'example_sentence' => 'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM5_S1/image34.png',
                'linked_sentences' => [
                    'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ دعم الظواهر اللغوية الإعدادية',
                'clean_word' => 'مُفْرَدَةُ دعم الظواهر اللغوية الإعدادية',
                'raw_word' => 'مفردة دعم الظواهر اللغوية الإعدادية',
                'example_sentence' => 'رصيد لغوي حول دعم الظواهر اللغوية الإعدادية',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM5_S1/image35.png',
                'linked_sentences' => [
                    'رصيد لغوي حول دعم الظواهر اللغوية الإعدادية',
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
                'period' => 'P3',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P3_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM5_S1/image19.jpg',
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
                'example_sentence' => 'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM6_S1/image13.png',
                'linked_sentences' => [
                    'مفهوم معرفي في العلم والتكنولوجيا والذكاء الاصطناعي',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم الوحدة',
                'clean_word' => 'مُفْرَدَةُ تقويم الوحدة',
                'raw_word' => 'مفردة تقويم الوحدة',
                'example_sentence' => 'رصيد لغوي حول تقويم الوحدة',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM6_S1/image12.png',
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
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P3',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P3_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P3_SEM6_S1/image11.png',
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
