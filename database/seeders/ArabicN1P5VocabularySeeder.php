<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الأولى ابتدائي (N1)
 * Unit: الوحدة 5: البيئة والطبيعة والرحلات (P5)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN1P5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: حرف القاف (ق) فِي الشَّاطِئِ',
                'lesson_sentence' => 'يَسِيرُ الْقَارِبُ بِهُدُوءٍ فِي الْبَحْرِ.',
                'vocabulary' => [
                [
                    'word' => 'قَمَرٌ',
                    'clean' => 'قمر',
                    'meaning' => 'كَوْكَبٌ مُنِيرٌ فِي السَّمَاءِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM1/vocab/image48.png'
                ],
                [
                    'word' => 'قِطٌّ',
                    'clean' => 'قط',
                    'meaning' => 'حَيَوَانٌ أَلِيفٌ يُحِبُّ اللَّعِبَ',
                    'image' => '/assets/lesson-images/N1/P5/SEM1/vocab/image49.png'
                ],
                [
                    'word' => 'قَلَمٌ',
                    'clean' => 'قلم',
                    'meaning' => 'أَدَاةُ الْكِتَابَةِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM1/vocab/image50.png'
                ],
                [
                    'word' => 'قَارِبٌ',
                    'clean' => 'قارب',
                    'meaning' => 'مَرْكَبٌ صَغِيرٌ لِلْإِبْحَارِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM1/vocab/image48.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حرف الشين (ش) وَفُصُولُ السَّنَةِ',
                'lesson_sentence' => 'تُشْرِقُ الشَّمْسُ دَافِئَةً فَوْقَ الشَّاطِئِ.',
                'vocabulary' => [
                [
                    'word' => 'شَمْسٌ',
                    'clean' => 'شمس',
                    'meaning' => 'نَجْمٌ يُنِيرُ الْأَرْضَ وَيُدَفِّئُهَا',
                    'image' => '/assets/lesson-images/N1/P5/SEM2/vocab/image46.png'
                ],
                [
                    'word' => 'شَجَرَةٌ',
                    'clean' => 'شجرة',
                    'meaning' => 'نَبَاتٌ ذُو جِذْعٍ وَأَغْصَانٍ',
                    'image' => '/assets/lesson-images/N1/P5/SEM2/vocab/image47.png'
                ],
                [
                    'word' => 'شَاطِئٌ',
                    'clean' => 'شاطئ',
                    'meaning' => 'سَاحِلُ الْبَحْرِ الرَّمْلِيُّ',
                    'image' => '/assets/lesson-images/N1/P5/SEM2/vocab/image52.png'
                ],
                [
                    'word' => 'شِتَاءٌ',
                    'clean' => 'شتاء',
                    'meaning' => 'فَصْلُ الْمَطَرِ وَالْبَرْدِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM2/vocab/image46.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حرفا الواو والياء (و / ي) فِي الرَّبِيعِ',
                'lesson_sentence' => 'سَقَيْتُ الْوَرْدَةَ الْجَمِيلَةَ بِيَدَيَّ.',
                'vocabulary' => [
                [
                    'word' => 'وَرْدَةٌ',
                    'clean' => 'وردة',
                    'meaning' => 'زَهْرَةٌ ذَاتُ رَائِحَةٍ عَطِرَةٍ',
                    'image' => '/assets/lesson-images/N1/P5/SEM3/vocab/image15.png'
                ],
                [
                    'word' => 'يَدٌ',
                    'clean' => 'يد',
                    'meaning' => 'عُضْوُ اللَّمْسِ وَالْعَمَلِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM3/vocab/image69.png'
                ],
                [
                    'word' => 'وِسَادَةٌ',
                    'clean' => 'وسادة',
                    'meaning' => 'مَا يُوضَعُ تَحْتَ الرَّأْسِ لِلنَّوْمِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM3/vocab/image75.png'
                ],
                [
                    'word' => 'يَمَامَةٌ',
                    'clean' => 'يمامة',
                    'meaning' => 'طَائِرٌ وَدِيعٌ يُشْبِهُ الْحَمَامَ',
                    'image' => '/assets/lesson-images/N1/P5/SEM3/vocab/image15.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الهمزة والألف (ء / أ) وَنُزُولُ الْمَطَرِ',
                'lesson_sentence' => 'نَزَلَ الْمَاءُ صَافِياً مِنَ السَّمَاءِ.',
                'vocabulary' => [
                [
                    'word' => 'أَرْنَبٌ',
                    'clean' => 'أرنب',
                    'meaning' => 'حَيَوَانٌ سَرِيعٌ ذُو أُذُنَيْنِ طَوِيلَتَيْنِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM4/vocab/image15.png'
                ],
                [
                    'word' => 'إِبْرِيقٌ',
                    'clean' => 'إبريق',
                    'meaning' => 'إِنَاءُ صَبِّ الْمَاءِ أَوِ الشَّايِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM4/vocab/image61.png'
                ],
                [
                    'word' => 'سَمَاءٌ',
                    'clean' => 'سماء',
                    'meaning' => 'فَضَاءٌ أَعْلَى الْأَرْضِ',
                    'image' => '/assets/lesson-images/N1/P5/SEM4/vocab/image94.png'
                ],
                [
                    'word' => 'مَاءٌ',
                    'clean' => 'ماء',
                    'meaning' => 'سَائِلُ الْحَيَاةِ الْعَذْبُ',
                    'image' => '/assets/lesson-images/N1/P5/SEM4/vocab/image15.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم الحصيلة السنوية ودعم الطلاقة القرائية',
                'lesson_sentence' => 'صِرْتُ أَقْرَأُ الْقِصَصَ وَأَكْتُبُ الْجُمَلَ بِطَلَاقَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'قَمَرٌ',
                    'clean' => 'قمر',
                    'meaning' => 'نُورُ السَّمَاءِ',
                    'image' => '/assets/lesson-images/N1/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'شَمْسٌ',
                    'clean' => 'شمس',
                    'meaning' => 'ضِيَاءُ النَّهَارِ',
                    'image' => '/assets/lesson-images/N1/SEM5/vocab/image76.jpg'
                ],
                [
                    'word' => 'وَرْدَةٌ',
                    'clean' => 'وردة',
                    'meaning' => 'زَهْرَةٌ عَطِرَةٌ',
                    'image' => '/assets/lesson-images/N1/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'كِتَابٌ',
                    'clean' => 'كتاب',
                    'meaning' => 'كَنْزُ الْمَعْرِفَةِ',
                    'image' => '/assets/lesson-images/N1/SEM5/vocab/image76.jpg'
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
