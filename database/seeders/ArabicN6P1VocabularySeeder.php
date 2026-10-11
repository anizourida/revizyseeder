<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN6P1VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N6, Period P1 (الوحدة 1: حضارات كونية وتاريخ الإنسانية المشترك).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: حضارات العالم: تصريف الفعل الصحيح والمعتل
            // ==========================================
            [
                'word' => 'حَضارَةٌ كَوْنِيَّةٌ',
                'clean_word' => 'حَضارَةٌ كَوْنِيَّةٌ',
                'raw_word' => 'حضارة كونية',
                'example_sentence' => 'إِرْثٌ بَشَرِيٌّ مُشْتَرَكٌ يُسْهِمُ في رِفْعَةِ الإِنْسانِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM1_S1/image72.png',
                'linked_sentences' => [
                    'إِرْثٌ بَشَرِيٌّ مُشْتَرَكٌ يُسْهِمُ في رِفْعَةِ الإِنْسانِ',
                ],
            ],
            [
                'word' => 'تَمازُجٌ',
                'clean_word' => 'تَمازُجٌ',
                'raw_word' => 'تمازج',
                'example_sentence' => 'اخْتِلاطُ الثَّقافاتِ وَتَلاقُحُ الأَفْكارِ عَبْرَ التّاريخِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM1_S1/image73.png',
                'linked_sentences' => [
                    'اخْتِلاطُ الثَّقافاتِ وَتَلاقُحُ الأَفْكارِ عَبْرَ التّاريخِ',
                ],
            ],
            [
                'word' => 'أَهْراماتٌ',
                'clean_word' => 'أَهْراماتٌ',
                'raw_word' => 'أهرامات',
                'example_sentence' => 'مَعالِمُ ضَخْمَةٌ شَيَّدَها الفَراعِنَةُ في مِصْرَ القَديمَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM1_S1/image74.png',
                'linked_sentences' => [
                    'مَعالِمُ ضَخْمَةٌ شَيَّدَها الفَراعِنَةُ في مِصْرَ القَديمَةِ',
                ],
            ],
            [
                'word' => 'بِابِلُ',
                'clean_word' => 'بِابِلُ',
                'raw_word' => 'بابل',
                'example_sentence' => 'مَهْدُ الحَضارَةِ العِراقِيَّةِ القَديمَةِ وَحَدائِقُها المُعَلَّقَةُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM1_S1/image75.png',
                'linked_sentences' => [
                    'مَهْدُ الحَضارَةِ العِراقِيَّةِ القَديمَةِ وَحَدائِقُها المُعَلَّقَةُ',
                ],
            ],
            [
                'word' => 'فَلْسَفَةٌ',
                'clean_word' => 'فَلْسَفَةٌ',
                'raw_word' => 'فلسفة',
                'example_sentence' => 'البَحْثُ عَنِ الحِكْمَةِ وَالحَقيقَةِ وَالتَّفْكيرُ المَنْطِقِيُّ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N6_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM1_S1/image76.png',
                'linked_sentences' => [
                    'البَحْثُ عَنِ الحِكْمَةِ وَالحَقيقَةِ وَالتَّفْكيرُ المَنْطِقِيُّ',
                ],
            ],
            // ==========================================
            // الأسبوع 2: إعراب الأسماء الخمسة والأفعال الخمسة
            // ==========================================
            [
                'word' => 'عَجائِبُ العالَمِ',
                'clean_word' => 'عَجائِبُ العالَمِ',
                'raw_word' => 'عجائب العالم',
                'example_sentence' => 'مَبَانٍ تَارِيخِيَّةٌ مُعْجِزَةٌ في التَّصْميمِ وَالبِناءِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM2_S1/image35.png',
                'linked_sentences' => [
                    'مَبَانٍ تَارِيخِيَّةٌ مُعْجِزَةٌ في التَّصْميمِ وَالبِناءِ',
                ],
            ],
            [
                'word' => 'حَريرٌ صينِيٌّ',
                'clean_word' => 'حَريرٌ صينِيٌّ',
                'raw_word' => 'حرير صيني',
                'example_sentence' => 'طَريقُ الحَريرِ التّاريخِيُّ لِلتِّجارَةِ بَيْنَ الشَّرْقِ وَالغَرْبِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM2_S1/image36.png',
                'linked_sentences' => [
                    'طَريقُ الحَريرِ التّاريخِيُّ لِلتِّجارَةِ بَيْنَ الشَّرْقِ وَالغَرْبِ',
                ],
            ],
            [
                'word' => 'تَدْوينٌ',
                'clean_word' => 'تَدْوينٌ',
                'raw_word' => 'تدوين',
                'example_sentence' => 'تَسْجيلُ العُلومِ وَالتّاريخِ كِتابَةً لِلأَجْيالِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM2_S1/image37.png',
                'linked_sentences' => [
                    'تَسْجيلُ العُلومِ وَالتّاريخِ كِتابَةً لِلأَجْيالِ',
                ],
            ],
            [
                'word' => 'مَخْطوطاتٌ',
                'clean_word' => 'مَخْطوطاتٌ',
                'raw_word' => 'مخطوطات',
                'example_sentence' => 'كُتُبٌ نَفيسَةٌ خُطَّتْ بِأَيْدي العُلَماءِ الأَوائِلِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N6_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM2_S1/image38.png',
                'linked_sentences' => [
                    'كُتُبٌ نَفيسَةٌ خُطَّتْ بِأَيْدي العُلَماءِ الأَوائِلِ',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الميزان الصرفي والمصادر الثلاثية وغير الثلاثية
            // ==========================================
            [
                'word' => 'حِوارُ الحَضاراتِ',
                'clean_word' => 'حِوارُ الحَضاراتِ',
                'raw_word' => 'حوار الحضارات',
                'example_sentence' => 'التَّفاهُمُ وَالسَّلامُ بَيْنَ الشُّعوبِ بَدَلَ الصِّدامِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM3_S1/image38.png',
                'linked_sentences' => [
                    'التَّفاهُمُ وَالسَّلامُ بَيْنَ الشُّعوبِ بَدَلَ الصِّدامِ',
                ],
            ],
            [
                'word' => 'تَسامُحٌ فِكْرِيٌّ',
                'clean_word' => 'تَسامُحٌ فِكْرِيٌّ',
                'raw_word' => 'تسامح فكري',
                'example_sentence' => 'قَبولُ الاخْتِلافِ وَاحْتِرامُ الثَّقافاتِ الأُخْرى',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM3_S1/image39.png',
                'linked_sentences' => [
                    'قَبولُ الاخْتِلافِ وَاحْتِرامُ الثَّقافاتِ الأُخْرى',
                ],
            ],
            [
                'word' => 'انْفِتاحٌ',
                'clean_word' => 'انْفِتاحٌ',
                'raw_word' => 'انفتاح',
                'example_sentence' => 'التَّفاعُلُ الإيجابِيُّ مَعَ التَّطَوُّراتِ العالَمِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM3_S1/image40.png',
                'linked_sentences' => [
                    'التَّفاعُلُ الإيجابِيُّ مَعَ التَّطَوُّراتِ العالَمِيَّةِ',
                ],
            ],
            [
                'word' => 'تَنَوُّعٌ ثَقافِيٌّ',
                'clean_word' => 'تَنَوُّعٌ ثَقافِيٌّ',
                'raw_word' => 'تنوع ثقافي',
                'example_sentence' => 'غِنى المُجْتَمَعِ البَشَرِيِّ بِتَعَدُّدِ لُغاتِهِ وَفُنونِهِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N6_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM3_S1/image41.png',
                'linked_sentences' => [
                    'غِنى المُجْتَمَعِ البَشَرِيِّ بِتَعَدُّدِ لُغاتِهِ وَفُنونِهِ',
                ],
            ],
            // ==========================================
            // الأسبوع 4: التوكيد والبدل والعطف
            // ==========================================
            [
                'word' => 'عَصْرُ التَّنْويرِ',
                'clean_word' => 'عَصْرُ التَّنْويرِ',
                'raw_word' => 'عصر التنوير',
                'example_sentence' => 'حِقْبَةٌ عَرَفَتْ انْتِشارَ المَعْرِفَةِ وَالعَقْلانِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM4_S1/image2.png',
                'linked_sentences' => [
                    'حِقْبَةٌ عَرَفَتْ انْتِشارَ المَعْرِفَةِ وَالعَقْلانِيَّةِ',
                ],
            ],
            [
                'word' => 'طِباعَةٌ',
                'clean_word' => 'طِباعَةٌ',
                'raw_word' => 'طباعة',
                'example_sentence' => 'اخْتِراعٌ ثَوْرِيٌّ أَتاحَ نَشْرَ الكُتُبِ لِعامَّةِ النّاسِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM4_S1/image40.png',
                'linked_sentences' => [
                    'اخْتِراعٌ ثَوْرِيٌّ أَتاحَ نَشْرَ الكُتُبِ لِعامَّةِ النّاسِ',
                ],
            ],
            [
                'word' => 'مَوْسوعَةٌ',
                'clean_word' => 'مَوْسوعَةٌ',
                'raw_word' => 'موسوعة',
                'example_sentence' => 'كِتابٌ شامِلٌ يَجْمَعُ المَعارِفَ في شَتّى العُلومِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N6_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM4_S1/image41.png',
                'linked_sentences' => [
                    'كِتابٌ شامِلٌ يَجْمَعُ المَعارِفَ في شَتّى العُلومِ',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم حصيلة الظواهر اللغوية
            // ==========================================
            [
                'word' => 'اشْتِقاقٌ مَعْجَمِيٌّ',
                'clean_word' => 'اشْتِقاقٌ مَعْجَمِيٌّ',
                'raw_word' => 'اشتقاق معجمي',
                'example_sentence' => 'تَوْليدُ كَلِماتٍ كَثيرَةٍ مِن جَذْرٍ لُغَوِيٍّ واحِدٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM5_S1/image19.jpg',
                'linked_sentences' => [
                    'تَوْليدُ كَلِماتٍ كَثيرَةٍ مِن جَذْرٍ لُغَوِيٍّ واحِدٍ',
                ],
            ],
            [
                'word' => 'سِياقٌ دَلالِيٌّ',
                'clean_word' => 'سِياقٌ دَلالِيٌّ',
                'raw_word' => 'سياق دلالي',
                'example_sentence' => 'تَحْديدُ مَعْنى الكَلِمَةِ بِحَسَبِ مَوْقِعِها في الجُمْلَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N6_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM5_S1/image22.png',
                'linked_sentences' => [
                    'تَحْديدُ مَعْنى الكَلِمَةِ بِحَسَبِ مَوْقِعِها في الجُمْلَةِ',
                ],
            ],
            // ==========================================
            // الأسبوع 6: روائز التقويم النهائي للوحدة الأولى
            // ==========================================
            [
                'word' => 'مَقالٌ تَرْكِيبِيٌّ',
                'clean_word' => 'مَقالٌ تَرْكِيبِيٌّ',
                'raw_word' => 'مقال تركيبي',
                'example_sentence' => 'كِتابَةُ نَصٍّ حِجاجِيٍّ حَوْلَ أَثَرِ الحَضاراتِ الإِنْسانِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N6',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N6_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N6_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'كِتابَةُ نَصٍّ حِجاجِيٍّ حَوْلَ أَثَرِ الحَضاراتِ الإِنْسانِيَّةِ',
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
