<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الرابعة ابتدائي (N4)
 * Unit: الوحدة 1: الحضارة المغربية العريقة (Default P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: المعالم التاريخية وصومعة حسان',
                'lesson_sentence' => 'تَقِفُ صَوْمَعَةُ حَسَّانَ شَامِخَةً تَشْهَدُ عَلَى عَرَاقَةِ الْحَضَارَةِ الْمَغْرِبِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'صَوْمَعَةٌ',
                    'clean' => 'صومعة',
                    'meaning' => 'مِئْذَنَةٌ عَالِيَةٌ تَارِيخِيَّةٌ',
                    'image' => '/assets/lesson-images/N4/P1/SEM1/vocab/image46.png'
                ],
                [
                    'word' => 'مِعْمَارٌ',
                    'clean' => 'معمار',
                    'meaning' => 'فَنُّ الْبِنَاءِ وَالتَّصْمِيمِ الْهَنْدَسِيِّ',
                    'image' => '/assets/lesson-images/N4/P1/SEM1/vocab/image57.png'
                ],
                [
                    'word' => 'فُسَيْفِسَاءُ',
                    'clean' => 'فسيفساء',
                    'meaning' => 'قِطَعٌ صَغِيرَةٌ مُلَوَّنَةٌ تُشَكِّلُ لَوْحَاتٍ جِدَارِيَّةً',
                    'image' => '/assets/lesson-images/N4/P1/SEM1/vocab/image58.png'
                ],
                [
                    'word' => 'شَامِخٌ',
                    'clean' => 'شامخ',
                    'meaning' => 'مُرْتَفِعٌ عَالٍ يَبْعَثُ عَلَى الْفَخْرِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM1/vocab/image59.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: المدن العتيقة والأسوار الخالدة',
                'lesson_sentence' => 'تَزْخَرُ مَدِينَةُ فَاسَ بِأَسْوَارٍ عَتِيقَةٍ وَمَسَاجِدَ خَالِدَةٍ تَسُرُّ الزَّائِرِينَ.',
                'vocabulary' => [
                [
                    'word' => 'قَصَبَاتٌ',
                    'clean' => 'قصبات',
                    'meaning' => 'حُصُونٌ مَنِيعَةٌ قَدِيمَةٌ فِي الْجَنُوبِ الْمَغْرِبِيِّ',
                    'image' => '/assets/lesson-images/N4/P1/SEM2/vocab/image50.jpg'
                ],
                [
                    'word' => 'أَصَالَةٌ',
                    'clean' => 'أصالة',
                    'meaning' => 'عَرَاقَةٌ وَتَمَسُّكٌ بِالْجُذُورِ التَّارِيخِيَّةِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM2/vocab/image54.png'
                ],
                [
                    'word' => 'أَسْوَارٌ',
                    'clean' => 'أسوار',
                    'meaning' => 'جُدْرَانٌ ضَخْمَةٌ تُحِيطُ بِالْمُدُنِ الْقَدِيمَةِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM2/vocab/image55.png'
                ],
                [
                    'word' => 'تُرَاثٌ',
                    'clean' => 'تراث',
                    'meaning' => 'كُلُّ مَا خَلَّفَهُ الْأَجْدَادُ مِنْ مَعَارِفَ وَعِمَارَةٍ',
                    'image' => '/assets/lesson-images/N4/P1/SEM2/vocab/image56.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الموسيقى الأندلسية والتراث الفني',
                'lesson_sentence' => 'يَطْرَبُ الْمُسْتَمِعُونَ لِأَلْحَانِ الْمُوسِيقَى الْأَنْدَلُسِيَّةِ الْأَصِيلَةِ.',
                'vocabulary' => [
                [
                    'word' => 'طَرَبٌ',
                    'clean' => 'طرب',
                    'meaning' => 'فَرَحٌ وَاهْتِزَازٌ نَفْسِيٌّ لِسَمَاعِ النَّغَمِ الْجَمِيلِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM3/vocab/image42.jpg'
                ],
                [
                    'word' => 'أَنْدَلُسِيٌّ',
                    'clean' => 'أندلسي',
                    'meaning' => 'مَنْسُوبٌ إِلَى الْحَضَارَةِ الْأَنْدَلُسِيَّةِ الْعَرِيقَةِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM3/vocab/image43.png'
                ],
                [
                    'word' => 'أَوْتَارٌ',
                    'clean' => 'أوتار',
                    'meaning' => 'خُيُوطُ الْآلَاتِ الْمُوسِيقِيَّةِ كَالْعُودِ وَالْكَمَانِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM3/vocab/image66.png'
                ],
                [
                    'word' => 'إِيقَاعٌ',
                    'clean' => 'إيقاع',
                    'meaning' => 'تَرْتِيبٌ مُنْتَظَمٌ لِلنَّغَمَاتِ وَالضَّرَبَاتِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM3/vocab/image67.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الزي التقليدي والأناقة المغربية',
                'lesson_sentence' => 'يَرْتَدِي الْمَغَارِبَةُ الْجِلْبَابَ وَالْقُفْطَانَ فِي الْأَعْيَادِ وَالْمُنَاسَبَاتِ الْوَطَنِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'جِلْبَابٌ',
                    'clean' => 'جلباب',
                    'meaning' => 'لِبَاسٌ تَقْلِيدِيٌّ مَغْرِبِيٌّ ذُو غِطَاءِ رَأْسٍ',
                    'image' => '/assets/lesson-images/N4/P1/SEM4/vocab/image13.png'
                ],
                [
                    'word' => 'قُفْطَانٌ',
                    'clean' => 'قفطان',
                    'meaning' => 'ثَوْبٌ نِسَائِيٌّ فَاخِرٌ مُطَرَّزٌ بِالْحَرِيرِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM4/vocab/image48.png'
                ],
                [
                    'word' => 'تَطْرِيزٌ',
                    'clean' => 'تطريز',
                    'meaning' => 'تَزْيِينُ الْقُمَاشِ بِخُيُوطِ الذَّهَبِ وَالْفِضَّةِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM4/vocab/image59.png'
                ],
                [
                    'word' => 'أَنَاقَةٌ',
                    'clean' => 'أناقة',
                    'meaning' => 'حُسْنُ الْمَظْهَرِ وَجَمَالُ اللِّبَاسِ',
                    'image' => '/assets/lesson-images/N4/P1/SEM4/vocab/image13.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 1',
                'lesson_sentence' => 'تَتَجَلَّى أَصَالَةُ الْمَغْرِبِ فِي مِعْمَارِهِ الْفَرِيدِ وَتُرَاثِهِ الْخَالِدِ.',
                'vocabulary' => [
                [
                    'word' => 'صَوْمَعَةٌ',
                    'clean' => 'صومعة',
                    'meaning' => 'مَعْلَمَةٌ شَامِخَةٌ',
                    'image' => '/assets/lesson-images/N4/P1/SEM5/vocab/image42.png'
                ],
                [
                    'word' => 'قَصَبَاتٌ',
                    'clean' => 'قصبات',
                    'meaning' => 'حُصُونٌ أَصِيلَةٌ',
                    'image' => '/assets/lesson-images/N4/P1/SEM5/vocab/image43.png'
                ],
                [
                    'word' => 'طَرَبٌ',
                    'clean' => 'طرب',
                    'meaning' => 'نَغَمٌ شَجِيٌّ',
                    'image' => '/assets/lesson-images/N4/P1/SEM5/vocab/image42.png'
                ],
                [
                    'word' => 'قُفْطَانٌ',
                    'clean' => 'قفطان',
                    'meaning' => 'لِبَاسٌ تُرَاثِيٌّ',
                    'image' => '/assets/lesson-images/N4/P1/SEM5/vocab/image43.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N4',
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
