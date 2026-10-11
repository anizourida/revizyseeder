<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN3P5VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N3, Period P5 (الوحدة 5: الماء والحياة والبيئة المائية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: أسلوب التعجب (ما أفعل!)
            // ==========================================
            [
                'word' => 'تَعَلُّمُ أسلوب التعجب',
                'clean_word' => 'تَعَلُّمُ أسلوب التعجب',
                'raw_word' => 'تعلم أسلوب التعجب',
                'example_sentence' => 'مفهوم معرفي في الماء والحياة والبيئة المائية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM1_S1/image56.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الماء والحياة والبيئة المائية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ أسلوب التعجب',
                'clean_word' => 'مُفْرَدَةُ أسلوب التعجب',
                'raw_word' => 'مفردة أسلوب التعجب',
                'example_sentence' => 'رصيد لغوي حول أسلوب التعجب (ما أفعل!)',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM1_S1/image58.png',
                'linked_sentences' => [
                    'رصيد لغوي حول أسلوب التعجب (ما أفعل!)',
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
                'period' => 'P5',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P5_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM1_S1/image59.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 1',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الجملة الاستفهامية وأدواتها
            // ==========================================
            [
                'word' => 'تَعَلُّمُ الجملة الاستفهامية وأدواتها',
                'clean_word' => 'تَعَلُّمُ الجملة الاستفهامية وأدواتها',
                'raw_word' => 'تعلم الجملة الاستفهامية وأدواتها',
                'example_sentence' => 'مفهوم معرفي في الماء والحياة والبيئة المائية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM2_S1/image42.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الماء والحياة والبيئة المائية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ الجملة الاستفهامية وأدواتها',
                'clean_word' => 'مُفْرَدَةُ الجملة الاستفهامية وأدواتها',
                'raw_word' => 'مفردة الجملة الاستفهامية وأدواتها',
                'example_sentence' => 'رصيد لغوي حول الجملة الاستفهامية وأدواتها',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM2_S1/image43.png',
                'linked_sentences' => [
                    'رصيد لغوي حول الجملة الاستفهامية وأدواتها',
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
                'period' => 'P5',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P5_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM2_S1/image44.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 2',
                ],
            ],
            // ==========================================
            // الأسبوع 3: النعت الحقيقي المبسط
            // ==========================================
            [
                'word' => 'تَعَلُّمُ النعت الحقيقي المبسط',
                'clean_word' => 'تَعَلُّمُ النعت الحقيقي المبسط',
                'raw_word' => 'تعلم النعت الحقيقي المبسط',
                'example_sentence' => 'مفهوم معرفي في الماء والحياة والبيئة المائية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM3_S1/image56.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الماء والحياة والبيئة المائية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ النعت الحقيقي المبسط',
                'clean_word' => 'مُفْرَدَةُ النعت الحقيقي المبسط',
                'raw_word' => 'مفردة النعت الحقيقي المبسط',
                'example_sentence' => 'رصيد لغوي حول النعت الحقيقي المبسط',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM3_S1/image58.png',
                'linked_sentences' => [
                    'رصيد لغوي حول النعت الحقيقي المبسط',
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
                'period' => 'P5',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P5_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM3_S1/image59.png',
                'linked_sentences' => [
                    'مهارة تواصلية وتعبيرية للأسبوع 3',
                ],
            ],
            // ==========================================
            // الأسبوع 4: تقويم ودعم عام للظواهر اللغوية
            // ==========================================
            [
                'word' => 'تَعَلُّمُ تقويم ودعم عام للظواهر اللغوية',
                'clean_word' => 'تَعَلُّمُ تقويم ودعم عام للظواهر اللغوية',
                'raw_word' => 'تعلم تقويم ودعم عام للظواهر اللغوية',
                'example_sentence' => 'مفهوم معرفي في الماء والحياة والبيئة المائية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM4_S1/image37.png',
                'linked_sentences' => [
                    'مفهوم معرفي في الماء والحياة والبيئة المائية',
                ],
            ],
            [
                'word' => 'مُفْرَدَةُ تقويم ودعم عام للظواهر اللغوية',
                'clean_word' => 'مُفْرَدَةُ تقويم ودعم عام للظواهر اللغوية',
                'raw_word' => 'مفردة تقويم ودعم عام للظواهر اللغوية',
                'example_sentence' => 'رصيد لغوي حول تقويم ودعم عام للظواهر اللغوية',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM4_S1/image42.png',
                'linked_sentences' => [
                    'رصيد لغوي حول تقويم ودعم عام للظواهر اللغوية',
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
                'period' => 'P5',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P5_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P5_SEM4_S1/image43.png',
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
