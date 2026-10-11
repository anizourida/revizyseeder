<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الأولى ابتدائي (N1)
 * Unit: الوحدة 1: الأسرة والمدرسة (P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN1P1VocabularySeeder extends Seeder
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
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image65.png'
                ],
                [
                    'word' => 'مِقْلَمَةٌ',
                    'clean' => 'مقلمة',
                    'meaning' => 'عُلْبَةُ حِفْظِ الْأَقْلَامِ وَالْأَدَوَاتِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image67.png'
                ],
                [
                    'word' => 'مِقَصٌّ',
                    'clean' => 'مقص',
                    'meaning' => 'أَدَاةُ قَصِّ الْأَوْرَاقِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image66.jpeg'
                ],
                [
                    'word' => 'قَلَمُ الرَّصَاصِ',
                    'clean' => 'قلم الرصاص',
                    'meaning' => 'أَدَاةُ الْكِتَابَةِ وَالتَّخْطِيطِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image64.png'
                ],
                [
                    'word' => 'مِسْطَرَةٌ',
                    'clean' => 'مسطرة',
                    'meaning' => 'أَدَاةُ تَسْطِيرِ الدَّفْتَرِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image63.jpeg'
                ],
                [
                    'word' => 'أَقْلَامٌ مُلَوَّنَةٌ',
                    'clean' => 'أقلام ملونة',
                    'meaning' => 'أَقْلَامُ التَّلْوِينِ الْجَمِيلَةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image62.png'
                ],
                [
                    'word' => 'دُودُو',
                    'clean' => 'دودو',
                    'meaning' => 'الْقِرْدُ الصَّغِيرُ بَطَلُ الْحِكَايَةِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image100.png'
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
                    'word' => 'مِحْفَظَةٌ',
                    'clean' => 'محفظة',
                    'meaning' => 'حَقِيبَةُ حَمْلِ الْكُتُبِ وَالدَّفَاتِرِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image75.png'
                ],
                [
                    'word' => 'مَكْتَبٌ',
                    'clean' => 'مكتب',
                    'meaning' => 'طَاوِلَةُ الدِّرَاسَةِ وَالْحَاسُوبِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image74.jpg'
                ],
                [
                    'word' => 'خِزَانَةٌ',
                    'clean' => 'خزانة',
                    'meaning' => 'خِزَانَةُ الْكُتُبِ فِي الْقِسْمِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image73.jpg'
                ],
                [
                    'word' => 'رِيمُ',
                    'clean' => 'ريم',
                    'meaning' => 'التِّلْمِيذَةُ رِيمُ',
                    'image' => '/assets/lesson-images/N1/P1/SEM2/vocab/image61.png'
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
                    'word' => 'مَدْرَسَةٌ',
                    'clean' => 'مدرسة',
                    'meaning' => 'سَاحَةُ الْمَدْرَسَةِ وَالْعَلَمُ الْوَطَنِيُّ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image68.jpg'
                ],
                [
                    'word' => 'حَدِيقَةٌ',
                    'clean' => 'حديقة',
                    'meaning' => 'حَدِيقَةُ الْمَدْرَسَةِ الْمُزْهِرَةُ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image69.jpg'
                ],
                [
                    'word' => 'إِدَارَةٌ',
                    'clean' => 'إدارة',
                    'meaning' => 'مَكْتَبُ الْمُدِيرِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM3/vocab/image67.jpg'
                ],
                [
                    'word' => 'مَقْلَمَةٌ',
                    'clean' => 'مقلمة',
                    'meaning' => 'عُلْبَةُ حِفْظِ الْأَقْلَامِ',
                    'image' => '/assets/lesson-images/N1/P1/SEM1/vocab/image67.png'
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
