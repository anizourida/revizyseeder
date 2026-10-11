<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الخامسة ابتدائي (N5)
 * Unit: الوحدة 1: مغاربة العالم والإشعاع الدولي (Default P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الكفاءات المغربية بالخارج وإشعاعها',
                'lesson_sentence' => 'يُسَاهِمُ مُغَارِبَةُ الْعَالَمِ بِكِفَاءَاتِهِمْ الْعَالِيَةِ فِي تَطْوِيرِ وَطَنِهِمُ الْأُمِّ.',
                'vocabulary' => [
                [
                    'word' => 'مَهْجَرٌ',
                    'clean' => 'مهجر',
                    'meaning' => 'بِلَادُ الْإِقَامَةِ خَارِجَ أَرْضِ الْوَطَنِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM1/vocab/image12.png'
                ],
                [
                    'word' => 'كِفَاءَةٌ',
                    'clean' => 'كفاءة',
                    'meaning' => 'قُدْرَةٌ عِلْمِيَّةٌ أَوْ مِهْنِيَّةٌ عَالِيَةُ الْمُسْتَوَى',
                    'image' => '/assets/lesson-images/N5/P1/SEM1/vocab/image3.png'
                ],
                [
                    'word' => 'اعْتِزَازٌ',
                    'clean' => 'اعتزاز',
                    'meaning' => 'افْتِخَارٌ صَادِقٌ بِالْهُوِيَّةِ وَالِانْتِمَاءِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM1/vocab/image45.png'
                ],
                [
                    'word' => 'هُوِيَّةٌ',
                    'clean' => 'هوية',
                    'meaning' => 'مَجْمُوعُ الصِّفَاتِ وَالْقِيَمِ الَّتِي تُمَيِّزُ الشَّعْبَ',
                    'image' => '/assets/lesson-images/N5/P1/SEM1/vocab/image51.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الارتباط بالوطن الأم والحنين للجذور',
                'lesson_sentence' => 'يَبْقَى الْمُغْتَرِبُ مُتَعَلِّقاً بِجُذُورِهِ الْوَطَنِيَّةِ وَثَقَافَتِهِ الْأَصِيلَةِ مَهْمَا طَالَ الْغِيَابُ.',
                'vocabulary' => [
                [
                    'word' => 'انْتِمَاءٌ',
                    'clean' => 'انتماء',
                    'meaning' => 'ارْتِبَاطٌ وَثِيقٌ بِالْأَرْضِ وَالتَّارِيخِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM2/vocab/image78.png'
                ],
                [
                    'word' => 'شَوْقٌ',
                    'clean' => 'شوق',
                    'meaning' => 'نُزُوعُ النَّفْسِ وَلَهْفَتُهَا لِرُؤْيَةِ الْأَحِبَّةِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM2/vocab/image80.png'
                ],
                [
                    'word' => 'جُذُورٌ',
                    'clean' => 'جذور',
                    'meaning' => 'أُصُولٌ عَرِيقَةٌ تَرْبِطُ الْإِنْسَانَ بِأَرْضِهِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM2/vocab/image81.png'
                ],
                [
                    'word' => 'تَعَلُّقٌ',
                    'clean' => 'تعلق',
                    'meaning' => 'حُبٌّ شَدِيدٌ وَتَمَسُّكٌ لَا يَنْقَطِعُ',
                    'image' => '/assets/lesson-images/N5/P1/SEM2/vocab/image82.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الإنجازات الرياضية والعلمية في المحافل الدولية',
                'lesson_sentence' => 'حَقَّقَ الْأَبْطَالُ الْمَغَارِبَةُ نَجَاحَاتٍ بَاهِرَةً فِي الْمَحَافِلِ الدَّوْلِيَّةِ الْكُبْرَى.',
                'vocabulary' => [
                [
                    'word' => 'تَأَلُّقٌ',
                    'clean' => 'تألق',
                    'meaning' => 'لَمَعَانٌ وَتَمَيُّزٌ بَاهِرٌ فِي الْأَدَاءِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM3/vocab/image72.png'
                ],
                [
                    'word' => 'بَرَاعَةٌ',
                    'clean' => 'براعة',
                    'meaning' => 'مَهَارَةٌ فَائِقَةٌ وَإِتْقَانٌ مُتَفَوِّقٌ',
                    'image' => '/assets/lesson-images/N5/P1/SEM3/vocab/image73.png'
                ],
                [
                    'word' => 'تَتْوِيجٌ',
                    'clean' => 'تتويج',
                    'meaning' => 'الْحُصُولُ عَلَى الْمَرَاتِبِ الْأُولَى وَالْجَوَائِزِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM3/vocab/image74.png'
                ],
                [
                    'word' => 'رِيَادَةٌ',
                    'clean' => 'ريادة',
                    'meaning' => 'تَصَدُّرُ الطَّلِيعَةِ وَالسَّبْقُ فِي الْمَجَالِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM3/vocab/image75.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: التبادل الثقافي وحوار الحضارات',
                'lesson_sentence' => 'يَنْقُلُ الْمُهَاجِرُ الصُّورَةَ الْمُشْرِقَةَ لِلْحَضَارَةِ الْمَغْرِبِيَّةِ إِلَى مُخْتَلِفِ الشُّعُوبِ.',
                'vocabulary' => [
                [
                    'word' => 'تَلَاقُحٌ',
                    'clean' => 'تلاقح',
                    'meaning' => 'تَفَاعُلٌ إِيجَابِيٌّ وَتَبَادُلُ الْمَعَارِفِ بَيْنَ الثَّقَافَاتِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM4/vocab/image14.png'
                ],
                [
                    'word' => 'إِشْعَاعٌ',
                    'clean' => 'إشعاع',
                    'meaning' => 'انْتِشَارُ التَّأْثِيرِ الْحَضَارِيِّ وَالْفِكْرِيِّ',
                    'image' => '/assets/lesson-images/N5/P1/SEM4/vocab/image30.jpg'
                ],
                [
                    'word' => 'سَفِيرٌ',
                    'clean' => 'سفير',
                    'meaning' => 'مُمَثِّلٌ لِبَلَدِهِ يَنْقُلُ صُورَتَهُ الْمُشْرِقَةَ',
                    'image' => '/assets/lesson-images/N5/P1/SEM4/vocab/image33.png'
                ],
                [
                    'word' => 'انْفِتَاحٌ',
                    'clean' => 'انفتاح',
                    'meaning' => 'تَقَبُّلُ الثَّقَافَاتِ الْأُخْرَى مَعَ صَوْنِ الْهُوِيَّةِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM4/vocab/image14.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 1',
                'lesson_sentence' => 'إِنَّ أَبْنَاءَ الْمَغْرِبِ فِي الْعَالَمِ طَاقَةٌ خَلَّاقَةٌ تُغْنِي التَّنْمِيَةَ الشَّامِلَةَ.',
                'vocabulary' => [
                [
                    'word' => 'مَهْجَرٌ',
                    'clean' => 'مهجر',
                    'meaning' => 'دَارُ الِاغْتِرَابِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM5/vocab/image39.png'
                ],
                [
                    'word' => 'انْتِمَاءٌ',
                    'clean' => 'انتماء',
                    'meaning' => 'رَابِطَةُ الْوَطَنِ',
                    'image' => '/assets/lesson-images/N5/P1/SEM5/vocab/image41.png'
                ],
                [
                    'word' => 'تَأَلُّقٌ',
                    'clean' => 'تألق',
                    'meaning' => 'تَمَيُّزٌ بَاهِرٌ',
                    'image' => '/assets/lesson-images/N5/P1/SEM5/vocab/image39.png'
                ],
                [
                    'word' => 'إِشْعَاعٌ',
                    'clean' => 'إشعاع',
                    'meaning' => 'تَأْثِيرٌ حَضَارِيٌّ',
                    'image' => '/assets/lesson-images/N5/P1/SEM5/vocab/image41.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N5',
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
