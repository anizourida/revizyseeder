<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN4P1VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N4, Period P1 (الوحدة 1: الحضارة المغربية والقيم الوطنية والإنسانية).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: أقسام الكلمة (اسم، فعل، حرف)
            // ==========================================
            [
                'word' => 'مُثابَرَةٌ',
                'clean_word' => 'مُثابَرَةٌ',
                'raw_word' => 'مثابرة',
                'example_sentence' => 'اَلاِسْتِمْرارُ بِجِدٍّ رَغْمَ التَّحَدِّياتِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image57.png',
                'linked_sentences' => [
                    'اَلاِسْتِمْرارُ بِجِدٍّ رَغْمَ التَّحَدِّياتِ',
                ],
            ],
            [
                'word' => 'تَفَوُّقٌ',
                'clean_word' => 'تَفَوُّقٌ',
                'raw_word' => 'تفوق',
                'example_sentence' => 'اَلنَّجاحُ بِشَكْلٍ مُمَيَّزٍ وَفَريدٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image46.png',
                'linked_sentences' => [
                    'اَلنَّجاحُ بِشَكْلٍ مُمَيَّزٍ وَفَريدٍ',
                ],
            ],
            [
                'word' => 'ذُهِلَ',
                'clean_word' => 'ذُهِلَ',
                'raw_word' => 'ذهل',
                'example_sentence' => 'أَصَابَتْهُ الدَّهْشَةُ وَالحَيْرَةُ الشَّديدَةُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image58.png',
                'linked_sentences' => [
                    'أَصَابَتْهُ الدَّهْشَةُ وَالحَيْرَةُ الشَّديدَةُ',
                ],
            ],
            [
                'word' => 'وَقْتٌ قِياسِيٌّ',
                'clean_word' => 'وَقْتٌ قِياسِيٌّ',
                'raw_word' => 'وقت قياسي',
                'example_sentence' => 'مُدَّةٌ زَمَنِيَّةٌ قَصيرَةٌ لَمْ تُسْبَقْ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image59.png',
                'linked_sentences' => [
                    'مُدَّةٌ زَمَنِيَّةٌ قَصيرَةٌ لَمْ تُسْبَقْ',
                ],
            ],
            [
                'word' => 'يُرَمِّمُ',
                'clean_word' => 'يُرَمِّمُ',
                'raw_word' => 'يرمم',
                'example_sentence' => 'يُصْلِحُ المَباني وَالآثارَ التّاريخِيَّةَ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image60.png',
                'linked_sentences' => [
                    'يُصْلِحُ المَباني وَالآثارَ التّاريخِيَّةَ',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الفعل الماضي والمضارع والأمر
            // ==========================================
            [
                'word' => 'حَضارَةٌ',
                'clean_word' => 'حَضارَةٌ',
                'raw_word' => 'حضارة',
                'example_sentence' => 'مَجْموعُ المُنْجَزاتِ العِلْمِيَّةِ وَالعُمْرانِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM2_S1/image54.png',
                'linked_sentences' => [
                    'مَجْموعُ المُنْجَزاتِ العِلْمِيَّةِ وَالعُمْرانِيَّةِ',
                ],
            ],
            [
                'word' => 'مَآثِرُ',
                'clean_word' => 'مَآثِرُ',
                'raw_word' => 'مآثر',
                'example_sentence' => 'المَعالِمُ التّاريخِيَّةُ الخالِدَةُ في الوَطَنِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM2_S1/image50.jpg',
                'linked_sentences' => [
                    'المَعالِمُ التّاريخِيَّةُ الخالِدَةُ في الوَطَنِ',
                ],
            ],
            [
                'word' => 'فُسَيْفِساءُ',
                'clean_word' => 'فُسَيْفِساءُ',
                'raw_word' => 'فسيفساء',
                'example_sentence' => 'فَنُّ تَشْكيلِ الزَّخارِفِ بِقِطَعٍ مَلَوَّنَةٍ صَغيرَةٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM2_S1/image55.png',
                'linked_sentences' => [
                    'فَنُّ تَشْكيلِ الزَّخارِفِ بِقِطَعٍ مَلَوَّنَةٍ صَغيرَةٍ',
                ],
            ],
            [
                'word' => 'عُمْرانٌ',
                'clean_word' => 'عُمْرانٌ',
                'raw_word' => 'عمران',
                'example_sentence' => 'فَنُّ البِناءِ وَالتَّشْييدِ المِعْمارِيِّ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N4_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM2_S1/image56.png',
                'linked_sentences' => [
                    'فَنُّ البِناءِ وَالتَّشْييدِ المِعْمارِيِّ',
                ],
            ],
            // ==========================================
            // الأسبوع 3: عناصر الجملة الفعلية والاسمية
            // ==========================================
            [
                'word' => 'أَصَالَةٌ',
                'clean_word' => 'أَصَالَةٌ',
                'raw_word' => 'أصالة',
                'example_sentence' => 'التَّمَسُّكُ بِالجُذورِ وَالقِيَمِ العَريقَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM3_S1/image66.png',
                'linked_sentences' => [
                    'التَّمَسُّكُ بِالجُذورِ وَالقِيَمِ العَريقَةِ',
                ],
            ],
            [
                'word' => 'مُعاصَرَةٌ',
                'clean_word' => 'مُعاصَرَةٌ',
                'raw_word' => 'معاصرة',
                'example_sentence' => 'مُواكَبَةُ الحَداثَةِ وَالتَّطَوُّرِ الحَديثِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM3_S1/image67.png',
                'linked_sentences' => [
                    'مُواكَبَةُ الحَداثَةِ وَالتَّطَوُّرِ الحَديثِ',
                ],
            ],
            [
                'word' => 'تُراثٌ',
                'clean_word' => 'تُراثٌ',
                'raw_word' => 'تراث',
                'example_sentence' => 'ما خَلَّفَهُ الأَجْدادُ مِن عاداتٍ وَفُنونٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM3_S1/image42.jpg',
                'linked_sentences' => [
                    'ما خَلَّفَهُ الأَجْدادُ مِن عاداتٍ وَفُنونٍ',
                ],
            ],
            [
                'word' => 'افْتِخارٌ',
                'clean_word' => 'افْتِخارٌ',
                'raw_word' => 'افتخار',
                'example_sentence' => 'الاعتِزازُ بِالأَمْجادِ وَالمَكاسِبِ الوَطَنِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N4_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM3_S1/image43.png',
                'linked_sentences' => [
                    'الاعتِزازُ بِالأَمْجادِ وَالمَكاسِبِ الوَطَنِيَّةِ',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الفعل الصحيح: السالم والمهموز والمضعف
            // ==========================================
            [
                'word' => 'صانِعٌ تَقْليدِيٌّ',
                'clean_word' => 'صانِعٌ تَقْليدِيٌّ',
                'raw_word' => 'صانع تقليدي',
                'example_sentence' => 'حِرَفِيٌّ ماهِرٌ يُبْدِعُ في الصَّناعَةِ اليَدَوِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM4_S1/image48.png',
                'linked_sentences' => [
                    'حِرَفِيٌّ ماهِرٌ يُبْدِعُ في الصَّناعَةِ اليَدَوِيَّةِ',
                ],
            ],
            [
                'word' => 'نَقْشٌ',
                'clean_word' => 'نَقْشٌ',
                'raw_word' => 'نقش',
                'example_sentence' => 'حَفْرُ الزَّخارِفِ عَلى الخَشَبِ وَالجِبْسِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM4_S1/image59.png',
                'linked_sentences' => [
                    'حَفْرُ الزَّخارِفِ عَلى الخَشَبِ وَالجِبْسِ',
                ],
            ],
            [
                'word' => 'نَسِيجٌ',
                'clean_word' => 'نَسِيجٌ',
                'raw_word' => 'نسيج',
                'example_sentence' => 'حِياكَةُ الزَّرابِي وَالأَقْمِشَةِ التَّقْليدِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N4_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM4_S1/image13.png',
                'linked_sentences' => [
                    'حِياكَةُ الزَّرابِي وَالأَقْمِشَةِ التَّقْليدِيَّةِ',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم الظواهر التركيبية والصرفية
            // ==========================================
            [
                'word' => 'تَراكيبُ',
                'clean_word' => 'تَراكيبُ',
                'raw_word' => 'تراكيب',
                'example_sentence' => 'بِناءُ الجُمَلِ المُفيدَةِ وَإِعْرابُ أَرْكانِها',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM5_S1/image42.png',
                'linked_sentences' => [
                    'بِناءُ الجُمَلِ المُفيدَةِ وَإِعْرابُ أَرْكانِها',
                ],
            ],
            [
                'word' => 'صَرْفٌ',
                'clean_word' => 'صَرْفٌ',
                'raw_word' => 'صرف',
                'example_sentence' => 'تَحْويلُ الأَفْعالِ وَالأَسْماءِ بَيْنَ الأزْمِنَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N4_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM5_S1/image43.png',
                'linked_sentences' => [
                    'تَحْويلُ الأَفْعالِ وَالأَسْماءِ بَيْنَ الأزْمِنَةِ',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم التعلمات والحصيلة
            // ==========================================
            [
                'word' => 'إِنْتاجٌ كِتابِيٌّ',
                'clean_word' => 'إِنْتاجٌ كِتابِيٌّ',
                'raw_word' => 'إنتاج كتابي',
                'example_sentence' => 'كِتابَةُ نَصٍّ وَصْفِيٍّ لِمَعْلَمَةٍ حَضارِيَّةٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N4_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'كِتابَةُ نَصٍّ وَصْفِيٍّ لِمَعْلَمَةٍ حَضارِيَّةٍ',
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
