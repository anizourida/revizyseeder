<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الأولى ابتدائي (N1)
 * Unit: الوحدة 2: المدرسة والقرية (P2)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN1P2VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: حرف اللام (ل) وأنشطة القسم',
                'lesson_sentence' => 'كَتَبَتْ لَيْلَى الْحُرُوفَ عَلَى اللَّوْحَةِ.',
                'vocabulary' => [
                [
                    'word' => 'لَوْحَةٌ',
                    'clean' => 'لوحة',
                    'meaning' => 'لَوْحُ الْكِتَابَةِ الْيَدَوِيَّةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM1/vocab/image65.png'
                ],
                [
                    'word' => 'لَيْمُونٌ',
                    'clean' => 'ليمون',
                    'meaning' => 'ثَمَرٌ حَامِضٌ غَنِيٌّ بِالْفِيتَامِينِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM1/vocab/image66.png'
                ],
                [
                    'word' => 'لَبَنٌ',
                    'clean' => 'لبن',
                    'meaning' => 'شَرَابٌ صِحِّيٌّ لَذِيذٌ',
                    'image' => '/assets/lesson-images/N1/P2/SEM1/vocab/image67.png'
                ],
                [
                    'word' => 'لُعْبَةٌ',
                    'clean' => 'لعبة',
                    'meaning' => 'دُمْيَةٌ لِلتَّسْلِيَةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM1/vocab/image65.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حرف الفاء (ف) في الطبيعة',
                'lesson_sentence' => 'تَطِيرُ الْفَرَاشَةُ بَيْنَ الْأَزْهَارِ.',
                'vocabulary' => [
                [
                    'word' => 'فَرَاشَةٌ',
                    'clean' => 'فراشة',
                    'meaning' => 'حَشَرَةٌ جَمِيلَةٌ مُلَوَّنَةُ الْأَجْنِحَةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM2/vocab/image46.png'
                ],
                [
                    'word' => 'فِيلٌ',
                    'clean' => 'فيل',
                    'meaning' => 'حَيَوَانٌ ضَخْمٌ ذُو خُرْطُومٍ',
                    'image' => '/assets/lesson-images/N1/P2/SEM2/vocab/image52.png'
                ],
                [
                    'word' => 'فَمٌ',
                    'clean' => 'فم',
                    'meaning' => 'عُضْوُ النُّطْقِ وَالْأَكْلِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM2/vocab/image53.png'
                ],
                [
                    'word' => 'فَأْرٌ',
                    'clean' => 'فأر',
                    'meaning' => 'حَيَوَانٌ صَغِيرٌ سَرِيعُ الْحَرَكَةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM2/vocab/image46.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حرف النون (ن) ومَعَالِمُ الْقَرْيَةِ',
                'lesson_sentence' => 'يَجْرِي النَّهْرُ صَافِياً قُرْبَ قَرْيَتِنَا.',
                'vocabulary' => [
                [
                    'word' => 'نَافِذَةٌ',
                    'clean' => 'نافذة',
                    'meaning' => 'فَتْحَةُ الْجِدَارِ لِدُخُولِ النُّورِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM3/vocab/image60.png'
                ],
                [
                    'word' => 'نَمْلَةٌ',
                    'clean' => 'نملة',
                    'meaning' => 'حَشَرَةٌ نَشِيطَةٌ تَعْمَلُ بِجِدٍّ',
                    'image' => '/assets/lesson-images/N1/P2/SEM3/vocab/image61.png'
                ],
                [
                    'word' => 'نَجْمٌ',
                    'clean' => 'نجم',
                    'meaning' => 'جِرْمٌ مُضِيءٌ فِي السَّمَاءِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM3/vocab/image69.png'
                ],
                [
                    'word' => 'نَهْرٌ',
                    'clean' => 'نهر',
                    'meaning' => 'مَجْرَى مَاءٍ عَذْبٍ',
                    'image' => '/assets/lesson-images/N1/P2/SEM3/vocab/image60.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: حرف التاء (ت) وثِمَارُ الْبُسْتَانِ',
                'lesson_sentence' => 'أَكَلَ التِّلْمِيذُ تُفَّاحَةً لَذِيذَةً.',
                'vocabulary' => [
                [
                    'word' => 'تِينٌ',
                    'clean' => 'تين',
                    'meaning' => 'فَاكِهَةٌ صَيْفِيَّةٌ مُبَارَكَةٌ',
                    'image' => '/assets/lesson-images/N1/P2/SEM4/vocab/image62.png'
                ],
                [
                    'word' => 'تِلْمِيذٌ',
                    'clean' => 'تلميذ',
                    'meaning' => 'مُتَعَلِّمٌ فِي الْمَدْرَسَةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM4/vocab/image63.png'
                ],
                [
                    'word' => 'تُفَّاحَةٌ',
                    'clean' => 'تفاحة',
                    'meaning' => 'ثَمَرَةٌ حُلْوَةُ الْمَذَاقِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM4/vocab/image64.png'
                ],
                [
                    'word' => 'تَاجٌ',
                    'clean' => 'تاج',
                    'meaning' => 'إِكْلِيلٌ يُوضَعُ فَوْقَ الرَّأْسِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM4/vocab/image62.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: حرف الكاف (ك) وعَالَمُ الْكُتُبِ',
                'lesson_sentence' => 'أَقْرَأُ كِتَاباً مُفِيداً فِي الْمَكْتَبَةِ.',
                'vocabulary' => [
                [
                    'word' => 'كِتَابٌ',
                    'clean' => 'كتاب',
                    'meaning' => 'مَصْدَرُ الْعِلْمِ وَالْمَعْرِفَةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM5/vocab/image63.png'
                ],
                [
                    'word' => 'كُرَةٌ',
                    'clean' => 'كرة',
                    'meaning' => 'أَدَاةُ اللَّعِبِ الدَّائِرِيَّةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM5/vocab/image83.png'
                ],
                [
                    'word' => 'كَلْبٌ',
                    'clean' => 'كلب',
                    'meaning' => 'حَيَوَانٌ أَلِيفٌ وَفِيٌّ',
                    'image' => '/assets/lesson-images/N1/P2/SEM5/vocab/image84.png'
                ],
                [
                    'word' => 'كَوْكَبٌ',
                    'clean' => 'كوكب',
                    'meaning' => 'جِرْمٌ يَدُورُ فِي الْفَضَاءِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM5/vocab/image63.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM6',
                'week_number' => 6,
                'week_title' => 'الأسبوع 6: دعم وتثبيت الحروف (ل، ف، ن، ت، ك)',
                'lesson_sentence' => 'يَتَعَاوَنُ التَّلَامِيذُ فِي مَعْرِضِ الْمَدْرَسَةِ.',
                'vocabulary' => [
                [
                    'word' => 'كِتَابٌ',
                    'clean' => 'كتاب',
                    'meaning' => 'مَصْدَرُ الْمَعْرِفَةِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM6/vocab/image18.jpg'
                ],
                [
                    'word' => 'لَوْحَةٌ',
                    'clean' => 'لوحة',
                    'meaning' => 'أَدَاةُ الرَّسْمِ',
                    'image' => '/assets/lesson-images/N1/P2/SEM6/vocab/image30.png'
                ],
                [
                    'word' => 'تِلْمِيذٌ',
                    'clean' => 'تلميذ',
                    'meaning' => 'مُتَعَلِّمٌ نَشِيطٌ',
                    'image' => '/assets/lesson-images/N1/P2/SEM6/vocab/image18.jpg'
                ],
                [
                    'word' => 'فَرَاشَةٌ',
                    'clean' => 'فراشة',
                    'meaning' => 'كَائِنٌ جَمِيلٌ',
                    'image' => '/assets/lesson-images/N1/P2/SEM6/vocab/image30.png'
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
                        'period' => 'P2',
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
