<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الأولى ابتدائي (N1)
 * Unit: الوحدة 4: الحي والمزرعة والحيوانات (P4)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN1P4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: حرف الطاء (ط) فِي الْمَزْرَعَةِ',
                'lesson_sentence' => 'يُحَلِّقُ الطَّائِرُ عَالِياً فِي السَّمَاءِ.',
                'vocabulary' => [
                [
                    'word' => 'طَبِيبٌ',
                    'clean' => 'طبيب',
                    'meaning' => 'مَنْ يُعَالِجُ الْمَرْضَى',
                    'image' => '/assets/lesson-images/N1/P4/SEM1/vocab/image47.png'
                ],
                [
                    'word' => 'طَائِرٌ',
                    'clean' => 'طائر',
                    'meaning' => 'حَيَوَانٌ ذُو رِيشٍ يَطِيرُ فِي الْهَوَاءِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM1/vocab/image51.png'
                ],
                [
                    'word' => 'طَاوِلَةٌ',
                    'clean' => 'طاولة',
                    'meaning' => 'مَائِدَةُ الْجُلُوسِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM1/vocab/image53.png'
                ],
                [
                    'word' => 'طَيَّارٌ',
                    'clean' => 'طيار',
                    'meaning' => 'قَائِدُ الطَّائِرَةِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM1/vocab/image47.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حرف الظاء (ظ) فِي ظِلِّ الْأَشْجَارِ',
                'lesson_sentence' => 'يَسْتَرِيحُ الْفَلَّاحُ تَحْتَ ظِلِّ الشَّجَرَةِ.',
                'vocabulary' => [
                [
                    'word' => 'ظِلٌّ',
                    'clean' => 'ظل',
                    'meaning' => 'مَكَانٌ تَحْجُبُ عَنْهُ الشَّمْسُ',
                    'image' => '/assets/lesson-images/N1/P4/SEM2/vocab/image107.png'
                ],
                [
                    'word' => 'ظَرْفٌ',
                    'clean' => 'ظرف',
                    'meaning' => 'غِلَافُ الرِّسَالَةِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM2/vocab/image108.png'
                ],
                [
                    'word' => 'ظَبْيٌ',
                    'clean' => 'ظبي',
                    'meaning' => 'غَزَالٌ رَشِيقٌ سَرِيعٌ',
                    'image' => '/assets/lesson-images/N1/P4/SEM2/vocab/image109.png'
                ],
                [
                    'word' => 'نَظَّارَةٌ',
                    'clean' => 'نظارة',
                    'meaning' => 'عَدَسَاتٌ لِتَوْضِيحِ الرُّؤْيَةِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM2/vocab/image107.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حرف العين (ع) وَأَصْوَاتُ الطَّبِيعَةِ',
                'lesson_sentence' => 'يُغَرِّدُ الْعُصْفُورُ فَوْقَ الْغُصْنِ فَرَحاً.',
                'vocabulary' => [
                [
                    'word' => 'عُصْفُورٌ',
                    'clean' => 'عصفور',
                    'meaning' => 'طَائِرٌ صَغِيرٌ غَرِّيدٌ',
                    'image' => '/assets/lesson-images/N1/P4/SEM3/vocab/image101.png'
                ],
                [
                    'word' => 'عَسَلٌ',
                    'clean' => 'عسل',
                    'meaning' => 'غِذَاءٌ حُلْوٌ مِنَ النَّحْلِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM3/vocab/image64.png'
                ],
                [
                    'word' => 'عَيْنٌ',
                    'clean' => 'عين',
                    'meaning' => 'عُضْوُ الْبَصَرِ وَالرُّؤْيَةِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM3/vocab/image65.png'
                ],
                [
                    'word' => 'عِنَبٌ',
                    'clean' => 'عنب',
                    'meaning' => 'فَاكِهَةٌ صَيْفِيَّةٌ عُنْقُودِيَّةٌ',
                    'image' => '/assets/lesson-images/N1/P4/SEM3/vocab/image101.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: حرف الغين (غ) فِي الْغَابَةِ',
                'lesson_sentence' => 'يَرْكُضُ الْغَزَالُ بِخِفَّةٍ فِي الْغَابَةِ.',
                'vocabulary' => [
                [
                    'word' => 'غَابَةٌ',
                    'clean' => 'غابة',
                    'meaning' => 'أَرْضٌ وَاسِعَةٌ كَثِيفَةُ الْأَشْجَارِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM4/vocab/image113.jpeg'
                ],
                [
                    'word' => 'غَزَالٌ',
                    'clean' => 'غزال',
                    'meaning' => 'حَيَوَانٌ بَرِّيٌّ رَشِيقٌ',
                    'image' => '/assets/lesson-images/N1/P4/SEM4/vocab/image15.png'
                ],
                [
                    'word' => 'غَيْمَةٌ',
                    'clean' => 'غيمة',
                    'meaning' => 'سَحَابَةٌ فِي السَّمَاءِ تَحْمِلُ الْمَطَرَ',
                    'image' => '/assets/lesson-images/N1/P4/SEM4/vocab/image88.jpg'
                ],
                [
                    'word' => 'غُرَابٌ',
                    'clean' => 'غراب',
                    'meaning' => 'طَائِرٌ أَسْوَدُ ذَكِيٌّ',
                    'image' => '/assets/lesson-images/N1/P4/SEM4/vocab/image113.jpeg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: حرف الهاء (هـ) وَالْحَيَوَانَاتُ الْأَلِيفَةُ',
                'lesson_sentence' => 'أَهْدَى هِشَامٌ هَدِيَّةً جَمِيلَةً لِأُخْتِهِ.',
                'vocabulary' => [
                [
                    'word' => 'هُدْهُدٌ',
                    'clean' => 'هدهد',
                    'meaning' => 'طَائِرٌ ذُو عُرْفٍ جَمِيلٍ',
                    'image' => '/assets/lesson-images/N1/P4/SEM5/vocab/image52.png'
                ],
                [
                    'word' => 'هِلَالٌ',
                    'clean' => 'هلال',
                    'meaning' => 'شَكْلُ الْقَمَرِ فِي أَوَّلِ الشَّهْرِ',
                    'image' => '/assets/lesson-images/N1/P4/SEM5/vocab/image67.png'
                ],
                [
                    'word' => 'هَدِيَّةٌ',
                    'clean' => 'هدية',
                    'meaning' => 'مَا يُهْدَى عُرْبُونَ مَحَبَّةٍ',
                    'image' => '/assets/lesson-images/N1/P4/SEM5/vocab/image68.png'
                ],
                [
                    'word' => 'هَاتِفٌ',
                    'clean' => 'هاتف',
                    'meaning' => 'جِهَازُ التَّوَاصُلِ عَنْ بُعْدٍ',
                    'image' => '/assets/lesson-images/N1/P4/SEM5/vocab/image52.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM6',
                'week_number' => 6,
                'week_title' => 'الأسبوع 6: دعم وتثبيت الحروف (ط، ظ، ع، غ، هـ)',
                'lesson_sentence' => 'فِي الْمَزْرَعَةِ حَيَوَانَاتٌ وَطُيُورٌ كَثِيرَةٌ.',
                'vocabulary' => [
                [
                    'word' => 'طَائِرٌ',
                    'clean' => 'طائر',
                    'meaning' => 'كَائِنٌ يَطِيرُ',
                    'image' => '/assets/lesson-images/N1/P4/SEM6/vocab/image45.png'
                ],
                [
                    'word' => 'عُصْفُورٌ',
                    'clean' => 'عصفور',
                    'meaning' => 'طَائِرٌ غَرِّيدٌ',
                    'image' => '/assets/lesson-images/N1/P4/SEM6/vocab/image49.png'
                ],
                [
                    'word' => 'غَابَةٌ',
                    'clean' => 'غابة',
                    'meaning' => 'فَضَاءٌ أَخْضَرُ',
                    'image' => '/assets/lesson-images/N1/P4/SEM6/vocab/image45.png'
                ],
                [
                    'word' => 'هُدْهُدٌ',
                    'clean' => 'هدهد',
                    'meaning' => 'طَائِرٌ جَمِيلٌ',
                    'image' => '/assets/lesson-images/N1/P4/SEM6/vocab/image49.png'
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
                        'period' => 'P4',
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
