<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثانية ابتدائي (N2)
 * Unit: الوحدة 5: عالم الحيوان والطبيعة الخلابة (P5)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN2P5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الحيوانات الأليفة في القرية',
                'lesson_sentence' => 'يَرْعَى الرَّاعِي الْأَغْنَامَ فِي الْمَرْعَى الْأَخْضَرِ.',
                'vocabulary' => [
                [
                    'word' => 'خَرُوفٌ',
                    'clean' => 'خروف',
                    'meaning' => 'حَيَوَانٌ أَلِيفٌ ذُو صُوفٍ كَثِيفٍ',
                    'image' => '/assets/lesson-images/N2/P5/SEM1/vocab/image15.jpg'
                ],
                [
                    'word' => 'بَقَرَةٌ',
                    'clean' => 'بقرة',
                    'meaning' => 'حَيَوَانٌ يُعْطِينَا الْحَلِيبَ اللَّذِيذَ',
                    'image' => '/assets/lesson-images/N2/P5/SEM1/vocab/image18.png'
                ],
                [
                    'word' => 'حِصَانٌ',
                    'clean' => 'حصان',
                    'meaning' => 'حَيَوَانٌ قَوِيٌّ وَسَرِيعُ الْعَدْوِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM1/vocab/image48.png'
                ],
                [
                    'word' => 'أَلِيفٌ',
                    'clean' => 'أليف',
                    'meaning' => 'حَيَوَانٌ يَعِيشُ مَعَ الْإِنْسَانِ بِسَلَامٍ',
                    'image' => '/assets/lesson-images/N2/P5/SEM1/vocab/image15.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الحيوانات المفترسة في الغابة',
                'lesson_sentence' => 'يَعِيشُ الْأَسَدُ فِي الْغَابَةِ الْكَثِيفَةِ بِحُرِّيَّةٍ.',
                'vocabulary' => [
                [
                    'word' => 'أَسَدٌ',
                    'clean' => 'أسد',
                    'meaning' => 'حَيَوَانٌ مُفْتَرِسٌ يُلَقَّبُ بِمَلِكِ الْغَابَةِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM2/vocab/image15.jpg'
                ],
                [
                    'word' => 'نَمِرٌ',
                    'clean' => 'نمر',
                    'meaning' => 'حَيَوَانٌ رَشِيقٌ ذُو فَرْوٍ مُخَطَّطٍ',
                    'image' => '/assets/lesson-images/N2/P5/SEM2/vocab/image16.png'
                ],
                [
                    'word' => 'ذِئْبٌ',
                    'clean' => 'ذئب',
                    'meaning' => 'حَيَوَانٌ ذَكِيٌّ يَعِيشُ فِي مَجْمُوعَاتٍ',
                    'image' => '/assets/lesson-images/N2/P5/SEM2/vocab/image48.png'
                ],
                [
                    'word' => 'غَابَةٌ',
                    'clean' => 'غابة',
                    'meaning' => 'مَوْطِنُ الْحَيَوَانَاتِ الْوَاسِعُ',
                    'image' => '/assets/lesson-images/N2/P5/SEM2/vocab/image15.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الطيور والفراشات المغردة',
                'lesson_sentence' => 'تَبْنِي الْحَمَامَةُ عُشَّهَا الصَّغِيرَ بَيْنَ أَغْصَانِ الزَّيْتُونِ.',
                'vocabulary' => [
                [
                    'word' => 'حَمَامَةٌ',
                    'clean' => 'حمامة',
                    'meaning' => 'طَائِرٌ وَدِيعٌ يَرْمُزُ لِلسَّلَامِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM3/vocab/image28.png'
                ],
                [
                    'word' => 'عُشٌّ',
                    'clean' => 'عش',
                    'meaning' => 'مَسْكَنُ الطُّيُورِ فَوْقَ الْأَغْصَانِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM3/vocab/image29.png'
                ],
                [
                    'word' => 'جَنَاحٌ',
                    'clean' => 'جناح',
                    'meaning' => 'عُضْوُ الطَّيَرَانِ فِي الْهَوَاءِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM3/vocab/image30.png'
                ],
                [
                    'word' => 'فَرَاشَةٌ',
                    'clean' => 'فراشة',
                    'meaning' => 'حَشَرَةٌ زَاهِيَةُ الْأَلْوَانِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM3/vocab/image28.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الماء والربيع والشلالات',
                'lesson_sentence' => 'يَتَدَفَّقُ الْمَاءُ الْعَذْبُ فِي الْجَدْوَلِ الصَّافِي فَرِحاً.',
                'vocabulary' => [
                [
                    'word' => 'جَدْوَلٌ',
                    'clean' => 'جدول',
                    'meaning' => 'مَجْرَى مَاءٍ صَغِيرٌ رَقْرَاقٌ',
                    'image' => '/assets/lesson-images/N2/P5/SEM4/vocab/image33.png'
                ],
                [
                    'word' => 'زُهُورٌ',
                    'clean' => 'زهور',
                    'meaning' => 'وُرُودٌ مُتَفَتِّحَةٌ فِي الرَّبِيعِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM4/vocab/image34.png'
                ],
                [
                    'word' => 'رَبِيعٌ',
                    'clean' => 'ربيع',
                    'meaning' => 'فَصْلُ الْخُضْرَةِ وَالْجَمَالِ',
                    'image' => '/assets/lesson-images/N2/P5/SEM4/vocab/image40.png'
                ],
                [
                    'word' => 'شَلَّالٌ',
                    'clean' => 'شلال',
                    'meaning' => 'مَاءٌ يَنْحَدِرُ مِنْ مَكَانٍ مُرْتَفِعٍ',
                    'image' => '/assets/lesson-images/N2/P5/SEM4/vocab/image33.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم الحصيلة السنوية ودعم التعلمات',
                'lesson_sentence' => 'نُحَافِظُ عَلَى جَمَالِ الطَّبِيعَةِ وَنَرْفُقُ بِالْحَيَوَانَاتِ دَائِماً.',
                'vocabulary' => [
                [
                    'word' => 'خَرُوفٌ',
                    'clean' => 'خروف',
                    'meaning' => 'حَيَوَانٌ أَلِيفٌ',
                    'image' => '/assets/lesson-images/N2/SEM5/vocab/image56.png'
                ],
                [
                    'word' => 'حَمَامَةٌ',
                    'clean' => 'حمامة',
                    'meaning' => 'رَمْزُ السَّلَامِ',
                    'image' => '/assets/lesson-images/N2/SEM5/vocab/image56.png'
                ],
                [
                    'word' => 'شَلَّالٌ',
                    'clean' => 'شلال',
                    'meaning' => 'مَاءٌ مُتَدَفِّقٌ',
                    'image' => '/assets/lesson-images/N2/SEM5/vocab/image56.png'
                ],
                [
                    'word' => 'غَابَةٌ',
                    'clean' => 'غابة',
                    'meaning' => 'فَضَاءٌ طَبِيعِيٌّ',
                    'image' => '/assets/lesson-images/N2/SEM5/vocab/image56.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N2',
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
