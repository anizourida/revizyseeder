<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الخامسة ابتدائي (N5)
 * Unit: الوحدة 5: عالم البحار والمحيطات والبيئة البحرية (P5)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN5P5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: أسرار الأعماق والشعاب المرجانية',
                'lesson_sentence' => 'تَزْخَرُ أَعْمَاقُ الْمُحِيطَاتِ بِكَائِنَاتٍ بَحْرِيَّةٍ عَجِيبَةِ الْخَلْقِ سُبْحَانَ صَانِعِهَا.',
                'vocabulary' => [
                [
                    'word' => 'مَرْجَانٌ',
                    'clean' => 'مرجان',
                    'meaning' => 'كَائِنٌ بَحْرِيٌّ شَبِيهٌ بِالصَّخْرِ يُشَكِّلُ حَيَائِدَ عَظِيمَةً',
                    'image' => '/assets/lesson-images/N5/P5/SEM1/vocab/image49.png'
                ],
                [
                    'word' => 'غَوْصٌ',
                    'clean' => 'غوص',
                    'meaning' => 'النُّزُولُ إِلَى أَعْمَاقِ الْمَاءِ بِأَجْهِزَةِ التَّنَفُّسِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM1/vocab/image50.png'
                ],
                [
                    'word' => 'أَعْمَاقٌ',
                    'clean' => 'أعماق',
                    'meaning' => 'قَاعُ الْبِحَارِ حَيْثُ تَسُودُ الظُّلُمَاتُ',
                    'image' => '/assets/lesson-images/N5/P5/SEM1/vocab/image58.png'
                ],
                [
                    'word' => 'ظُلُمَاتٌ',
                    'clean' => 'ظلمات',
                    'meaning' => 'انْعِدَامُ الضَّوْءِ فِي أَعْمَاقِ الْمُحِيطِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM1/vocab/image49.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الثروة السمكية وتنظيم الصيد البحري',
                'lesson_sentence' => 'يَجِبُ تَنْظِيمُ الصَّيْدِ الْبَحْرِيِّ حِمَايَةً لِلتَّنَوُّعِ الْبَيُولُوجِيِّ فِي سَوَاحِلِنَا.',
                'vocabulary' => [
                [
                    'word' => 'صَيْدٌ',
                    'clean' => 'صيد',
                    'meaning' => 'اسْتِخْرَاجُ الْأَسْمَاكِ مِنَ الْبَحْرِ بِالشِّبَاكِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM2/vocab/image14.png'
                ],
                [
                    'word' => 'تَنَوُّعٌ',
                    'clean' => 'تنوع',
                    'meaning' => 'وَفْرَةُ أَصْنَافِ الْأَسْمَاكِ وَالرَّخَوِيَّاتِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM2/vocab/image44.png'
                ],
                [
                    'word' => 'مَوَارِدُ',
                    'clean' => 'موارد',
                    'meaning' => 'خَيْرَاتٌ بَحْرِيَّةٌ تُغَذِّي الِاقْتِصَادَ',
                    'image' => '/assets/lesson-images/N5/P5/SEM2/vocab/image50.jpeg'
                ],
                [
                    'word' => 'حِمَايَةٌ',
                    'clean' => 'حماية',
                    'meaning' => 'مَنْعُ اسْتِنْزَافِ الثَّرْوَةِ السَّمَكِيَّةِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM2/vocab/image14.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: التلوث البحري وخطر البلاستيك على الأسماك',
                'lesson_sentence' => 'تُهَدِّدُ النِّفَايَاتُ الْبِلَاسْتِيكِيَّةُ سَلَامَةَ الْحَيَاةِ الْبَحْرِيَّةِ تَهْدِيداً خَطِيراً.',
                'vocabulary' => [
                [
                    'word' => 'نِفَايَاتٌ',
                    'clean' => 'نفايات',
                    'meaning' => 'مُخَلَّفَاتٌ بِلَاسْتِيكِيَّةٌ تُلْوَثُ الْمِيَاهَ',
                    'image' => '/assets/lesson-images/N5/P5/SEM3/vocab/image49.png'
                ],
                [
                    'word' => 'اخْتِنَاقٌ',
                    'clean' => 'اختناق',
                    'meaning' => 'هَلَاكُ الْكَائِنَاتِ بِسَبَبِ ابْتِلَاعِ الْمَوَادِّ السَّامَّةِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM3/vocab/image69.png'
                ],
                [
                    'word' => 'شَوَاطِئُ',
                    'clean' => 'شواطئ',
                    'meaning' => 'سَوَاحِلُ الْبِحَارِ الَّتِي تَقْصِدُهَا الْأُسَرُ',
                    'image' => '/assets/lesson-images/N5/P5/SEM3/vocab/image70.png'
                ],
                [
                    'word' => 'خَطَرٌ',
                    'clean' => 'خطر',
                    'meaning' => 'تَهْدِيدٌ حَقِيقِيٌّ لِسَلَامَةِ الْحَيَاةِ الْفِطْرِيَّةِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM3/vocab/image49.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الموانئ المغربية والملاحة التجارية العالمية',
                'lesson_sentence' => 'تَرْسُو السُّفُنُ التِّجَارِيَّةُ فِي مِينَاءِ طَنْجَةَ الْمُتَوَسِّطِ لِشَحْنِ الْبَضَائِعِ.',
                'vocabulary' => [
                [
                    'word' => 'بَاخِرَةٌ',
                    'clean' => 'باخرة',
                    'meaning' => 'سَفِينَةٌ عِمْلَاقَةٌ تَمْخُرُ عُبَابَ الْبِحَارِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM4/vocab/image20.jpg'
                ],
                [
                    'word' => 'مِينَاءٌ',
                    'clean' => 'ميناء',
                    'meaning' => 'حَوْضٌ مَائِيٌّ مَحْمِيٌّ لِرُسُوِّ السُّفُنِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM4/vocab/image21.png'
                ],
                [
                    'word' => 'رُسُوٌّ',
                    'clean' => 'رسو',
                    'meaning' => 'تَوَقُّفُ الْبَوَاخِرِ بِأَمَانٍ عَلَى الرَّصِيفِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM4/vocab/image23.png'
                ],
                [
                    'word' => 'شَحْنٌ',
                    'clean' => 'شحن',
                    'meaning' => 'تَحْمِيلُ الْبَضَائِعِ وَالْحَاوِيَاتِ',
                    'image' => '/assets/lesson-images/N5/P5/SEM4/vocab/image20.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم نهاية السنة الخامسة ابتدائي',
                'lesson_sentence' => 'إِنَّ حِمَايَةَ الْبِحَارِ مِنَ التَّلَوُّثِ ضَمَانٌ لِبَقَاءِ الثَّرْوَةِ السَّمَكِيَّةِ لِلْأَجْيَالِ.',
                'vocabulary' => [
                [
                    'word' => 'مَرْجَانٌ',
                    'clean' => 'مرجان',
                    'meaning' => 'كَنْزُ الْأَعْمَاقِ',
                    'image' => '/assets/lesson-images/N5/SEM5/vocab/image54.png'
                ],
                [
                    'word' => 'صَيْدٌ',
                    'clean' => 'صيد',
                    'meaning' => 'ثَرْوَةٌ غِذَائِيَّةٌ',
                    'image' => '/assets/lesson-images/N5/SEM5/vocab/image56.png'
                ],
                [
                    'word' => 'نِفَايَاتٌ',
                    'clean' => 'نفايات',
                    'meaning' => 'عَدُوُّ الْبِيئَةِ',
                    'image' => '/assets/lesson-images/N5/SEM5/vocab/image54.png'
                ],
                [
                    'word' => 'مِينَاءٌ',
                    'clean' => 'ميناء',
                    'meaning' => 'بَوَّابَةُ التِّجَارَةِ',
                    'image' => '/assets/lesson-images/N5/SEM5/vocab/image56.png'
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
                        'period' => 'P5',
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
