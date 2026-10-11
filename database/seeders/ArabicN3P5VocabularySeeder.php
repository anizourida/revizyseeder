<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثالثة ابتدائي (N3)
 * Unit: الوحدة 5: الماء والحياة والبيئة المستدامة (P5)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN3P5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: قطرة ماء ثمينة وترشيد الاستهلاك',
                'lesson_sentence' => 'الْمَاءُ نِعْمَةٌ كُبْرَى يَجِبُ تَرْشِيدُ اسْتِعْمَالِهَا حِمَايَةً لِلْحَيَاةِ.',
                'vocabulary' => [
                [
                    'word' => 'يَنْبُوعٌ',
                    'clean' => 'ينبوع',
                    'meaning' => 'عَيْنُ مَاءٍ طَبِيعِيَّةٌ تَتَفَجَّرُ مِنَ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM1/vocab/image56.png'
                ],
                [
                    'word' => 'سَدٌّ',
                    'clean' => 'سد',
                    'meaning' => 'بِنَاءٌ عَظِيمٌ لِحَجْزِ مِيَاهِ الْأَمْطَارِ وَتَخْزِينِهَا',
                    'image' => '/assets/lesson-images/N3/P5/SEM1/vocab/image58.png'
                ],
                [
                    'word' => 'تَرْشِيدٌ',
                    'clean' => 'ترشيد',
                    'meaning' => 'حُسْنُ اسْتِعْمَالِ الْمَاءِ دُونَ إِسْرَافٍ',
                    'image' => '/assets/lesson-images/N3/P5/SEM1/vocab/image59.png'
                ],
                [
                    'word' => 'تَبَخُّرٌ',
                    'clean' => 'تبخر',
                    'meaning' => 'تَحَوُّلُ الْمَاءِ إِلَى بُخَارٍ بِفِعْلِ الْحَرَارَةِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM1/vocab/image56.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: دورة الماء في الطبيعة وتكون الغيوم',
                'lesson_sentence' => 'تَتَجَمَّعُ الْغُيُومُ فِي السَّمَاءِ ثُمَّ تَهْطِلُ أَمْطَاراً غَزِيرَةً تُحْيِي الْأَرْضَ.',
                'vocabulary' => [
                [
                    'word' => 'سَحَابٌ',
                    'clean' => 'سحاب',
                    'meaning' => 'غُيُومٌ مُتَرَاكِمَةٌ فِي السَّمَاءِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM2/vocab/image42.png'
                ],
                [
                    'word' => 'هُطُولٌ',
                    'clean' => 'هطول',
                    'meaning' => 'نُزُولُ الْمَطَرِ بِغَزَارَةٍ',
                    'image' => '/assets/lesson-images/N3/P5/SEM2/vocab/image43.png'
                ],
                [
                    'word' => 'أَمْطَارٌ',
                    'clean' => 'أمطار',
                    'meaning' => 'مِيَاهٌ عَذْبَةٌ تَنْزِلُ مِنَ السَّمَاءِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM2/vocab/image44.png'
                ],
                [
                    'word' => 'ثَرْوَةٌ',
                    'clean' => 'ثروة',
                    'meaning' => 'خَيْرٌ كَثِيرٌ يَجِبُ صَوْنُهُ',
                    'image' => '/assets/lesson-images/N3/P5/SEM2/vocab/image42.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حماية الأنهار والبحيرات من التلوث',
                'lesson_sentence' => 'حَافَظَ الْأَطْفَالُ عَلَى نَظَافَةِ الشَّاطِئِ وَالنَّهْرِ مِنْ كُلِّ الْمُخَلَّفَاتِ.',
                'vocabulary' => [
                [
                    'word' => 'تَلَوُّثٌ',
                    'clean' => 'تلوث',
                    'meaning' => 'تَعَكُّرُ الْمَاءِ بِالنِّفَايَاتِ وَالْمَوَادِّ الضَّارَّةِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM3/vocab/image56.png'
                ],
                [
                    'word' => 'نَقَاءٌ',
                    'clean' => 'نقاء',
                    'meaning' => 'صَفَاءُ الْمَاءِ وَخُلُوُّهُ مِنَ الشَّوَائِبِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM3/vocab/image58.png'
                ],
                [
                    'word' => 'بِيئَةٌ',
                    'clean' => 'بيئة',
                    'meaning' => 'الْمُحِيطُ الطَّبِيعِيُّ الَّذِي نَعِيشُ فِيهِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM3/vocab/image59.png'
                ],
                [
                    'word' => 'حِمَايَةٌ',
                    'clean' => 'حماية',
                    'meaning' => 'صَوْنُ الطَّبِيعَةِ وَالدِّفَاعُ عَنْ نَقَائِهَا',
                    'image' => '/assets/lesson-images/N3/P5/SEM3/vocab/image56.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الواحة المغربية وأشجار النخيل الباسقة',
                'lesson_sentence' => 'تَنْتَشِرُ أَشْجَارُ النَّخِيلِ الْبَاسِقَةِ حَوْلَ عَيْنِ الْمَاءِ فِي الْوَاحَةِ.',
                'vocabulary' => [
                [
                    'word' => 'نَخِيلٌ',
                    'clean' => 'نخيل',
                    'meaning' => 'شَجَرٌ طَوِيلٌ يُثْمِرُ التَّمْرَ اللَّذِيذَ',
                    'image' => '/assets/lesson-images/N3/P5/SEM4/vocab/image37.png'
                ],
                [
                    'word' => 'وَاحَةٌ',
                    'clean' => 'واحة',
                    'meaning' => 'أَرْضٌ خَضْرَاءُ خَصِيبَةٌ وَسَطَ الصَّحْرَاءِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM4/vocab/image42.png'
                ],
                [
                    'word' => 'جَدْوَلٌ',
                    'clean' => 'جدول',
                    'meaning' => 'مَجْرَى مَاءٍ رَقْرَاقٍ يَسْقِي الزَّرْعَ',
                    'image' => '/assets/lesson-images/N3/P5/SEM4/vocab/image43.png'
                ],
                [
                    'word' => 'خُصُوبَةٌ',
                    'clean' => 'خصوبة',
                    'meaning' => 'قُدْرَةُ الْأَرْضِ عَلَى الْإِنْبَاتِ وَالْعَطَاءِ',
                    'image' => '/assets/lesson-images/N3/P5/SEM4/vocab/image37.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم نهاية السنة الثالثة ابتدائي',
                'lesson_sentence' => 'صَوْنُ الْمِيَاهِ وَاجِبٌ وَطَنِيٌّ وَإِنْسَانِيٌّ يَحْفَظُ حَيَاةَ الْأَجْيَالِ.',
                'vocabulary' => [
                [
                    'word' => 'يَنْبُوعٌ',
                    'clean' => 'ينبوع',
                    'meaning' => 'مَنْبَعُ الْمَاءِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'سَدٌّ',
                    'clean' => 'سد',
                    'meaning' => 'حَاجِزُ الْمِيَاهِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image76.jpg'
                ],
                [
                    'word' => 'تَلَوُّثٌ',
                    'clean' => 'تلوث',
                    'meaning' => 'عَطَبُ الْبِيئَةِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'وَاحَةٌ',
                    'clean' => 'واحة',
                    'meaning' => 'جَنَّةُ الصَّحْرَاءِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image76.jpg'
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
