<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة السادسة ابتدائي (N6)
 * Unit: الوحدة 1: حضارات العالم الإنساني وتلاقح الثقافات (P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN6P1VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: تلاقح الحضارات القديمة ومعالمها الخالدة',
                'lesson_sentence' => 'شَيَّدَتِ الْحَضَارَاتُ الْإِنْسَانِيَّةُ الْقَدِيمَةُ مَعَالِمَ تَارِيخِيَّةً شَاهِدَةً عَلَى عَبْقَرِيَّةِ الْإِنْسَانِ.',
                'vocabulary' => [
                [
                    'word' => 'بَابِلُ',
                    'clean' => 'بابل',
                    'meaning' => 'حَضَارَةٌ عَرِيقَةٌ فِي بِلَادِ الرَّافِدَيْنِ اشْتُهِرَتْ بِالْجَنَائِنِ الْمُعَلَّقَةِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM1/vocab/image72.png'
                ],
                [
                    'word' => 'أَهْرَامَاتٌ',
                    'clean' => 'أهرامات',
                    'meaning' => 'مَعَالِمُ مِعْمَارِيَّةٌ شَامِخَةٌ شَيَّدَهَا الْفَرَاعِنَةُ',
                    'image' => '/assets/lesson-images/N6/P1/SEM1/vocab/image73.png'
                ],
                [
                    'word' => 'عَرَاقَةٌ',
                    'clean' => 'عراقة',
                    'meaning' => 'أَصَالَةٌ تَارِيخِيَّةٌ تَمْتَدُّ لِآلَافِ السِّنِينَ',
                    'image' => '/assets/lesson-images/N6/P1/SEM1/vocab/image74.png'
                ],
                [
                    'word' => 'تَلَاقُحٌ',
                    'clean' => 'تلاقح',
                    'meaning' => 'تَأَثُّرٌ وَتَأْثِيرٌ مُتَبَادَلٌ بَيْنَ ثَقَافَاتِ الشُّعُوبِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM1/vocab/image75.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حضارة الرافدين وقوانين حمورابي',
                'lesson_sentence' => 'وَضَعَتْ حَضَارَةُ بَابِلَ قَوَانِينَ مَكْتُوبَةً لِتَنْظِيمِ حَيَاةِ الْمُجْتَمَعِ وَحِمَايَةِ الْعَدْلِ.',
                'vocabulary' => [
                [
                    'word' => 'مِسَلَّةٌ',
                    'clean' => 'مسلة',
                    'meaning' => 'عَمُودٌ حَجَرِيٌّ مَنْقُوشٌ بِالنُّصُوصِ الْقَانُونِيَّةِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM2/vocab/image35.png'
                ],
                [
                    'word' => 'نُقُوشٌ',
                    'clean' => 'نقوش',
                    'meaning' => 'كِتَابَاتٌ مَحْفُورَةٌ عَلَى الصُّخُورِ وَالْأَلْوَاحِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM2/vocab/image36.png'
                ],
                [
                    'word' => 'كِتَابَةٌ',
                    'clean' => 'كتابة',
                    'meaning' => 'الْخَطُّ الْمِسْمَارِيُّ الَّذِي ابْتَكَرَهُ السُّومَرِيُّونَ',
                    'image' => '/assets/lesson-images/N6/P1/SEM2/vocab/image37.png'
                ],
                [
                    'word' => 'فَرَاعِنَةٌ',
                    'clean' => 'فراعنة',
                    'meaning' => 'مُلُوكُ مِصْرَ الْقَدِيمَةِ وَبُنَاةُ حَضَارَتِهَا',
                    'image' => '/assets/lesson-images/N6/P1/SEM2/vocab/image38.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الحضارة المغربية الأندلسية وجسور العلم',
                'lesson_sentence' => 'شَكَّلَتِ الْأَنْدَلُسُ وَالْمَغْرِبُ جِسْراً حَضَارِيّاً رَبَطَ بَيْنَ الشَّرْقِ وَالْغَرْبِ بِإِبْدَاعٍ.',
                'vocabulary' => [
                [
                    'word' => 'مَوْرُوثٌ',
                    'clean' => 'موروث',
                    'meaning' => 'كُلُّ مَا تَوَارَثَتْهُ الْأَجْيَالُ مِنْ عِلْمٍ وَفَنٍّ',
                    'image' => '/assets/lesson-images/N6/P1/SEM3/vocab/image38.png'
                ],
                [
                    'word' => 'إِشْعَاعٌ',
                    'clean' => 'إشعاع',
                    'meaning' => 'انْتِشَارُ نُورِ الْمَعْرِفَةِ مِنْ فَاسَ لِلْعَالَمِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM3/vocab/image39.png'
                ],
                [
                    'word' => 'عُلَمَاءُ',
                    'clean' => 'علماء',
                    'meaning' => 'فُقَهَاءُ وَفَلَاسِفَةٌ أَنَارُوا دُرُوبَ الْفِكْرِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM3/vocab/image40.png'
                ],
                [
                    'word' => 'مَخْطُوطَاتٌ',
                    'clean' => 'مخطوطات',
                    'meaning' => 'كُتُبٌ قَدِيمَةٌ نَادِرَةٌ خُطَّتْ بِالْيَدِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM3/vocab/image41.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: التراث العالمي الإنساني وصيانته',
                'lesson_sentence' => 'تُصَنِّفُ مُنَظَّمَةُ الْيُونِسْكُو الْمَعَالِمَ الْفَرِيدَةَ تُرَاثاً إِنْسَانِيّاً مُشْتَرَكاً يَجِبُ حِمَايَتُهُ.',
                'vocabulary' => [
                [
                    'word' => 'يُونِسْكُو',
                    'clean' => 'يونسكو',
                    'meaning' => 'مُنَظَّمَةُ الْأُمَمِ الْمُتَّحِدَةِ لِلتَّرْبِيَةِ وَالْعُلُومِ وَالثَّقَافَةِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM4/vocab/image2.png'
                ],
                [
                    'word' => 'صِيَانَةٌ',
                    'clean' => 'صيانة',
                    'meaning' => 'تَرْمِيمُ الْآثَارِ لِحِمَايَتِهَا مِنَ التَّلَفِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM4/vocab/image40.png'
                ],
                [
                    'word' => 'آثَارٌ',
                    'clean' => 'آثار',
                    'meaning' => 'بَقَايَا مَبَانٍ وَأَدَوَاتٍ تَارِيخِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P1/SEM4/vocab/image41.png'
                ],
                [
                    'word' => 'خُلُودٌ',
                    'clean' => 'خلود',
                    'meaning' => 'بَقَاءُ الْأَعْمَالِ الْعَظِيمَةِ حَيَّةً فِي الذَّاكِرَةِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM4/vocab/image2.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 1',
                'lesson_sentence' => 'إِنَّ أَبَاكَ وَأَخَاكَ يَفْتَخِرَانِ بِمَا شَيَّدَهُ الْأَجْدَادُ مِنْ حَضَارَةٍ خَالِدَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'بَابِلُ',
                    'clean' => 'بابل',
                    'meaning' => 'مَهْدُ الْحَضَارَةِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM5/vocab/image19.jpg'
                ],
                [
                    'word' => 'مِسَلَّةٌ',
                    'clean' => 'مسلة',
                    'meaning' => 'نَقْشُ الْقَوَانِينِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM5/vocab/image22.png'
                ],
                [
                    'word' => 'مَخْطُوطَاتٌ',
                    'clean' => 'مخطوطات',
                    'meaning' => 'كُنُوزُ الْعِلْمِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM5/vocab/image19.jpg'
                ],
                [
                    'word' => 'صِيَانَةٌ',
                    'clean' => 'صيانة',
                    'meaning' => 'حِفْظُ التُّرَاثِ',
                    'image' => '/assets/lesson-images/N6/P1/SEM5/vocab/image22.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N6',
                        'period' => 'P1',
                        'clean_word' => $item['clean'],
                    ],
                    [
                        'vocalized_word' => $item['word'],
                        'meaning' => $item['meaning'],
                        'image_url' => $item['image'],
                        'week_id' => $week['week_id'],
                        'lesson_sentence' => $week['lesson_sentence'],
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
