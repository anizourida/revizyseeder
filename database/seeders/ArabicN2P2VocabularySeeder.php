<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثانية ابتدائي (N2)
 * Unit: الوحدة 2: الحياة المدرسية والتعاون (P2)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN2P2VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: فضاء القسم وتزيينه',
                'lesson_sentence' => 'يُزَيِّنُ التَّلَامِيذُ جُدْرَانَ الْقِسْمِ بِرُسُومٍ رَائِعَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'سَبُّورَةٌ',
                    'clean' => 'سبورة',
                    'meaning' => 'لَوْحٌ لِشَرْحِ الدُّرُوسِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM1/vocab/image60.png'
                ],
                [
                    'word' => 'مَكْتَبَةٌ',
                    'clean' => 'مكتبة',
                    'meaning' => 'خِزَانَةُ الْكُتُبِ وَالْقِصَصِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM1/vocab/image71.png'
                ],
                [
                    'word' => 'زُمَلَاءُ',
                    'clean' => 'زملاء',
                    'meaning' => 'أَصْدِقَاءُ الدِّرَاسَةِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM1/vocab/image79.png'
                ],
                [
                    'word' => 'نَشَاطٌ',
                    'clean' => 'نشاط',
                    'meaning' => 'عَمَلٌ تَرْبَوِيٌّ مُفِيدٌ',
                    'image' => '/assets/lesson-images/N2/P2/SEM1/vocab/image60.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: ساحة المدرسة والاستراحة',
                'lesson_sentence' => 'يَقِفُ التَّلَامِيذُ فِي الصَّفِّ بِنِظَامٍ عِنْدَ دَقِّ الْجَرَسِ.',
                'vocabulary' => [
                [
                    'word' => 'جَرَسٌ',
                    'clean' => 'جرس',
                    'meaning' => 'أَدَاةُ إِعْلَانِ بِدَايَةِ الْحِصَّةِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM2/vocab/image56.png'
                ],
                [
                    'word' => 'صَفٌّ',
                    'clean' => 'صف',
                    'meaning' => 'طَابُورُ التَّلَامِيذِ الْمُنْتَظِمُ',
                    'image' => '/assets/lesson-images/N2/P2/SEM2/vocab/image67.png'
                ],
                [
                    'word' => 'سَاحَةٌ',
                    'clean' => 'ساحة',
                    'meaning' => 'فَضَاءُ اللَّعِبِ وَالرِّيَاضَةِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM2/vocab/image75.png'
                ],
                [
                    'word' => 'نِظَامٌ',
                    'clean' => 'نظام',
                    'meaning' => 'انْضِبَاطٌ وَاحْتِرَامٌ لِلْقَوَاعِدِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM2/vocab/image56.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: مشروع القسم ومجلة الحائط',
                'lesson_sentence' => 'تَعَاوَنَ الْفَرِيقُ فِي إِعْدَادِ مَجَلَّةِ الْحَائِطِ الْمَدْرَسِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'فَرِيقٌ',
                    'clean' => 'فريق',
                    'meaning' => 'مَجْمُوعَةُ تَلَامِيذَ يَعْمَلُونَ مَعاً',
                    'image' => '/assets/lesson-images/N2/P2/SEM3/vocab/image58.png'
                ],
                [
                    'word' => 'مَجَلَّةٌ',
                    'clean' => 'مجلة',
                    'meaning' => 'صَحِيفَةٌ حَائِطِيَّةٌ تَضُمُّ مَقَالَاتٍ وَرُسُوماً',
                    'image' => '/assets/lesson-images/N2/P2/SEM3/vocab/image59.png'
                ],
                [
                    'word' => 'تَشَاوُرٌ',
                    'clean' => 'تشاور',
                    'meaning' => 'تَبَادُلُ الرَّأْيِ قَبْلَ اتِّخَاذِ الْقَرَارِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM3/vocab/image67.png'
                ],
                [
                    'word' => 'نَجَاحٌ',
                    'clean' => 'نجاح',
                    'meaning' => 'تَحْقِيقُ الْهَدَفِ بِتَفَوُّقٍ',
                    'image' => '/assets/lesson-images/N2/P2/SEM3/vocab/image58.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: احترام المعلم وتقدير جهوده',
                'lesson_sentence' => 'يَسْتَمِعُ التَّلَامِيذُ بِاهْتِمَامٍ لِشَرْحِ الْمُعَلِّمِ فِي الْفَصْلِ.',
                'vocabulary' => [
                [
                    'word' => 'مُعَلِّمٌ',
                    'clean' => 'معلم',
                    'meaning' => 'مُرَبٍّ يَنْشُرُ الْعِلْمَ وَالْأَخْلَاقَ',
                    'image' => '/assets/lesson-images/N2/P2/SEM4/vocab/image57.png'
                ],
                [
                    'word' => 'اسْتِمَاعٌ',
                    'clean' => 'استماع',
                    'meaning' => 'إِنْصَاتٌ بِانْتِبَاهٍ وَتَرْكِيزٍ',
                    'image' => '/assets/lesson-images/N2/P2/SEM4/vocab/image58.png'
                ],
                [
                    'word' => 'شَرْحٌ',
                    'clean' => 'شرح',
                    'meaning' => 'تَبْسِيطُ الْمَعْلُومَاتِ لِلْفَهْمِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM4/vocab/image59.png'
                ],
                [
                    'word' => 'احْتِرَامٌ',
                    'clean' => 'احترام',
                    'meaning' => 'تَقْدِيرٌ وَتَوْقِيرٌ',
                    'image' => '/assets/lesson-images/N2/P2/SEM4/vocab/image57.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 2',
                'lesson_sentence' => 'الِاجْتِهَادُ وَالتَّعَاوُنُ يُحَقِّقَانِ النَّجَاحَ لِلْجَمِيعِ.',
                'vocabulary' => [
                [
                    'word' => 'سَبُّورَةٌ',
                    'clean' => 'سبورة',
                    'meaning' => 'أَدَاةُ الشَّرْحِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM5/vocab/image11.png'
                ],
                [
                    'word' => 'فَرِيقٌ',
                    'clean' => 'فريق',
                    'meaning' => 'مَجْمُوعَةُ عَمَلٍ',
                    'image' => '/assets/lesson-images/N2/P2/SEM5/vocab/image50.png'
                ],
                [
                    'word' => 'مُعَلِّمٌ',
                    'clean' => 'معلم',
                    'meaning' => 'مُرَبِّي الْأَجْيَالِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'نَجَاحٌ',
                    'clean' => 'نجاح',
                    'meaning' => 'ثَمَرَةُ الِاجْتِهَادِ',
                    'image' => '/assets/lesson-images/N2/P2/SEM5/vocab/image11.png'
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
