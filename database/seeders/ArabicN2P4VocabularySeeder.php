<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثانية ابتدائي (N2)
 * Unit: الوحدة 4: القرية والمدينة والمهن والحرف (P4)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN2P4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: النجار والخياط وإبداع اليدين',
                'lesson_sentence' => 'يَصْنَعُ النَّجَّارُ كَرَاسِيَّ خَشَبِيَّةً جَمِيلَةً بِإِتْقَانٍ.',
                'vocabulary' => [
                [
                    'word' => 'نَجَّارٌ',
                    'clean' => 'نجار',
                    'meaning' => 'مَنْ يَصْنَعُ الْأَثَاثَ مِنَ الْخَشَبِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM1/vocab/image11.png'
                ],
                [
                    'word' => 'خَيَّاطٌ',
                    'clean' => 'خياط',
                    'meaning' => 'مَنْ يَخِيطُ الثِّيَابَ وَيُفَصِّلُهَا',
                    'image' => '/assets/lesson-images/N2/P4/SEM1/vocab/image16.jpg'
                ],
                [
                    'word' => 'مِنْشَارٌ',
                    'clean' => 'منشار',
                    'meaning' => 'أَدَاةُ قَطْعِ الْخَشَبِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM1/vocab/image19.png'
                ],
                [
                    'word' => 'إِبْرَةٌ',
                    'clean' => 'إبرة',
                    'meaning' => 'أَدَاةُ خِيَاطَةِ الْقُمَاشِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM1/vocab/image11.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الفلاح والحقل وموسم الحصاد',
                'lesson_sentence' => 'يَحْرُثُ الْفَلَّاحُ الْأَرْضَ فِي الصَّبَاحِ الْبَاكِرِ.',
                'vocabulary' => [
                [
                    'word' => 'فَلَّاحٌ',
                    'clean' => 'فلاح',
                    'meaning' => 'مَنْ يَزْرَعُ الْأَرْضَ وَيَرْعَى النَّبَاتَ',
                    'image' => '/assets/lesson-images/N2/P4/SEM2/vocab/image19.png'
                ],
                [
                    'word' => 'مِحْرَاثٌ',
                    'clean' => 'محراث',
                    'meaning' => 'أَدَاةُ شَقِّ الْأَرْضِ لِلزِّرَاعَةِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM2/vocab/image25.png'
                ],
                [
                    'word' => 'حَصَادٌ',
                    'clean' => 'حصاد',
                    'meaning' => 'جَمْعُ مَحْصُولِ الْقَمْحِ وَالشَّعِيرِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM2/vocab/image26.png'
                ],
                [
                    'word' => 'سُنْبُلَةٌ',
                    'clean' => 'سنبلة',
                    'meaning' => 'جُزْءُ النَّبَاتِ الَّذِي يَحْمِلُ الْحَبَّ',
                    'image' => '/assets/lesson-images/N2/P4/SEM2/vocab/image19.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: البناء وعمال المعمار',
                'lesson_sentence' => 'يَبْنِي الْعُمَّالُ مَنَازِلَ قَوِيَّةً وَمُتَنَاسِقَةَ الشَّكْلِ.',
                'vocabulary' => [
                [
                    'word' => 'بَنَّاءٌ',
                    'clean' => 'بناء',
                    'meaning' => 'مَنْ يَبْنِي الْجُدْرَانَ وَالْمَنَازِلَ',
                    'image' => '/assets/lesson-images/N2/P4/SEM3/vocab/image11.png'
                ],
                [
                    'word' => 'قِرْمِيدٌ',
                    'clean' => 'قرميد',
                    'meaning' => 'طِينٌ مَحْرُوقٌ لِتَغْطِيَةِ السُّطُوحِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM3/vocab/image16.jpg'
                ],
                [
                    'word' => 'إِسْمَنْتٌ',
                    'clean' => 'إسمنت',
                    'meaning' => 'مَادَّةٌ تُمَاسِكُ الْآجُرَّ وَالْحِجَارَةَ',
                    'image' => '/assets/lesson-images/N2/P4/SEM3/vocab/image19.png'
                ],
                [
                    'word' => 'جِدَارٌ',
                    'clean' => 'جدار',
                    'meaning' => 'حَائِطٌ قَوِيٌّ لِلْبِنَايَةِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM3/vocab/image11.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: شرطي المرور وسلامة المواطنين',
                'lesson_sentence' => 'يُنَظِّمُ شُرْطِيُّ الْمُرُورِ حَرَكَةَ السَّيَّارَاتِ بِنَشَاطٍ.',
                'vocabulary' => [
                [
                    'word' => 'شُرْطِيٌّ',
                    'clean' => 'شرطي',
                    'meaning' => 'حَامِي النِّظَامِ وَالْأَمْنِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM4/vocab/image25.png'
                ],
                [
                    'word' => 'إِشَارَةٌ',
                    'clean' => 'إشارة',
                    'meaning' => 'عَلَامَةٌ ضَوْئِيَّةٌ لِتَنْظِيمِ السَّيْرِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM4/vocab/image26.png'
                ],
                [
                    'word' => 'سَلَامَةٌ',
                    'clean' => 'سلامة',
                    'meaning' => 'أَمَانٌ وَوِقَايَةٌ مِنَ الْأَخْطَارِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM4/vocab/image27.png'
                ],
                [
                    'word' => 'طَرِيقٌ',
                    'clean' => 'طريق',
                    'meaning' => 'مَسْلَكُ سَيْرِ السَّيَّارَاتِ وَالرَّاجِلِينَ',
                    'image' => '/assets/lesson-images/N2/P4/SEM4/vocab/image25.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 4',
                'lesson_sentence' => 'يُخْلِصُ كُلُّ حِرْفِيٍّ فِي عَمَلِهِ لِيَزْدَهِرَ الْوَطَنُ.',
                'vocabulary' => [
                [
                    'word' => 'نَجَّارٌ',
                    'clean' => 'نجار',
                    'meaning' => 'صَانِعُ الْخَشَبِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM5/vocab/image11.png'
                ],
                [
                    'word' => 'فَلَّاحٌ',
                    'clean' => 'فلاح',
                    'meaning' => 'زَارِعُ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM5/vocab/image53.png'
                ],
                [
                    'word' => 'بَنَّاءٌ',
                    'clean' => 'بناء',
                    'meaning' => 'مُشَيِّدُ الدُّورِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM5/vocab/image65.png'
                ],
                [
                    'word' => 'شُرْطِيٌّ',
                    'clean' => 'شرطي',
                    'meaning' => 'حَافِظُ السَّلَامَةِ',
                    'image' => '/assets/lesson-images/N2/P4/SEM5/vocab/image11.png'
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
