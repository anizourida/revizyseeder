<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الخامسة ابتدائي (N5)
 * Unit: الوحدة 2: الواجبات والحقوق وسيادة القانون (P2)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN5P2VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: حقوق الطفل الأساسية في الرعاية والتعليم',
                'lesson_sentence' => 'يَنْصُّ الْقَانُونُ صَرَاحَةً عَلَى حَقِّ كُلِّ طِفْلٍ فِي التَّعْلِيمِ وَالرِّعَايَةِ الشَّامِلَةِ.',
                'vocabulary' => [
                [
                    'word' => 'تَعْلِيمٌ',
                    'clean' => 'تعليم',
                    'meaning' => 'تَلَقِّي الْمَعَارِفِ وَتَطْوِيرُ الْقُدُرَاتِ فِي الْمَدْرَسَةِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM1/vocab/image74.png'
                ],
                [
                    'word' => 'رِعَايَةٌ',
                    'clean' => 'رعاية',
                    'meaning' => 'تَوْفِيرُ الْحِمَايَةِ الصِّحِّيَّةِ وَالِاجْتِمَاعِيَّةِ لِلصِّغَارِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM1/vocab/image77.png'
                ],
                [
                    'word' => 'حِمَايَةٌ',
                    'clean' => 'حماية',
                    'meaning' => 'صَوْنُ الطِّفْلِ مِنْ كُلِّ أَشْكَالِ الِاسْتِغْلَالِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM1/vocab/image78.png'
                ],
                [
                    'word' => 'مَسْؤُولِيَّةٌ',
                    'clean' => 'مسؤولية',
                    'meaning' => 'وَاجِبُ الْمُجْتَمَعِ وَالْأُسْرَةِ تِجَاهَ الطُّفُولَةِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM1/vocab/image74.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: أداء الواجب بأمانة وإخلاص',
                'lesson_sentence' => 'يُؤَدِّي الْمُوَاطَنُ الصَّالِحُ وَاجِبَاتِهِ صَبَاحَ مَسَاءَ بِإِخْلَاصٍ وَضَمِيرٍ حَيٍّ.',
                'vocabulary' => [
                [
                    'word' => 'نَزَاهَةٌ',
                    'clean' => 'نزاهة',
                    'meaning' => 'اسْتِقَامَةٌ وَعِفَّةٌ وَبُعْدٌ عَنْ كُلِّ فَسَادٍ',
                    'image' => '/assets/lesson-images/N5/P2/SEM2/vocab/image30.jpg'
                ],
                [
                    'word' => 'إِخْلَاصٌ',
                    'clean' => 'إخلاص',
                    'meaning' => 'تَفَانٍ فِي أَدَاءِ الْمَهَامِّ دُونَ رِيَاءٍ',
                    'image' => '/assets/lesson-images/N5/P2/SEM2/vocab/image31.png'
                ],
                [
                    'word' => 'ضَمِيرٌ',
                    'clean' => 'ضمير',
                    'meaning' => 'اسْتِعْدَادٌ نَفْسِيٌّ لِلتَّمْيِيزِ بَيْنَ الْحَقِّ وَالْبَاطِلِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM2/vocab/image33.png'
                ],
                [
                    'word' => 'الْتِزَامٌ',
                    'clean' => 'التزام',
                    'meaning' => 'الْوَفَاءُ بِالْعُهُودِ وَاحْتِرَامُ الْقَوَانِينِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM2/vocab/image30.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: العدالة والمساواة وتكافؤ الفرص',
                'lesson_sentence' => 'تَتَحَقَّقُ الْعَدَالَةُ الِاجْتِمَاعِيَّةُ بِتَكْرِيسِ مَبْدَأِ تَكَافُؤِ الْفُرَصِ بَيْنَ الْجَمِيعِ.',
                'vocabulary' => [
                [
                    'word' => 'إِنْصَافٌ',
                    'clean' => 'إنصاف',
                    'meaning' => 'إِعْطَاءُ كُلِّ ذِي حَقٍّ حَقَّهُ بِعَدْلٍ تَامٍّ',
                    'image' => '/assets/lesson-images/N5/P2/SEM3/vocab/image84.png'
                ],
                [
                    'word' => 'تَكَافُؤٌ',
                    'clean' => 'تكافؤ',
                    'meaning' => 'تَسَاوِي الْفُرَصِ بَيْنَ جَمِيعِ الْمُوَاطِنِينَ',
                    'image' => '/assets/lesson-images/N5/P2/SEM3/vocab/image85.png'
                ],
                [
                    'word' => 'كَرَامَةٌ',
                    'clean' => 'كرامة',
                    'meaning' => 'قِيمَةٌ إِنْسَانِيَّةٌ غَالِيَةٌ تَحْرُمُ إِهَانَتُهَا',
                    'image' => '/assets/lesson-images/N5/P2/SEM3/vocab/image86.png'
                ],
                [
                    'word' => 'سِيَادَةٌ',
                    'clean' => 'سيادة',
                    'meaning' => 'عُلُوُّ كَلِمَةِ الْقَانُونِ وَنَفَاذُهُ عَلَى الْكُلِّ',
                    'image' => '/assets/lesson-images/N5/P2/SEM3/vocab/image84.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: حرية التعبير واحترام الاختلاف',
                'lesson_sentence' => 'يُعَدُّ قَبُولُ الرَّأْيِ الْمُخَالِفِ دَلِيلاً قَاطِعاً عَلَى النُّضْجِ وَالْوَعْيِ الْحَضَارِيِّ.',
                'vocabulary' => [
                [
                    'word' => 'تَعَدُّدٌ',
                    'clean' => 'تعدد',
                    'meaning' => 'تَنَوُّعُ الْآرَاءِ وَالْأَفْكَارِ دَاخِلَ الْمُجْتَمَعِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM4/vocab/image32.png'
                ],
                [
                    'word' => 'حِوَارٌ',
                    'clean' => 'حوار',
                    'meaning' => 'مُنَاقَشَةٌ مَوْضُوعِيَّةٌ تَقُومُ عَلَى الْحُجَجِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM4/vocab/image39.png'
                ],
                [
                    'word' => 'اخْتِلَافٌ',
                    'clean' => 'اختلاف',
                    'meaning' => 'تَبَايُنٌ طَبِيعِيٌّ يُغْنِي التَّفْكِيرَ وَلَا يُفْسِدُ الْوُدَّ',
                    'image' => '/assets/lesson-images/N5/P2/SEM4/vocab/image40.png'
                ],
                [
                    'word' => 'رُقِيٌّ',
                    'clean' => 'رقي',
                    'meaning' => 'تَحَضُّرٌ وَسُمُوٌّ فِي طَرِيقَةِ التَّوَاصُلِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM4/vocab/image32.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 2',
                'lesson_sentence' => 'يَقْتَرِنُ الِالْتِزَامُ بِالْوَاجِبَاتِ بِالتَّمَتُّعِ بِالْحُقُوقِ فِي ظِلِّ سِيَادَةِ الْقَانُونِ.',
                'vocabulary' => [
                [
                    'word' => 'تَعْلِيمٌ',
                    'clean' => 'تعليم',
                    'meaning' => 'حَقٌّ لِلْجَمِيعِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM5/vocab/image14.png'
                ],
                [
                    'word' => 'نَزَاهَةٌ',
                    'clean' => 'نزاهة',
                    'meaning' => 'اسْتِقَامَةُ الضَّمِيرِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM5/vocab/image39.png'
                ],
                [
                    'word' => 'إِنْصَافٌ',
                    'clean' => 'إنصاف',
                    'meaning' => 'تَحْقِيقُ الْعَدْلِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM5/vocab/image41.png'
                ],
                [
                    'word' => 'رُقِيٌّ',
                    'clean' => 'رقي',
                    'meaning' => 'سُمُوُّ الْفِكْرِ',
                    'image' => '/assets/lesson-images/N5/P2/SEM5/vocab/image14.png'
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
