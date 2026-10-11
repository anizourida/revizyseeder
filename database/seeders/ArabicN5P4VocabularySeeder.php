<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الخامسة ابتدائي (N5)
 * Unit: الوحدة 4: الابتكار والاختراع والطاقات النظيفة (P4)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN5P4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: المخترعون العظام وقصص الإصرار',
                'lesson_sentence' => 'يَتَطَلَّبُ الِاخْتِرَاعُ شَغَفاً كَبِيراً وَإِصْرَاراً عَلَى تَجَاوُزِ كُلِّ الْعَقَبَاتِ.',
                'vocabulary' => [
                [
                    'word' => 'بَرَاءَةٌ',
                    'clean' => 'براءة',
                    'meaning' => 'شَهَادَةٌ رَسْمِيَّةٌ تَحْمِي حَقَّ الْمُخْتَرِعِ فِي ابْتِكَارِهِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM1/vocab/image40.png'
                ],
                [
                    'word' => 'عَبْقَرِيَّةٌ',
                    'clean' => 'عبقرية',
                    'meaning' => 'تَفَوُّقٌ ذِهْنِيٌّ اسْتِثْنَائِيٌّ',
                    'image' => '/assets/lesson-images/N5/P4/SEM1/vocab/image41.png'
                ],
                [
                    'word' => 'تَجْرِبَةٌ',
                    'clean' => 'تجربة',
                    'meaning' => 'اخْتِبَارٌ عِلْمِيٌّ لِلتَّأَكُّدِ مِنْ صِحَّةِ الْفَرَضِيَّاتِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM1/vocab/image42.png'
                ],
                [
                    'word' => 'إِصْرَارٌ',
                    'clean' => 'إصرار',
                    'meaning' => 'عَزِيمَةٌ قَوِيَّةٌ لَا تَنْثَنِي أَمَامَ الصِّعَابِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM1/vocab/image40.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الطاقات المتجددة ومحطة نور ورزازات',
                'lesson_sentence' => 'تُنْتِجُ مَحَطَّةُ نُور وَرْزَازَات لِلطَّاقَةِ الشَّمْسِيَّةِ كَهْرَبَاءَ نَظِيفَةً وَمُسْتَدَامَةً.',
                'vocabulary' => [
                [
                    'word' => 'شَمْسِيَّةٌ',
                    'clean' => 'شمسية',
                    'meaning' => 'طَاقَةٌ مُسْتَمَدَّةٌ مِنْ أَشِعَّةِ الشَّمْسِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM2/vocab/image14.png'
                ],
                [
                    'word' => 'أَلْوَاحٌ',
                    'clean' => 'ألواح',
                    'meaning' => 'صَفَائِحُ الْتِقَاطِ الضَّوْءِ وَتَحْوِيلِهِ لِكَهْرَبَاءَ',
                    'image' => '/assets/lesson-images/N5/P4/SEM2/vocab/image39.png'
                ],
                [
                    'word' => 'نَظِيفَةٌ',
                    'clean' => 'نظيفة',
                    'meaning' => 'طَاقَةٌ لَا تَنْفُثُ انْبِعَاثَاتٍ مُلَوِّثَةً',
                    'image' => '/assets/lesson-images/N5/P4/SEM2/vocab/image40.png'
                ],
                [
                    'word' => 'مَحَطَّةٌ',
                    'clean' => 'محطة',
                    'meaning' => 'مَرْكَبٌ ضَخْمٌ لِتَوْلِيدِ الْكَهْرَبَاءِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM2/vocab/image14.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الروبوتات والآلات الذكية في المصانع',
                'lesson_sentence' => 'تُنْجِزُ الرُّوبُوتَاتُ الْمَهَامَّ الشَّاقَّةَ فِي الْمَصَانِعِ بِسُرْعَةٍ وَدِقَّةٍ بَالِغَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'إِنْسَانٌ آلِيٌّ',
                    'clean' => 'إنسان آلي',
                    'meaning' => 'آلَةٌ مُبَرْمَجَةٌ لِأَدَاءِ مَهَامَّ مُعَقَّدَةٍ',
                    'image' => '/assets/lesson-images/N5/P4/SEM3/vocab/image71.png'
                ],
                [
                    'word' => 'أَتْمَتَةٌ',
                    'clean' => 'أتمتة',
                    'meaning' => 'تَشْغِيلُ الْمَصَانِعِ بِآلَاتٍ ذَاتِيَّةِ الْحَرَكَةِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM3/vocab/image72.jpeg'
                ],
                [
                    'word' => 'تَحَكُّمٌ',
                    'clean' => 'تحكم',
                    'meaning' => 'إِدَارَةُ الْأَجْهِزَةِ عَنْ بُعْدٍ بِدِقَّةٍ',
                    'image' => '/assets/lesson-images/N5/P4/SEM3/vocab/image73.png'
                ],
                [
                    'word' => 'دِقَّةٌ',
                    'clean' => 'دقة',
                    'meaning' => 'إِنْجَازٌ مُتَنَاهٍ بِلَا أَيْ خَطَأٍ',
                    'image' => '/assets/lesson-images/N5/P4/SEM3/vocab/image71.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: صناعة الطيران والسيارات بالمغرب',
                'lesson_sentence' => 'أَصْبَحَ الْمَغْرِبُ قُطْباً رَائِداً فِي صِنَاعَةِ السَّيَّارَاتِ وَأَجْزَاءِ الطَّائِرَاتِ.',
                'vocabulary' => [
                [
                    'word' => 'تَرْكِيبٌ',
                    'clean' => 'تركيب',
                    'meaning' => 'تَجْمِيعُ أَجْزَاءِ الطَّائِرَاتِ وَالسَّيَّارَاتِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM4/vocab/image14.png'
                ],
                [
                    'word' => 'هَنْدَسَةٌ',
                    'clean' => 'هندسة',
                    'meaning' => 'تَصْمِيمٌ عِلْمِيٌّ مُتَطَوِّرٌ لِلْآلَاتِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM4/vocab/image41.png'
                ],
                [
                    'word' => 'صِنَاعَةٌ',
                    'clean' => 'صناعة',
                    'meaning' => 'قِطَاعُ إِنْتَاجِ الْمَعَدَّاتِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM4/vocab/image42.png'
                ],
                [
                    'word' => 'جَوْدَةٌ',
                    'clean' => 'جودة',
                    'meaning' => 'امْتِيَازٌ فِي الْمَعَايِيرِ الدَّوْلِيَّةِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM4/vocab/image14.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 4',
                'lesson_sentence' => 'يُحَقِّقُ الِابْتِكَارُ الصِّنَاعِيُّ وَالطَّاقِيُّ قَفْزَةً تَنْمَوِيَّةً بَاهِرَةً لِبِلَادِنَا.',
                'vocabulary' => [
                [
                    'word' => 'بَرَاءَةٌ',
                    'clean' => 'براءة',
                    'meaning' => 'حَقُّ الِابْتِكَارِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM5/vocab/image31.png'
                ],
                [
                    'word' => 'أَلْوَاحٌ',
                    'clean' => 'ألواح',
                    'meaning' => 'طَاقَةُ الشَّمْسِ',
                    'image' => '/assets/lesson-images/N5/P4/SEM5/vocab/image34.png'
                ],
                [
                    'word' => 'أَتْمَتَةٌ',
                    'clean' => 'أتمتة',
                    'meaning' => 'تَشْغِيلٌ آلِيٌّ',
                    'image' => '/assets/lesson-images/N5/P4/SEM5/vocab/image35.png'
                ],
                [
                    'word' => 'هَنْدَسَةٌ',
                    'clean' => 'هندسة',
                    'meaning' => 'تَصْمِيمٌ مُتْقَنٌ',
                    'image' => '/assets/lesson-images/N5/P4/SEM5/vocab/image31.png'
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
