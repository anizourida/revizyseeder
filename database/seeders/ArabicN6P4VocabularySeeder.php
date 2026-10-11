<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة السادسة ابتدائي (N6)
 * Unit: الوحدة 4: الطاقة والتنمية المستدامة والاحتباس الحراري (P4)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN6P4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الطاقات النظيفة والرهان على الهيدروجين الأخضر',
                'lesson_sentence' => 'يُعَدُّ الْهَيْدْرُوجِينُ الْأَخْضَرُ رِهَاناً اسْتِرَاتِيجِيّاً لِلطَّاقَةِ النَّظِيفَةِ فِي الْمَغْرِبِ.',
                'vocabulary' => [
                [
                    'word' => 'رِيَاحِيَّةٌ',
                    'clean' => 'رياحية',
                    'meaning' => 'طَاقَةٌ كَهْرَبَائِيَّةٌ مُوَلَّدَةٌ مِنْ حَرَكَةِ الرِّيَاحِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM1/vocab/image38.png'
                ],
                [
                    'word' => 'هَيْدْرُوجِينٌ',
                    'clean' => 'هيدروجين',
                    'meaning' => 'وَقُودٌ نَظِيفٌ يُنْتَجُ بِاسْتِعْمَالِ الْمِيَاهِ وَالطَّاقَاتِ الْمُتَجَدِّدَةِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM1/vocab/image39.png'
                ],
                [
                    'word' => 'كَهْرَمَائِيَّةٌ',
                    'clean' => 'كهرمائية',
                    'meaning' => 'طَاقَةٌ نَاتِجَةٌ عَنْ قُوَّةِ تَدَفُّقِ مِيَاهِ السُّدُودِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM1/vocab/image40.png'
                ],
                [
                    'word' => 'انْبِعَاثَاتٌ',
                    'clean' => 'انبعاثات',
                    'meaning' => 'غَازَاتٌ سَامَّةٌ تَنْبَعِثُ مِنَ الْمَصَانِعِ وَالسَّيَّارَاتِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM1/vocab/image38.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: ظاهرة الاحتباس الحراري وذوبان الجليد القطبي',
                'lesson_sentence' => 'يُؤَدِّي ارْتِفَاعُ دَرَجَاتِ الْحَرَارَةِ إِلَى تَسَارُعِ ذَوَبَانِ الْجَلِيدِ فِي الْقُطْبَيْنِ.',
                'vocabulary' => [
                [
                    'word' => 'غَازَاتٌ',
                    'clean' => 'غازات',
                    'meaning' => 'أَبْخِرَةٌ كَالْكَرْبُونِ تَحْبِسُ الْحَرَارَةَ دَاخِلَ الْغِلَافِ الْجَوِّيِّ',
                    'image' => '/assets/lesson-images/N6/P4/SEM2/vocab/image40.png'
                ],
                [
                    'word' => 'ارْتِفَاعٌ',
                    'clean' => 'ارتفاع',
                    'meaning' => 'زِيَادَةٌ مُقْلِقَةٌ فِي دَرَجَاتِ حَرَارَةِ الْكَوْكَبِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM2/vocab/image41.png'
                ],
                [
                    'word' => 'ذَوَبَانٌ',
                    'clean' => 'ذوبان',
                    'meaning' => 'تَحَوُّلُ الْجَلِيدِ إِلَى مِيَاهٍ تَرْفَعُ مُسْتَوَى الْبِحَارِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM2/vocab/image42.png'
                ],
                [
                    'word' => 'جَلِيدٌ',
                    'clean' => 'جليد',
                    'meaning' => 'كُتَلٌ ثَلْجِيَّةٌ ضَخْمَةٌ فِي الْقُطْبَيْنِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM2/vocab/image40.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الاقتصاد الدائري وإعادة تدوير النفايات',
                'lesson_sentence' => 'يَهْدِفُ الِاقْتِصَادُ الدَّائِرِيُّ إِلَى إِعَادَةِ تَدْوِيرِ الْمَوَادِّ وَتَقْلِيلِ النِّفَايَاتِ كَثِيراً.',
                'vocabulary' => [
                [
                    'word' => 'تَدْوِيرٌ',
                    'clean' => 'تدوير',
                    'meaning' => 'إِعَادَةُ تَصْنِيعِ الْمَوَادِّ الْمُسْتَعْمَلَةِ كَالْبِلَاسْتِيكِ وَالْوَرَقِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM3/vocab/image36.png'
                ],
                [
                    'word' => 'اسْتِرْجَاعٌ',
                    'clean' => 'استرجاع',
                    'meaning' => 'فَرْزُ النِّفَايَاتِ لِلِاسْتِفَادَةِ مِنْهَا ثَانِيَةً',
                    'image' => '/assets/lesson-images/N6/P4/SEM3/vocab/image37.png'
                ],
                [
                    'word' => 'اسْتِهْلَاكٌ',
                    'clean' => 'استهلاك',
                    'meaning' => 'اسْتِعْمَالُ الْمَوَارِدِ بِعَقْلَانِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P4/SEM3/vocab/image38.png'
                ],
                [
                    'word' => 'تَرْشِيدٌ',
                    'clean' => 'ترشيد',
                    'meaning' => 'حُسْنُ التَّصَرُّفِ وَالْحَدُّ مِنَ التَّبْذِيرِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM3/vocab/image36.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: المدن الذكية الخضراء والمباني الإيكولوجية',
                'lesson_sentence' => 'تَعْتَمِدُ الْمُدُنُ الذَّكِيَّةُ عَلَى النَّقْلِ النَّظِيفِ وَالْمَبَانِي الصَّدِيقَةِ لِلْبِيئَةِ تَمَاماً.',
                'vocabulary' => [
                [
                    'word' => 'اسْتِدَامَةٌ',
                    'clean' => 'استدامة',
                    'meaning' => 'تَوْفِيرُ طَاقَةٍ صَدِيقَةٍ لِلْبِيئَةِ تَدُومُ طَوِيلاً',
                    'image' => '/assets/lesson-images/N6/P4/SEM4/vocab/image2.png'
                ],
                [
                    'word' => 'كَفَاءَةٌ',
                    'clean' => 'كفاءة',
                    'meaning' => 'تَقْلِيلُ هَدْرِ الْكَهْرَبَاءِ فِي الْمَنَازِلِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM4/vocab/image3.png'
                ],
                [
                    'word' => 'بِيئِيَّةٌ',
                    'clean' => 'بيئية',
                    'meaning' => 'مَبَانٍ تُحَافِظُ عَلَى الطَّبِيعَةِ وَتَسْتَفِيدُ مِنَ الشَّمْسِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM4/vocab/image4.png'
                ],
                [
                    'word' => 'تَخْطِيطٌ',
                    'clean' => 'تخطيط',
                    'meaning' => 'هَنْدَسَةٌ عَصْرِيَّةٌ تُعْطِي الْأَوْلَوِيَّةَ لِلْمَسَاحَاتِ الْخَضْرَاءِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM4/vocab/image2.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 4',
                'lesson_sentence' => 'إِنَّ الْمَغْرِبَ أَكْثَرُ الدُّوَلِ إِقْدَاماً عَلَى اسْتِثْمَارِ الطَّاقَاتِ النَّظِيفَةِ.',
                'vocabulary' => [
                [
                    'word' => 'هَيْدْرُوجِينٌ',
                    'clean' => 'هيدروجين',
                    'meaning' => 'وَقُودُ الْغَدِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM5/vocab/image19.jpg'
                ],
                [
                    'word' => 'ارْتِفَاعٌ',
                    'clean' => 'ارتفاع',
                    'meaning' => 'حَرَارَةُ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM5/vocab/image20.png'
                ],
                [
                    'word' => 'تَدْوِيرٌ',
                    'clean' => 'تدوير',
                    'meaning' => 'صِنَاعَةُ الْأَمَلِ',
                    'image' => '/assets/lesson-images/N6/P4/SEM5/vocab/image22.png'
                ],
                [
                    'word' => 'اسْتِدَامَةٌ',
                    'clean' => 'استدامة',
                    'meaning' => 'نَمَاءٌ مُسْتَمِرٌّ',
                    'image' => '/assets/lesson-images/N6/P4/SEM5/vocab/image19.jpg'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N6',
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
