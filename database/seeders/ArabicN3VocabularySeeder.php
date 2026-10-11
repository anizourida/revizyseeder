<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثالثة ابتدائي (N3)
 * Unit: الوحدة 1: عالم الأصدقاء والمشاعر الإنسانية (Default P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الصديق المخلص والوفاء',
                'lesson_sentence' => 'الصَّدِيقُ الصَّالِحُ يُعِينُ صَدِيقَهُ عَلَى الْخَيْرِ فِي الشَّدَائِدِ.',
                'vocabulary' => [
                [
                    'word' => 'صَدَاقَةٌ',
                    'clean' => 'صداقة',
                    'meaning' => 'عَلَاقَةُ مَحَبَّةٍ وَإِخْلَاصٍ بَيْنَ شَخْصَيْنِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM1/vocab/image100.png'
                ],
                [
                    'word' => 'وَفَاءٌ',
                    'clean' => 'وفاء',
                    'meaning' => 'إِخْلَاصٌ فِي الْعَهْدِ وَالثَّبَاتُ عَلَيْهِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM1/vocab/image101.png'
                ],
                [
                    'word' => 'إِخْلَاصٌ',
                    'clean' => 'إخلاص',
                    'meaning' => 'صِدْقُ النِّيَّةِ وَالْعَمَلِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM1/vocab/image88.png'
                ],
                [
                    'word' => 'مَوَدَّةٌ',
                    'clean' => 'مودة',
                    'meaning' => 'مَحَبَّةٌ خَالِصَةٌ وَعَطْفٌ',
                    'image' => '/assets/lesson-images/N3/P1/SEM1/vocab/image97.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: التعاون في اللعب والإيثار',
                'lesson_sentence' => 'نَتَقَاسَمُ الْأَلْعَابَ فِي فَرَحٍ وَتَآخٍ مَعَ جَمِيعِ الْأَصْدِقَاءِ.',
                'vocabulary' => [
                [
                    'word' => 'إِيثَارٌ',
                    'clean' => 'إيثار',
                    'meaning' => 'تَفْضِيلُ مَصْلَحَةِ الْآخَرِينَ عَلَى النَّفْسِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM2/vocab/image43.png'
                ],
                [
                    'word' => 'تَسَامُحٌ',
                    'clean' => 'تسامح',
                    'meaning' => 'الْعَفْوُ عِنْدَ الْمَقْدِرَةِ وَالصَّفْحُ',
                    'image' => '/assets/lesson-images/N3/P1/SEM2/vocab/image44.png'
                ],
                [
                    'word' => 'مَرَحٌ',
                    'clean' => 'مرح',
                    'meaning' => 'فَرَحٌ وَنَشَاطٌ فِي اللَّعِبِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM2/vocab/image45.png'
                ],
                [
                    'word' => 'رِفْقٌ',
                    'clean' => 'رفق',
                    'meaning' => 'لِينُ الْمُعَامَلَةِ وَالْبُعْدُ عَنِ الْعُنْفِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM2/vocab/image46.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: التضامن المدرسي ومساندة الزميل',
                'lesson_sentence' => 'تَعَاطَفَ الْأَطْفَالُ مَعَ صَدِيقِهِمُ الْمَرِيضِ وَزَارُوهُ فِي بَيْتِهِ.',
                'vocabulary' => [
                [
                    'word' => 'مُؤَازَرَةٌ',
                    'clean' => 'مؤازرة',
                    'meaning' => 'مُسَاعَدَةٌ وَتَقْدِيمُ الْعَوْنِ فِي الْوَقْتِ الْمُنَاسِبِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM3/vocab/image4.png'
                ],
                [
                    'word' => 'عَطْفٌ',
                    'clean' => 'عطف',
                    'meaning' => 'حَنَانٌ وَشَفَقَةٌ عَلَى الْمُحْتَاجِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM3/vocab/image48.jpeg'
                ],
                [
                    'word' => 'نَشَاطٌ',
                    'clean' => 'نشاط',
                    'meaning' => 'حَرَكَةٌ وَعَمَلٌ مُتَوَاصِلٌ',
                    'image' => '/assets/lesson-images/N3/P1/SEM3/vocab/image49.png'
                ],
                [
                    'word' => 'تَعَاطُفٌ',
                    'clean' => 'تعاطف',
                    'meaning' => 'مُشَارَكَةُ الْآخَرِينَ مَشَاعِرَهُمْ',
                    'image' => '/assets/lesson-images/N3/P1/SEM3/vocab/image5.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الحوار البناء واحترام الرأي الآخر',
                'lesson_sentence' => 'يُعَبِّرُ كُلُّ تِلْمِيذٍ عَنْ رَأْيِهِ بِكُلِّ ثِقَةٍ وَاحْتِرَامٍ.',
                'vocabulary' => [
                [
                    'word' => 'إِنْصَاتٌ',
                    'clean' => 'إنصات',
                    'meaning' => 'حُسْنُ الِاسْتِمَاعِ بِتَرْكِيزٍ وَاهْتِمَامٍ',
                    'image' => '/assets/lesson-images/N3/P1/SEM4/vocab/image46.png'
                ],
                [
                    'word' => 'تَبَادُلٌ',
                    'clean' => 'تبادل',
                    'meaning' => 'أَخْذٌ وَعَطَاءٌ فِي الْكَلَامِ وَالْأَفْكَارِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM4/vocab/image47.png'
                ],
                [
                    'word' => 'احْتِرَامٌ',
                    'clean' => 'احترام',
                    'meaning' => 'تَوْقِيرُ الرَّأْيِ الْمُخَالِفِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM4/vocab/image48.jpeg'
                ],
                [
                    'word' => 'وِئَامٌ',
                    'clean' => 'وئام',
                    'meaning' => 'تَوَافُقٌ وَسَلَامٌ دَاخِلَ الْمَجْمُوعَةِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM4/vocab/image49.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 1',
                'lesson_sentence' => 'يَحْرِصُ الْأَصْدِقَاءُ عَلَى الِاحْتِرَامِ الْمُتَبَادَلِ وَالتَّعَاوُنِ الْمُسْتَمِرِّ.',
                'vocabulary' => [
                [
                    'word' => 'صَدَاقَةٌ',
                    'clean' => 'صداقة',
                    'meaning' => 'مَحَبَّةٌ مُتَبَادَلَةٌ',
                    'image' => '/assets/lesson-images/N3/P1/SEM5/vocab/image44.png'
                ],
                [
                    'word' => 'إِيثَارٌ',
                    'clean' => 'إيثار',
                    'meaning' => 'تَفْضِيلُ الصَّدِيقِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM5/vocab/image45.png'
                ],
                [
                    'word' => 'تَعَاطُفٌ',
                    'clean' => 'تعاطف',
                    'meaning' => 'مُشَارَكَةُ الْمَشَاعِرِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM5/vocab/image44.png'
                ],
                [
                    'word' => 'إِنْصَاتٌ',
                    'clean' => 'إنصات',
                    'meaning' => 'حُسْنُ الِاسْتِمَاعِ',
                    'image' => '/assets/lesson-images/N3/P1/SEM5/vocab/image45.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N3',
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
