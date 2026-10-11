<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الرابعة ابتدائي (N4)
 * Unit: الوحدة 3: الهوايات والأنشطة الإبداعية (P3)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN4P3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: المطالعة الحرة وبناء الشخصية',
                'lesson_sentence' => 'تُنَمِّي الْمُطَالَعَةُ الْحُرَّةُ الْفِكْرَ وَتُغَذِّي الْخَيَالَ بِأَفْكَارٍ نَيِّرَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'مُطَالَعَةٌ',
                    'clean' => 'مطالعة',
                    'meaning' => 'قِرَاءَةُ الْكُتُبِ لِلتَّثْقِيفِ وَكَسْبِ الْعِلْمِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM1/vocab/image67.png'
                ],
                [
                    'word' => 'مَوْهِبَةٌ',
                    'clean' => 'موهبة',
                    'meaning' => 'قُدْرَةٌ فِطْرِيَّةٌ يَجِبُ صَقْلُهَا بِالتَّدْرِيبِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM1/vocab/image68.png'
                ],
                [
                    'word' => 'إِلْهَامٌ',
                    'clean' => 'إلهام',
                    'meaning' => 'فِكْرَةٌ إِبْدَاعِيَّةٌ تَهْبِطُ عَلَى الْعَقْلِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM1/vocab/image69.png'
                ],
                [
                    'word' => 'ثَقَافَةٌ',
                    'clean' => 'ثقافة',
                    'meaning' => 'مَعَارِفُ شَامِلَةٌ فِي شَتَّى الْعُلُومِ وَالْفُنُونِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM1/vocab/image67.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: فن المسرح والتعبير الجسدي',
                'lesson_sentence' => 'أَبْدَعَ التَّلَامِيذُ فِي أَدَاءِ مَسْرَحِيَّةٍ رَائِعَةٍ عَنِ الصَّدَاقَةِ.',
                'vocabulary' => [
                [
                    'word' => 'خَشَبَةٌ',
                    'clean' => 'خشبة',
                    'meaning' => 'مِنَصَّةُ أَدَاءِ الْمَسْرَحِيَّةِ أَمَامَ الْجُمْهُورِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM2/vocab/image23.png'
                ],
                [
                    'word' => 'سِتَارٌ',
                    'clean' => 'ستار',
                    'meaning' => 'قُمَاشٌ يَفْصِلُ الْخَشَبَةَ عَنِ الْمُشَاهِدِينَ',
                    'image' => '/assets/lesson-images/N4/P3/SEM2/vocab/image49.jpg'
                ],
                [
                    'word' => 'حِوَارٌ',
                    'clean' => 'حوار',
                    'meaning' => 'كَلَامٌ مُتَبَادَلٌ بَيْنَ أَبْطَالِ الْمَسْرَحِيَّةِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM2/vocab/image63.png'
                ],
                [
                    'word' => 'أَدَاءٌ',
                    'clean' => 'أداء',
                    'meaning' => 'تَمْثِيلُ الدَّوْرِ بِإِتْقَانٍ وَصِدْقٍ',
                    'image' => '/assets/lesson-images/N4/P3/SEM2/vocab/image23.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الرسم والتشكيل وسحر الألوان',
                'lesson_sentence' => 'رَسَمَتْ نَدَى لَوْحَةً مُبْهِرَةً تَفِيضُ بِالْأَلْوَانِ الْحَيَّةِ وَالتَّنَاغُمِ.',
                'vocabulary' => [
                [
                    'word' => 'رِيشَةٌ',
                    'clean' => 'ريشة',
                    'meaning' => 'أَدَاةُ وَضْعِ الصِّبَاغَةِ عَلَى اللَّوْحَةِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM3/vocab/image47.png'
                ],
                [
                    'word' => 'مِعْرَضٌ',
                    'clean' => 'معرض',
                    'meaning' => 'فَضَاءُ عَرْضِ اللَّوْحَاتِ الْفَنِّيَّةِ لِلْجُمْهُورِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM3/vocab/image48.png'
                ],
                [
                    'word' => 'تَنَاغُمٌ',
                    'clean' => 'تناغم',
                    'meaning' => 'انْسِجَامٌ بَدِيعٌ بَيْنَ الْأَلْوَانِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM3/vocab/image66.png'
                ],
                [
                    'word' => 'لَوْحَةٌ',
                    'clean' => 'لوحة',
                    'meaning' => 'عَمَلٌ فَنِّيٌّ تَشْكِيلِيٌّ',
                    'image' => '/assets/lesson-images/N4/P3/SEM3/vocab/image47.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الشطرنج والرياضات الذهنية',
                'lesson_sentence' => 'تَتَطَلَّبُ لُعْبَةُ الشِّطْرَنَجِ هُدُوءاً وَتَرْكِيزاً ذِهْنِيّاً عَالِياً وَتَخْطِيطاً.',
                'vocabulary' => [
                [
                    'word' => 'تَرْكِيزٌ',
                    'clean' => 'تركيز',
                    'meaning' => 'تَوْجِيهُ كُلِّ الِانْتِبَاهِ نَحْوَ الْهَدَفِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM4/vocab/image58.png'
                ],
                [
                    'word' => 'مُنَافَسَةٌ',
                    'clean' => 'منافسة',
                    'meaning' => 'مُبَارَاةٌ شَرِيفَةٌ لِإِظْهَارِ الْمَهَارَةِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM4/vocab/image78.png'
                ],
                [
                    'word' => 'ذَكَاءٌ',
                    'clean' => 'ذكاء',
                    'meaning' => 'سُرْعَةُ الْفَهْمِ وَحُسْنُ التَّصَرُّفِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM4/vocab/image79.png'
                ],
                [
                    'word' => 'خُطَّةٌ',
                    'clean' => 'خطة',
                    'meaning' => 'طَرِيقَةٌ مَدْرُوسَةٌ لِلْوُصُولِ إِلَى النَّصْرِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM4/vocab/image58.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 3',
                'lesson_sentence' => 'تُسَاعِدُ الْهِوَايَاتُ الْإِبْدَاعِيَّةُ عَلَى صَقْلِ الشَّخْصِيَّةِ وَالنَّجَاحِ فِي الدِّرَاسَةِ.',
                'vocabulary' => [
                [
                    'word' => 'مُطَالَعَةٌ',
                    'clean' => 'مطالعة',
                    'meaning' => 'غِذَاءُ الْعَقْلِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM5/vocab/image66.png'
                ],
                [
                    'word' => 'خَشَبَةٌ',
                    'clean' => 'خشبة',
                    'meaning' => 'مَسْرَحُ الْفَنِّ',
                    'image' => '/assets/lesson-images/N4/P3/SEM5/vocab/image70.png'
                ],
                [
                    'word' => 'تَنَاغُمٌ',
                    'clean' => 'تناغم',
                    'meaning' => 'انْسِجَامُ الْأَلْوَانِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM5/vocab/image71.png'
                ],
                [
                    'word' => 'تَرْكِيزٌ',
                    'clean' => 'تركيز',
                    'meaning' => 'صَفَاءُ الذِّهْنِ',
                    'image' => '/assets/lesson-images/N4/P3/SEM5/vocab/image66.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N4',
                        'period' => 'P3',
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
