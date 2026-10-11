<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الرابعة ابتدائي (N4)
 * Unit: الوحدة 4: الفلاحة والصناعة والتجارة (P4)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN4P4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الفلاحة العصرية وتطوير الإنتاج',
                'lesson_sentence' => 'تُسْتَخْدَمُ التَّقْنِيَّاتُ الْحَدِيثَةُ لِتَحْسِينِ مَحَاصِيلِ الْأَرَاضِي الْفِلَاحِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'مَحْصُولٌ',
                    'clean' => 'محصول',
                    'meaning' => 'كَمِّيَّةُ الثِّمَارِ أَوِ الْحُبُوبِ الْمَجْنِيَّةِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM1/vocab/image76.png'
                ],
                [
                    'word' => 'رَيٌّ',
                    'clean' => 'ري',
                    'meaning' => 'سَقْيُ الْمَزْرُوعَاتِ بِطُرُقٍ حَدِيثَةٍ كَالْتَّقْطِيرِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM1/vocab/image77.png'
                ],
                [
                    'word' => 'حَصَّادَةٌ',
                    'clean' => 'حصادة',
                    'meaning' => 'آلَةٌ زِرَاعِيَّةٌ كَبِيرَةٌ لِقَطْعِ الْحُبُوبِ وَدَرْسِهَا',
                    'image' => '/assets/lesson-images/N4/P4/SEM1/vocab/image78.png'
                ],
                [
                    'word' => 'خُصُوبَةٌ',
                    'clean' => 'خصوبة',
                    'meaning' => 'جَوْدَةُ التُّرْبَةِ وَقُدْرَتُهَا عَلَى الْإِنْتَاجِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM1/vocab/image76.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الصناعة الوطنية وتطور المصانع',
                'lesson_sentence' => 'تَشْهَدُ الصِّنَاعَةُ الْمَغْرِبِيَّةُ تَطَوُّراً كَبِيراً فِي كُلِّ الْمَجَالَاتِ.',
                'vocabulary' => [
                [
                    'word' => 'مَصْنَعٌ',
                    'clean' => 'مصنع',
                    'meaning' => 'مَبْنًى كَبِيرٌ يَضُمُّ آلَاتٍ لِإِنْتَاجِ السِّلَعِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM2/vocab/image46.png'
                ],
                [
                    'word' => 'آلَاتٌ',
                    'clean' => 'آلات',
                    'meaning' => 'أَجْهِزَةٌ مِيكَانِيكِيَّةٌ لِتَسْهِيلِ الْعَمَلِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM2/vocab/image47.png'
                ],
                [
                    'word' => 'تَصْنِيعٌ',
                    'clean' => 'تصنيع',
                    'meaning' => 'تَحْوِيلُ الْمَوَادِّ الْخَامِ إِلَى مُنْتَجَاتٍ جَاهِزَةٍ',
                    'image' => '/assets/lesson-images/N4/P4/SEM2/vocab/image65.png'
                ],
                [
                    'word' => 'جَوْدَةٌ',
                    'clean' => 'جودة',
                    'meaning' => 'إِتْقَانٌ وَمُطَابَقَةٌ لِأَعْلَى الْمَعَايِيرِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM2/vocab/image46.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: التجارة والتصدير نحو الأسواق الدولية',
                'lesson_sentence' => 'يُصَدِّرُ الْمَغْرِبُ مُنْتَجَاتِهِ الزِّرَاعِيَّةَ وَالصِّنَاعِيَّةَ إِلَى الْعَالَمِ.',
                'vocabulary' => [
                [
                    'word' => 'تَصْدِيرٌ',
                    'clean' => 'تصدير',
                    'meaning' => 'بَيْعُ الْمُنْتَجَاتِ الْوَطَنِيَّةِ لِلدُّوَلِ الْأُخْرَى',
                    'image' => '/assets/lesson-images/N4/P4/SEM3/vocab/image39.jpg'
                ],
                [
                    'word' => 'سُوقٌ',
                    'clean' => 'سوق',
                    'meaning' => 'فَضَاءُ تَبَادُلِ السِّلَعِ وَالْبَضَائِعِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM3/vocab/image76.png'
                ],
                [
                    'word' => 'تَبَادُلٌ',
                    'clean' => 'تبادل',
                    'meaning' => 'حَرَكَةُ الْبَيْعِ وَالشِّرَاءِ بَيْنَ الشُّعُوبِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM3/vocab/image77.png'
                ],
                [
                    'word' => 'بَضَائِعُ',
                    'clean' => 'بضائع',
                    'meaning' => 'مَوَادُّ تِجَارِيَّةٌ مُعَدَّةٌ لِلِاسْتِهْلَاكِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM3/vocab/image39.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الخدمات والمهن الحرة وتنمية المجتمع',
                'lesson_sentence' => 'تُسَاهِمُ الْمِهَنُ الْحُرَّةُ فِي إِنْعَاشِ الِاقْتِصَادِ وَخِدْمَةِ الْمُوَاطِنِينَ.',
                'vocabulary' => [
                [
                    'word' => 'خَدَمَاتٌ',
                    'clean' => 'خدمات',
                    'meaning' => 'أَعْمَالٌ غَيْرُ مَادِّيَّةٍ تُقَدَّمُ لِلنَّاسِ كَالصِّحَّةِ وَالنَّقْلِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM4/vocab/image37.png'
                ],
                [
                    'word' => 'كِفَاءَةٌ',
                    'clean' => 'كفاءة',
                    'meaning' => 'قُدْرَةٌ عَالِيَةٌ عَلَى إِنْجَازِ الْمَهَامِّ بِنَجَاحٍ',
                    'image' => '/assets/lesson-images/N4/P4/SEM4/vocab/image38.png'
                ],
                [
                    'word' => 'مِهْنِيٌّ',
                    'clean' => 'مهني',
                    'meaning' => 'مَنْ يُمَارِسُ حِرْفَةً أَوْ مِهْنَةً بِاحْتِرَافٍ',
                    'image' => '/assets/lesson-images/N4/P4/SEM4/vocab/image39.png'
                ],
                [
                    'word' => 'تَطْوِيرٌ',
                    'clean' => 'تطوير',
                    'meaning' => 'تَحْسِينُ الْأَدَاءِ وَالِارْتِقَاءُ بِالْعَمَلِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM4/vocab/image37.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 4',
                'lesson_sentence' => 'يَتَكَامَلُ الْإِنْتَاجُ الْفِلَاحِيُّ وَالصِّنَاعِيُّ لِتَحْقِيقِ الِاكْتِفَاءِ الذَّاتِيِّ.',
                'vocabulary' => [
                [
                    'word' => 'مَحْصُولٌ',
                    'clean' => 'محصول',
                    'meaning' => 'خَيْرَاتُ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM5/vocab/image74.png'
                ],
                [
                    'word' => 'مَصْنَعٌ',
                    'clean' => 'مصنع',
                    'meaning' => 'قَلْعَةُ الْإِنْتَاجِ',
                    'image' => '/assets/lesson-images/N4/P4/SEM5/vocab/image75.png'
                ],
                [
                    'word' => 'تَصْدِيرٌ',
                    'clean' => 'تصدير',
                    'meaning' => 'تِجَارَةٌ دَوْلِيَّةٌ',
                    'image' => '/assets/lesson-images/N4/P4/SEM5/vocab/image76.png'
                ],
                [
                    'word' => 'كِفَاءَةٌ',
                    'clean' => 'كفاءة',
                    'meaning' => 'خِبْرَةٌ وَإِتْقَانٌ',
                    'image' => '/assets/lesson-images/N4/P4/SEM5/vocab/image74.png'
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
