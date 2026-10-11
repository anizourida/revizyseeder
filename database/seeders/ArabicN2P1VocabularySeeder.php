<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثانية ابتدائي (N2)
 * Unit: الوحدة 1: العائلة والقرية والمدينة (P1)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN2P1VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الأقارب والبيت العائلي',
                'lesson_sentence' => 'تَجْتَمِعُ الْأُسْرَةُ حَوْلَ مَائِدَةِ الْغَدَاءِ فِي بَهْجَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'أُسْرَةٌ',
                    'clean' => 'أسرة',
                    'meaning' => 'الْأَبُ وَالْأُمُّ وَالْأَبْنَاءُ',
                    'image' => '/assets/lesson-images/N2/P1/SEM1/vocab/image108.png'
                ],
                [
                    'word' => 'جَدٌّ',
                    'clean' => 'جد',
                    'meaning' => 'وَالِدُ الْأَبِ أَوِ الْأُمِّ',
                    'image' => '/assets/lesson-images/N2/P1/SEM1/vocab/image123.png'
                ],
                [
                    'word' => 'حَدِيقَةٌ',
                    'clean' => 'حديقة',
                    'meaning' => 'مَسَاحَةٌ خَضْرَاءُ حَوْلَ الْمَنْزِلِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM1/vocab/image124.png'
                ],
                [
                    'word' => 'عِمَارَةٌ',
                    'clean' => 'عمارة',
                    'meaning' => 'مَبْنًى سَكَنِيٌّ عَالٍ',
                    'image' => '/assets/lesson-images/N2/P1/SEM1/vocab/image125.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الحي والشارع ومرافقه',
                'lesson_sentence' => 'يَسِيرُ الرَّاجِلُونَ بِانْتِظَامٍ عَلَى الرَّصِيفِ.',
                'vocabulary' => [
                [
                    'word' => 'شَارِعٌ',
                    'clean' => 'شارع',
                    'meaning' => 'طَرِيقٌ وَاسِعٌ فِي الْمَدِينَةِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM2/vocab/image22.jpg'
                ],
                [
                    'word' => 'رَصِيفٌ',
                    'clean' => 'رصيف',
                    'meaning' => 'مَمْشَى الْمُشَاةِ بِجَانِبِ الطَّرِيقِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM2/vocab/image23.png'
                ],
                [
                    'word' => 'مَسْجِدٌ',
                    'clean' => 'مسجد',
                    'meaning' => 'مَكَانُ إِقَامَةِ الصَّلَوَاتِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM2/vocab/image24.png'
                ],
                [
                    'word' => 'سَاحَةٌ',
                    'clean' => 'ساحة',
                    'meaning' => 'فَضَاءٌ فَسِيحٌ لِلْعِبِ وَاللِّقَاءِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM2/vocab/image25.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: مرافق القرية والحقول',
                'lesson_sentence' => 'يَسْقِي الْفَلَّاحُ الزَّرْعَ مِنْ مَاءِ النَّبْعِ الصَّافِي.',
                'vocabulary' => [
                [
                    'word' => 'حَقْلٌ',
                    'clean' => 'حقل',
                    'meaning' => 'أَرْضٌ مَزْرُوعَةٌ بِالْحُبُوبِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM3/vocab/image68.jpg'
                ],
                [
                    'word' => 'نَبْعٌ',
                    'clean' => 'نبع',
                    'meaning' => 'عَيْنُ مَاءٍ تَنْبُعُ مِنَ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM3/vocab/image69.jpg'
                ],
                [
                    'word' => 'حَظِيرَةٌ',
                    'clean' => 'حظيرة',
                    'meaning' => 'مَأْوَى الْمَوَاشِي وَالْأَبْقَارِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM3/vocab/image70.jpg'
                ],
                [
                    'word' => 'سَاقِيَةٌ',
                    'clean' => 'ساقية',
                    'meaning' => 'مَجْرَى مَاءٍ صَغِيرٌ لِسَقْيِ الزَّرْعِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM3/vocab/image78.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: التكافل الأسري والتعاون',
                'lesson_sentence' => 'يُسَاعِدُ الْوَلَدُ أُمَّهُ فِي تَرْتِيبِ الْبَيْتِ بَهْجَةً وَحُبّاً.',
                'vocabulary' => [
                [
                    'word' => 'تَعَاوُنٌ',
                    'clean' => 'تعاون',
                    'meaning' => 'الْمُسَاعَدَةُ وَالْعَمَلُ الْمُشْتَرَكُ',
                    'image' => '/assets/lesson-images/N2/P1/SEM4/vocab/image48.png'
                ],
                [
                    'word' => 'مَوَدَّةٌ',
                    'clean' => 'مودة',
                    'meaning' => 'الْمَحَبَّةُ الْخَالِصَةُ بَيْنَ النَّاسِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM4/vocab/image56.png'
                ],
                [
                    'word' => 'مُسَاعَدَةٌ',
                    'clean' => 'مساعدة',
                    'meaning' => 'مَدُّ يَدِ الْعَوْنِ لِلْآخَرِينَ',
                    'image' => '/assets/lesson-images/N2/P1/SEM4/vocab/image65.png'
                ],
                [
                    'word' => 'بَهْجَةٌ',
                    'clean' => 'بهجة',
                    'meaning' => 'فَرَحٌ وَسُرُورٌ كَبِيرٌ',
                    'image' => '/assets/lesson-images/N2/P1/SEM4/vocab/image72.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 1',
                'lesson_sentence' => 'تَتَعَاوَنُ الْأُسْرَةُ وَالْجِيرَانُ لِتَجْمِيلِ الْحَيِّ.',
                'vocabulary' => [
                [
                    'word' => 'أُسْرَةٌ',
                    'clean' => 'أسرة',
                    'meaning' => 'أَهْلُ الْبَيْتِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM5/vocab/image11.png'
                ],
                [
                    'word' => 'شَارِعٌ',
                    'clean' => 'شارع',
                    'meaning' => 'طَرِيقُ الْمَدِينَةِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM5/vocab/image56.png'
                ],
                [
                    'word' => 'حَقْلٌ',
                    'clean' => 'حقل',
                    'meaning' => 'مَزْرَعَةُ الْخَيْرِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM5/vocab/image64.png'
                ],
                [
                    'word' => 'تَعَاوُنٌ',
                    'clean' => 'تعاون',
                    'meaning' => 'تَآزُرُ الْجَمِيعِ',
                    'image' => '/assets/lesson-images/N2/P1/SEM5/vocab/image11.png'
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
