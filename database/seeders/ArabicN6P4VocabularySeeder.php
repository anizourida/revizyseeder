<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN6P4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N6, Period P4 (الوحدة 4: الطاقة في حياتنا ومستقبل الكوكب الأخضر).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: التوكيد: اللفظي والمعنوي بألفاظه
            // ==========================================
            [
                'word' => 'تَعَلُّمُ التوكيد: اللفظي والمعنوي بألفاظه',
                'clean_word' => 'تَعَلُّمُ التوكيد: اللفظي والمعنوي بألفاظه',
                'raw_word' => 'تعلم التوكيد: اللفظي والمعنوي بألفاظه',
                'example_sentence' => 'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM1_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ التوكيد: اللفظي والمعنوي بألفاظه',
                'clean_word' => 'مُفْرَدَةُ التوكيد: اللفظي والمعنوي بألفاظه',
                'raw_word' => 'مفردة التوكيد: اللفظي والمعنوي بألفاظه',
                'example_sentence' => 'رصيد لغوي حول التوكيد: اللفظي والمعنوي بألفاظه',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM1_S1/image39.png',
                'linked_sentences' => [
                    'رصيد لغوي حول التوكيد: اللفظي والمعنوي بألفاظه',
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
                'period' => 'P4',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P4_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM1_S1/image38.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: التمييز الملفوظ والملحوظ وإعرابهما
            // ==========================================
            [
                'word' => 'تَعَلُّمُ التمييز الملفوظ والملحوظ وإعرابهما',
                'clean_word' => 'تَعَلُّمُ التمييز الملفوظ والملحوظ وإعرابهما',
                'raw_word' => 'تعلم التمييز الملفوظ والملحوظ وإعرابهما',
                'example_sentence' => 'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM2_S1/image40.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ التمييز الملفوظ والملحوظ وإعرابهما',
                'clean_word' => 'مُفْرَدَةُ التمييز الملفوظ والملحوظ وإعرابهما',
                'raw_word' => 'مفردة التمييز الملفوظ والملحوظ وإعرابهما',
                'example_sentence' => 'رصيد لغوي حول التمييز الملفوظ والملحوظ وإعرابهما',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM2_S1/image41.png',
                'linked_sentences' => [
                    'رصيد لغوي حول التمييز الملفوظ والملحوظ وإعرابهما',
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
                'period' => 'P4',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P4_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM2_S1/image42.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: البدل وأنواعه (المطابق، البعض، الاشتمال)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ البدل وأنواعه',
                'clean_word' => 'تَعَلُّمُ البدل وأنواعه',
                'raw_word' => 'تعلم البدل وأنواعه',
                'example_sentence' => 'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM3_S1/image38.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ البدل وأنواعه',
                'clean_word' => 'مُفْرَدَةُ البدل وأنواعه',
                'raw_word' => 'مفردة البدل وأنواعه',
                'example_sentence' => 'رصيد لغوي حول البدل وأنواعه (المطابق، البعض، الاشتمال)',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM3_S1/image37.png',
                'linked_sentences' => [
                    'رصيد لغوي حول البدل وأنواعه (المطابق، البعض، الاشتمال)',
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
                'period' => 'P4',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P4_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM3_S1/image36.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الهمزة المتوسطة والمتطرفة في حالات الإعراب
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'clean_word' => 'تَعَلُّمُ الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'raw_word' => 'تعلم الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'example_sentence' => 'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM4_S1/image4.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'clean_word' => 'مُفْرَدَةُ الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'raw_word' => 'مفردة الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'example_sentence' => 'رصيد لغوي حول الهمزة المتوسطة والمتطرفة في حالات الإعراب',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM4_S1/image3.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الهمزة المتوسطة والمتطرفة في حالات الإعراب',
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
                'period' => 'P4',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P4_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM4_S1/image2.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 4',
                ],
            ],
            // ==========================================
            // الأسبوع 5: أسبوع الدعم والتركيب السنوي
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسبوع الدعم والتركيب السنوي',
                'clean_word' => 'تَعَلُّمُ أسبوع الدعم والتركيب السنوي',
                'raw_word' => 'تعلم أسبوع الدعم والتركيب السنوي',
                'example_sentence' => 'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM5_S1/image19.jpg',
                'linked_sentences' => [
                    'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب السنوي',
                'clean_word' => 'مُفْرَدَةُ أسبوع الدعم والتركيب السنوي',
                'raw_word' => 'مفردة أسبوع الدعم والتركيب السنوي',
                'example_sentence' => 'رصيد لغوي حول أسبوع الدعم والتركيب السنوي',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM5_S1/image20.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسبوع الدعم والتركيب السنوي',
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
                'period' => 'P4',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P4_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM5_S1/image22.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 5',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم شامل
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم شامل',
                'clean_word' => 'تَعَلُّمُ تقويم شامل',
                'raw_word' => 'تعلم تقويم شامل',
                'example_sentence' => 'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM6_S1/image28.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الطاقة في حياتنا ومستقبل الكوكب الأخضر',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم شامل',
                'clean_word' => 'مُفْرَدَةُ تقويم شامل',
                'raw_word' => 'مفردة تقويم شامل',
                'example_sentence' => 'رصيد لغوي حول تقويم شامل',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM6_S1/image29.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم شامل',
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
                'period' => 'P4',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P4_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P4_SEM6_S1/image30.png',
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
