<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الأولى ابتدائي (N1)
 * Unit: الوحدة 1: الأسرة والمدرسة (Default P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN1VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: حرف الدال (د) في البيت والمدرسة',
                'lesson_sentence' => 'هَذَا دَفْتَرِي الْجَدِيدُ يَا أُمِّي.',
                'vocabulary' => [
                [
                    'word' => 'دَفْتَرٌ',
                    'clean' => 'دفتر',
                    'meaning' => 'كُرَّاسَةُ الْكِتَابَةِ وَالرَّسْمِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image100.png'
                ],
                [
                    'word' => 'دَارٌ',
                    'clean' => 'دار',
                    'meaning' => 'مَنْزِلُ الْأُسْرَةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image106.png'
                ],
                [
                    'word' => 'دُبٌّ',
                    'clean' => 'دب',
                    'meaning' => 'لُعْبَةُ الدُّبِّ الصَّغِيرِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image107.png'
                ],
                [
                    'word' => 'دَرَاجَةٌ',
                    'clean' => 'دراجة',
                    'meaning' => 'مَرْكَبَةٌ ذَاتُ عَجَلَتَيْنِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image30.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حرف الراء (ر) وأنشطة الرسم',
                'lesson_sentence' => 'رَسَمَ رَامِي رُمَّانَةً حَمْرَاءَ.',
                'vocabulary' => [
                [
                    'word' => 'رَسْمٌ',
                    'clean' => 'رسم',
                    'meaning' => 'صُورَةٌ مُلَوَّنَةٌ بِالْفُرْشَاةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image61.png'
                ],
                [
                    'word' => 'رُمَّانٌ',
                    'clean' => 'رمان',
                    'meaning' => 'فَاكِهَةٌ خَرِيفِيَّةٌ لَذِيذَةٌ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image66.jpg'
                ],
                [
                    'word' => 'رِيشَةٌ',
                    'clean' => 'ريشة',
                    'meaning' => 'أَدَاةُ الرَّسْمِ وَالتَّلْوِينِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image73.jpg'
                ],
                [
                    'word' => 'رَمْلٌ',
                    'clean' => 'رمل',
                    'meaning' => 'حَبَّاتُ الرَّمْلِ فِي الشَّاطِئِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image74.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حرف الميم (م) وأدواتي المدرسية',
                'lesson_sentence' => 'مِحْفَظَةُ مَرْيَمَ مُرَتَّبَةٌ وَجَمِيلَةٌ.',
                'vocabulary' => [
                [
                    'word' => 'مِحْفَظَةٌ',
                    'clean' => 'محفظة',
                    'meaning' => 'حَقِيبَةُ حَمْلِ الْكُتُبِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image57.png'
                ],
                [
                    'word' => 'مَقْلَمَةٌ',
                    'clean' => 'مقلمة',
                    'meaning' => 'عُلْبَةُ حِفْظِ الْأَقْلَامِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image58.png'
                ],
                [
                    'word' => 'مِقَصٌّ',
                    'clean' => 'مقص',
                    'meaning' => 'أَدَاةُ قَصِّ الْأَوْرَاقِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image59.png'
                ],
                [
                    'word' => 'مَكْتَبٌ',
                    'clean' => 'مكتب',
                    'meaning' => 'طَاوِلَةُ الدِّرَاسَةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image67.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: حرف الباء (ب) وفضاء البيت',
                'lesson_sentence' => 'بَابُ قِسْمِنَا مَفْتُوحٌ لِلْجَمِيعِ.',
                'vocabulary' => [
                [
                    'word' => 'بَابٌ',
                    'clean' => 'باب',
                    'meaning' => 'مَدْخَلُ الْغُرْفَةِ أَوِ الْبَيْتِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM4/vocab/image40.png'
                ],
                [
                    'word' => 'بَيْتٌ',
                    'clean' => 'بيت',
                    'meaning' => 'مَسْكَنُ الْعَائِلَةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM4/vocab/image44.png'
                ],
                [
                    'word' => 'بِنْتٌ',
                    'clean' => 'بنت',
                    'meaning' => 'فَتَاةٌ صَغِيرَةٌ مُهَذَّبَةٌ',
                    'image' => '/assets/lesson-images/N1/P1/SEM4/vocab/image46.png'
                ],
                [
                    'word' => 'بُرْتُقَالٌ',
                    'clean' => 'برتقال',
                    'meaning' => 'فَاكِهَةٌ شِتَوِيَّةٌ لَذِيذَةٌ',
                    'image' => '/assets/lesson-images/N1/P1/SEM4/vocab/image52.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: حرف السين (س) وتنظيم الوقت',
                'lesson_sentence' => 'فِي قِسْمِنَا سَاعَةٌ حَائِطِيَّةٌ كَبِيرَةٌ.',
                'vocabulary' => [
                [
                    'word' => 'سَرِيرٌ',
                    'clean' => 'سرير',
                    'meaning' => 'مَكَانُ النَّوْمِ وَالرَّاحَةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM5/vocab/image84.png'
                ],
                [
                    'word' => 'سَاعَةٌ',
                    'clean' => 'ساعة',
                    'meaning' => 'أَدَاةُ مَعْرِفَةِ الْوَقْتِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM5/vocab/image85.png'
                ],
                [
                    'word' => 'سَمَكٌ',
                    'clean' => 'سمك',
                    'meaning' => 'كَائِنٌ يَعِيشُ فِي الْمَاءِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM5/vocab/image84.png'
                ],
                [
                    'word' => 'سَيَّارَةٌ',
                    'clean' => 'سيارة',
                    'meaning' => 'مَرْكَبَةٌ لِنَقْلِ الرُّكَّابِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM5/vocab/image85.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM6',
                'week_number' => 6,
                'week_title' => 'الأسبوع 6: تقويم ودعم الحروف المدروسة (د، ر، م، ب، س)',
                'lesson_sentence' => 'تَعَاوَنَ الْأَصْدِقَاءُ فِي تَرْتِيبِ الْقِسْمِ.',
                'vocabulary' => [
                [
                    'word' => 'دَفْتَرٌ',
                    'clean' => 'دفتر',
                    'meaning' => 'كُرَّاسَةُ الْكِتَابَةِ',
                    'image' => '/assets/lesson-images/SEM1/vocab/image62.png'
                ],
                [
                    'word' => 'رَسْمٌ',
                    'clean' => 'رسم',
                    'meaning' => 'لَوْحَةٌ فَنِّيَّةٌ',
                    'image' => '/assets/lesson-images/SEM1/vocab/image64.png'
                ],
                [
                    'word' => 'مِحْفَظَةٌ',
                    'clean' => 'محفظة',
                    'meaning' => 'حَقِيبَةُ التِّلْمِيذِ',
                    'image' => '/assets/lesson-images/SEM1/vocab/image65.png'
                ],
                [
                    'word' => 'سَاعَةٌ',
                    'clean' => 'ساعة',
                    'meaning' => 'أَدَاةُ الْوَقْتِ',
                    'image' => '/assets/lesson-images/SEM1/vocab/image69.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N1',
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
