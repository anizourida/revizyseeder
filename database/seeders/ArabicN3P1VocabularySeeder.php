<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN3P1VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N3, Period P1 (الوحدة 1: عالم الأصدقاء والقيم الإنسانية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: الفعل والفاعل والجملة الفعلية
            // ==========================================
            [
                'word' => 'غُرورٌ',
                'clean_word' => 'غُرورٌ',
                'raw_word' => 'غرور',
                'example_sentence' => 'إِعْجابُ المَرْءِ بِنَفْسِهِ وَالتَّكَبُّرُ عَلى الغَيْرِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image97.png',
                'linked_sentences' => [
                    'إِعْجابُ المَرْءِ بِنَفْسِهِ وَالتَّكَبُّرُ عَلى الغَيْرِ',
                ],
            ],
            [
                'word' => 'عِتابٌ',
                'clean_word' => 'عِتابٌ',
                'raw_word' => 'عتاب',
                'example_sentence' => 'لَوْمُ الصَّديقِ بِرِفْقٍ وَمَحَبَّةٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image98.png',
                'linked_sentences' => [
                    'لَوْمُ الصَّديقِ بِرِفْقٍ وَمَحَبَّةٍ',
                ],
            ],
            [
                'word' => 'اِعْتِذارٌ',
                'clean_word' => 'اِعْتِذارٌ',
                'raw_word' => 'اعتذار',
                'example_sentence' => 'طَلَبُ الصَّفْحِ وَالمَغْفِرَةِ عِنْدَ الخَطَإِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image88.png',
                'linked_sentences' => [
                    'طَلَبُ الصَّفْحِ وَالمَغْفِرَةِ عِنْدَ الخَطَإِ',
                ],
            ],
            [
                'word' => 'اَلْأَسَفُ',
                'clean_word' => 'اَلْأَسَفُ',
                'raw_word' => 'الأسف',
                'example_sentence' => 'الشُّعورُ بِالحُزْنِ وَالنَّدَمِ عَلى ما فاتَ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image99.png',
                'linked_sentences' => [
                    'الشُّعورُ بِالحُزْنِ وَالنَّدَمِ عَلى ما فاتَ',
                ],
            ],
            [
                'word' => 'اِحْتِرامٌ',
                'clean_word' => 'اِحْتِرامٌ',
                'raw_word' => 'احترام',
                'example_sentence' => 'تَقْديرُ الآخَرينَ وَحُسْنُ التَّعامُلِ مَعَهُمْ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image100.png',
                'linked_sentences' => [
                    'تَقْديرُ الآخَرينَ وَحُسْنُ التَّعامُلِ مَعَهُمْ',
                ],
            ],
            [
                'word' => 'تَعاوُنٌ',
                'clean_word' => 'تَعاوُنٌ',
                'raw_word' => 'تعاون',
                'example_sentence' => 'التَّآزُرُ وَالعَمَلُ الجَماعِيُّ لِتَحْقيقِ النَّجاحِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image101.png',
                'linked_sentences' => [
                    'التَّآزُرُ وَالعَمَلُ الجَماعِيُّ لِتَحْقيقِ النَّجاحِ',
                ],
            ],
            // ==========================================
            // الأسبوع 2: المبتدأ والخبر والجملة الاسمية
            // ==========================================
            [
                'word' => 'مَوَدَّةٌ',
                'clean_word' => 'مَوَدَّةٌ',
                'raw_word' => 'مودة',
                'example_sentence' => 'حُبٌّ خالِصٌ وَأُلْفَةٌ بَيْنَ الأَصْدِقاءِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM2_S1/image43.png',
                'linked_sentences' => [
                    'حُبٌّ خالِصٌ وَأُلْفَةٌ بَيْنَ الأَصْدِقاءِ',
                ],
            ],
            [
                'word' => 'إِيثارٌ',
                'clean_word' => 'إِيثارٌ',
                'raw_word' => 'إيثار',
                'example_sentence' => 'تَفْضيلُ مَصْلَحَةِ الغَيْرِ عَلى النَّفْسِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM2_S1/image44.png',
                'linked_sentences' => [
                    'تَفْضيلُ مَصْلَحَةِ الغَيْرِ عَلى النَّفْسِ',
                ],
            ],
            [
                'word' => 'وَفاءٌ',
                'clean_word' => 'وَفاءٌ',
                'raw_word' => 'وفاء',
                'example_sentence' => 'الإِخْلاصُ في العَهْدِ وَالصَّداقَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM2_S1/image45.png',
                'linked_sentences' => [
                    'الإِخْلاصُ في العَهْدِ وَالصَّداقَةِ',
                ],
            ],
            [
                'word' => 'تَسامُحٌ',
                'clean_word' => 'تَسامُحٌ',
                'raw_word' => 'تسامح',
                'example_sentence' => 'العَفْوُ عَنِ الزَّلّاتِ وَنَبْذُ الخِلافاتِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N3_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM2_S1/image46.png',
                'linked_sentences' => [
                    'العَفْوُ عَنِ الزَّلّاتِ وَنَبْذُ الخِلافاتِ',
                ],
            ],
            // ==========================================
            // الأسبوع 3: الضمائر المنفصلة والمتصلة
            // ==========================================
            [
                'word' => 'مُغامِرٌ',
                'clean_word' => 'مُغامِرٌ',
                'raw_word' => 'مغامر',
                'example_sentence' => 'شَجاعٌ يَهْوى اسْتِكْشافَ المَجاهِلِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM3_S1/image5.png',
                'linked_sentences' => [
                    'شَجاعٌ يَهْوى اسْتِكْشافَ المَجاهِلِ',
                ],
            ],
            [
                'word' => 'عَزيمَةٌ',
                'clean_word' => 'عَزيمَةٌ',
                'raw_word' => 'عزيمة',
                'example_sentence' => 'إِرادَةٌ قَوِيَّةٌ لا تَنْكَسِرُ أَمامَ العَقَباتِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM3_S1/image4.png',
                'linked_sentences' => [
                    'إِرادَةٌ قَوِيَّةٌ لا تَنْكَسِرُ أَمامَ العَقَباتِ',
                ],
            ],
            [
                'word' => 'إِبْداعٌ',
                'clean_word' => 'إِبْداعٌ',
                'raw_word' => 'إبداع',
                'example_sentence' => 'ابْتِكارُ أَمْرٍ جَديدٍ لَمْ يُسْبَقْ إِلَيْهِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM3_S1/image48.jpeg',
                'linked_sentences' => [
                    'ابْتِكارُ أَمْرٍ جَديدٍ لَمْ يُسْبَقْ إِلَيْهِ',
                ],
            ],
            [
                'word' => 'شَغَفٌ',
                'clean_word' => 'شَغَفٌ',
                'raw_word' => 'شغف',
                'example_sentence' => 'اهْتِمامٌ بالِغٌ وَحُبٌّ شَديدٌ لِلتَّعَلُّمِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N3_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM3_S1/image49.png',
                'linked_sentences' => [
                    'اهْتِمامٌ بالِغٌ وَحُبٌّ شَديدٌ لِلتَّعَلُّمِ',
                ],
            ],
            // ==========================================
            // الأسبوع 4: أسماء الإشارة والأساليب
            // ==========================================
            [
                'word' => 'حِوارٌ',
                'clean_word' => 'حِوارٌ',
                'raw_word' => 'حوار',
                'example_sentence' => 'تَبادُلُ الحَديثِ بِأَدَبٍ وَاحْتِرامِ الآراءِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM4_S1/image46.png',
                'linked_sentences' => [
                    'تَبادُلُ الحَديثِ بِأَدَبٍ وَاحْتِرامِ الآراءِ',
                ],
            ],
            [
                'word' => 'إِنْصاتٌ',
                'clean_word' => 'إِنْصاتٌ',
                'raw_word' => 'إنصات',
                'example_sentence' => 'حُسْنُ الاسْتِماعِ بِتَرْكيزٍ وَاهْتِمامٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM4_S1/image47.png',
                'linked_sentences' => [
                    'حُسْنُ الاسْتِماعِ بِتَرْكيزٍ وَاهْتِمامٍ',
                ],
            ],
            [
                'word' => 'حِكْمَةٌ',
                'clean_word' => 'حِكْمَةٌ',
                'raw_word' => 'حكمة',
                'example_sentence' => 'وَضْعُ الأُمورِ في مَواضِعِها الصَّحيحَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM4_S1/image48.jpeg',
                'linked_sentences' => [
                    'وَضْعُ الأُمورِ في مَواضِعِها الصَّحيحَةِ',
                ],
            ],
            [
                'word' => 'نَصيحَةٌ',
                'clean_word' => 'نَصيحَةٌ',
                'raw_word' => 'نصيحة',
                'example_sentence' => 'تَوْجيهٌ صادِقٌ يَبْتَغي مَصْلَحَةَ الصَّديقِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N3_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM4_S1/image49.png',
                'linked_sentences' => [
                    'تَوْجيهٌ صادِقٌ يَبْتَغي مَصْلَحَةَ الصَّديقِ',
                ],
            ],
            // ==========================================
            // الأسبوع 5: أسبوع الدعم والتركيب
            // ==========================================
            [
                'word' => 'شَبَكَةُ الكَلِمَةِ',
                'clean_word' => 'شَبَكَةُ الكَلِمَةِ',
                'raw_word' => 'شبكة الكلمة',
                'example_sentence' => 'اسْتِخْراجُ كُلِّ ما يَتَعَلَّقُ بِالمَفْهُومِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM5_S1/image44.png',
                'linked_sentences' => [
                    'اسْتِخْراجُ كُلِّ ما يَتَعَلَّقُ بِالمَفْهُومِ',
                ],
            ],
            [
                'word' => 'خَريطَةُ الكَلِمَةِ',
                'clean_word' => 'خَريطَةُ الكَلِمَةِ',
                'raw_word' => 'خريطة الكلمة',
                'example_sentence' => 'النَّوْعُ، المُرادِفُ، الضِّدُّ، وَالسِّياقُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N3_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM5_S1/image45.png',
                'linked_sentences' => [
                    'النَّوْعُ، المُرادِفُ، الضِّدُّ، وَالسِّياقُ',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم الوحدة
            // ==========================================
            [
                'word' => 'حَصيلَةٌ',
                'clean_word' => 'حَصيلَةٌ',
                'raw_word' => 'حصيلة',
                'example_sentence' => 'مَجْموعُ المَعارِفِ وَالقِيَمِ المُكْتَسَبَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N3_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'مَجْموعُ المَعارِفِ وَالقِيَمِ المُكْتَسَبَةِ',
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
