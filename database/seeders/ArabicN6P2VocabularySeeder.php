<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة السادسة ابتدائي (N6)
 * Unit: الوحدة 2: المواطنة والديمقراطية والسلوك المدني (P2)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN6P2VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: المشاركة المدنية والانتخابات الديمقراطية',
                'lesson_sentence' => 'يُعَبِّرُ الْمُوَاطِنُونَ كُلُّهُمْ عَنْ إِرَادَتِهِمْ الْحُرَّةِ بِاخْتِيَارِ مُمَثِّلِيهِمْ بِنَزَاهَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'انْتِخَابَاتٌ',
                    'clean' => 'انتخابات',
                    'meaning' => 'عَمَلِيَّةٌ دِيمُقْرَاطِيَّةٌ لِاخْتِيَارِ مُمَثِّلِي الشَّعْبِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM1/vocab/image56.png'
                ],
                [
                    'word' => 'صُنْدُوقٌ',
                    'clean' => 'صندوق',
                    'meaning' => 'وِعَاءُ وَضْعِ أَصْوَاتِ النَّاخِبِينَ',
                    'image' => '/assets/lesson-images/N6/P2/SEM1/vocab/image57.png'
                ],
                [
                    'word' => 'نَزَاهَةٌ',
                    'clean' => 'نزاهة',
                    'meaning' => 'شَفَافِيَّةٌ وَعَدَالَةٌ تَامَّةٌ فِي فَرْزِ النَّتَائِجِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM1/vocab/image58.png'
                ],
                [
                    'word' => 'شَفَافِيَّةٌ',
                    'clean' => 'شفافية',
                    'meaning' => 'وُضُوحٌ كَامِلٌ لَا شَكَّ فِيهِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM1/vocab/image56.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: دولة الحق والقانون وفصل السلط',
                'lesson_sentence' => 'تَضْمَنُ دَوْلَةُ الْحَقِّ نَفْسُهَا حِمَايَةَ الْحُرِّيَّاتِ الْأَسَاسِيَّةِ لِكُلِّ الْمُوَاطِنِينَ.',
                'vocabulary' => [
                [
                    'word' => 'عَدَالَةٌ',
                    'clean' => 'عدالة',
                    'meaning' => 'إِعْطَاءُ الْحُقُوقِ وَالْفَصْلُ بَيْنَ النَّاسِ بِالْحَقِّ',
                    'image' => '/assets/lesson-images/N6/P2/SEM2/vocab/image39.png'
                ],
                [
                    'word' => 'مُؤَسَّسَاتٌ',
                    'clean' => 'مؤسسات',
                    'meaning' => 'هَيْئَاتٌ رَسْمِيَّةٌ تُدِيرُ شُؤُونَ الدَّوْلَةِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM2/vocab/image40.png'
                ],
                [
                    'word' => 'فَصْلٌ',
                    'clean' => 'فصل',
                    'meaning' => 'اسْتِقْلَالُ السُّلْطَةِ الْقَضَائِيَّةِ وَالتَّشْرِيعِيَّةِ وَالتَّنْفِيذِيَّةِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM2/vocab/image41.png'
                ],
                [
                    'word' => 'مَشْرُوعِيَّةٌ',
                    'clean' => 'مشروعية',
                    'meaning' => 'مُطَابَقَةُ الْأَعْمَالِ لِنُصُوصِ الدُّسْتُورِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM2/vocab/image39.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: العمل الجمعوي والمبادرات التطوعية',
                'lesson_sentence' => 'قَامَ الْخَلِيفَةُ عُمَرُ، عَدْلُهُ مَشْهُورٌ، بِمُسَانَدَةِ كُلِّ الْمُحْتَاجِينَ فِي أَوْقَاتِ الشِّدَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'مُجْتَمَعٌ مَدَنِيٌّ',
                    'clean' => 'مجتمع مدني',
                    'meaning' => 'جَمْعِيَّاتٌ وَمُنَظَّمَاتٌ تَطَوُّعِيَّةٌ تَعْمَلُ لِلْخَيْرِ الْعَامِّ',
                    'image' => '/assets/lesson-images/N6/P2/SEM3/vocab/image38.png'
                ],
                [
                    'word' => 'مُبَادَرَةٌ',
                    'clean' => 'مبادرة',
                    'meaning' => 'عَمَلٌ رِيَادِيٌّ يَنْطَلِقُ لِحَلِّ مُشْكِلَةٍ اجْتِمَاعِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P2/SEM3/vocab/image39.png'
                ],
                [
                    'word' => 'تَضَامُنٌ',
                    'clean' => 'تضامن',
                    'meaning' => 'تَآزُرٌ قَوِيٌّ بَيْنَ أَبْنَاءِ الْحَيِّ وَالْوَطَنِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM3/vocab/image40.png'
                ],
                [
                    'word' => 'خِدْمَةٌ',
                    'clean' => 'خدمة',
                    'meaning' => 'تَقْدِيمُ الْعَوْنِ لِلْمُحْتَاجِينَ وَالْفِئَاتِ الْهَشَّةِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM3/vocab/image38.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: ثقافة الحوار وحل النزاعات سلمياً',
                'lesson_sentence' => 'يُفْضِي الْحِوَارُ الْبَنَّاءُ نَفْسُهُ إِلَى تَسْوِيَةِ الْخِلَافَاتِ كُلِّهَا بِطُرُقٍ حَضَارِيَّةٍ.',
                'vocabulary' => [
                [
                    'word' => 'تَفَاوُضٌ',
                    'clean' => 'تفاوض',
                    'meaning' => 'جُلُوسٌ مَعاً لِلتَّوَصُّلِ إِلَى حُلُولٍ مَرْضِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P2/SEM4/vocab/image3.png'
                ],
                [
                    'word' => 'صُلْحٌ',
                    'clean' => 'صلح',
                    'meaning' => 'إِنْهَاءُ النِّزَاعَاتِ بِالْمَحَبَّةِ وَالتَّرَاضِي',
                    'image' => '/assets/lesson-images/N6/P2/SEM4/vocab/image4.png'
                ],
                [
                    'word' => 'وِئَامٌ',
                    'clean' => 'وئام',
                    'meaning' => 'سَلَامٌ وَانْسِجَامٌ دَاخِلَ الْمُجْتَمَعِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM4/vocab/image40.png'
                ],
                [
                    'word' => 'تَعَايُشٌ',
                    'clean' => 'تعايش',
                    'meaning' => 'الْعَيْشُ فِي أَمَانٍ مَعَ احْتِرَامِ الْفُرُوقِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM4/vocab/image3.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 2',
                'lesson_sentence' => 'تَحَقَّقَ النَّجَاحُ كُلُّهُ بِفَضْلِ إِخْلَاصِ الْمُوَاطِنِينَ جَمِيعِهِمْ فِي أَدَاءِ الْوَاجِبِ.',
                'vocabulary' => [
                [
                    'word' => 'انْتِخَابَاتٌ',
                    'clean' => 'انتخابات',
                    'meaning' => 'صَوْتُ الشَّعْبِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM5/vocab/image36.png'
                ],
                [
                    'word' => 'مُؤَسَّسَاتٌ',
                    'clean' => 'مؤسسات',
                    'meaning' => 'رَكِيزَةُ الدَّوْلَةِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM5/vocab/image37.png'
                ],
                [
                    'word' => 'مُبَادَرَةٌ',
                    'clean' => 'مبادرة',
                    'meaning' => 'عَمَلٌ تَطَوُّعِيٌّ',
                    'image' => '/assets/lesson-images/N6/P2/SEM5/vocab/image38.png'
                ],
                [
                    'word' => 'صُلْحٌ',
                    'clean' => 'صلح',
                    'meaning' => 'إِحْلَالُ السَّلَامِ',
                    'image' => '/assets/lesson-images/N6/P2/SEM5/vocab/image36.png'
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
