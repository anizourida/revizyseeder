<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الرابعة ابتدائي (N4)
 * Unit: الوحدة 5: الغابة والبيئة والتنوع الطبيعي (P5)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN4P5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الغابة رئة الأرض وحامية التوازن',
                'lesson_sentence' => 'تُمَثِّلُ الْغَابَاتُ رِئَةَ الْأَرْضِ الَّتِي تَمُدُّ الْكَائِنَاتِ بِالْأُكْسِجِينِ النَّقِيِّ.',
                'vocabulary' => [
                [
                    'word' => 'أُكْسِجِينٌ',
                    'clean' => 'أكسجين',
                    'meaning' => 'غَازٌ حَيَوِيٌّ لِتَنَفُّسِ الْكَائِنَاتِ الْحَيَّةِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM1/vocab/image76.png'
                ],
                [
                    'word' => 'تَصَحُّرٌ',
                    'clean' => 'تصحر',
                    'meaning' => 'تَحَوُّلُ الْأَرَاضِي الْخَصْبَةِ إِلَى صَحْرَاءَ جَدْبَاءَ',
                    'image' => '/assets/lesson-images/N4/P5/SEM1/vocab/image78.png'
                ],
                [
                    'word' => 'ثَرْوَةٌ',
                    'clean' => 'ثروة',
                    'meaning' => 'خَيْرٌ طَبِيعِيٌّ كَبِيرٌ يَجِبُ صَوْنُهُ',
                    'image' => '/assets/lesson-images/N4/P5/SEM1/vocab/image79.png'
                ],
                [
                    'word' => 'تَنَوُّعٌ',
                    'clean' => 'تنوع',
                    'meaning' => 'كَثْرَةُ الْأَصْنَافِ النَّبَاتِيَّةِ وَالْحَيَوَانِيَّةِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM1/vocab/image76.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الحفاظ على الغطاء النباتي ومنع الحرائق',
                'lesson_sentence' => 'نَظَّمَ النَّادِي الْبِيئِيُّ حَمْلَةَ تَشْجِيرٍ كُبْرَى حِمَايَةً لِلْغِطَاءِ النَّبَاتِيِّ.',
                'vocabulary' => [
                [
                    'word' => 'تَشْجِيرٌ',
                    'clean' => 'تشجير',
                    'meaning' => 'غَرْسُ أَشْجَارٍ جَدِيدَةٍ لِتَعْوِيضِ مَا تَلِفَ',
                    'image' => '/assets/lesson-images/N4/P5/SEM2/vocab/image23.png'
                ],
                [
                    'word' => 'حَرَائِقُ',
                    'clean' => 'حرائق',
                    'meaning' => 'نِيرَانٌ تَلْتَهِمُ الْغِطَاءَ النَّبَاتِيَّ',
                    'image' => '/assets/lesson-images/N4/P5/SEM2/vocab/image46.png'
                ],
                [
                    'word' => 'حِمَايَةٌ',
                    'clean' => 'حماية',
                    'meaning' => 'حِرَاسَةُ الْغَابَةِ وَمَنْعُ قَطْعِ الْأَشْجَارِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM2/vocab/image65.png'
                ],
                [
                    'word' => 'مَحْمِيَّةٌ',
                    'clean' => 'محمية',
                    'meaning' => 'مِنْطَقَةٌ طَبِيعِيَّةٌ مَصُونَةٌ بِمُوجِبِ الْقَانُونِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM2/vocab/image23.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الكائنات المهددة بالانقراض وحمايتها',
                'lesson_sentence' => 'تَحْمِي الْمَحْمِيَّاتُ الطَّبِيعِيَّةُ الْحَيَوَانَاتِ النَّادِرَةَ مِنَ الصَّيْدِ الْجَائِرِ.',
                'vocabulary' => [
                [
                    'word' => 'انْقِرَاضٌ',
                    'clean' => 'انقراض',
                    'meaning' => 'اخْتِفَاءُ نَوْعٍ مِنَ الْكَائِنَاتِ نِهَائِيّاً مِنَ الْوُجُودِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM3/vocab/image74.png'
                ],
                [
                    'word' => 'مَوْطِنٌ',
                    'clean' => 'موطن',
                    'meaning' => 'الْمَكَانُ الطَّبِيعِيُّ الَّذِي يَعِيشُ فِيهِ الْحَيَوَانُ',
                    'image' => '/assets/lesson-images/N4/P5/SEM3/vocab/image75.png'
                ],
                [
                    'word' => 'مُهَدَّدٌ',
                    'clean' => 'مهدد',
                    'meaning' => 'مُعَرَّضٌ لِخَطَرِ الزَّوَالِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM3/vocab/image76.png'
                ],
                [
                    'word' => 'سُلَالَةٌ',
                    'clean' => 'سلالة',
                    'meaning' => 'مَجْمُوعَةٌ حَيَوَانِيَّةٌ تَحْمِلُ صِفَاتٍ وَرَاثِيَّةً مُمَيَّزَةً',
                    'image' => '/assets/lesson-images/N4/P5/SEM3/vocab/image74.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: التوازن البيئي والتنمية المستدامة',
                'lesson_sentence' => 'يُعَدُّ الْحِفَاظُ عَلَى التَّوَازُنِ الْبِيئِيِّ ضَمَانَةً حَقِيقِيَّةً لِحَيَاةِ الْبَشَرِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'تَلَوُّثٌ',
                    'clean' => 'تلوث',
                    'meaning' => 'إِفْسَادُ الْهَوَاءِ وَالْمَاءِ وَالتُّرْبَةِ بِالْمُخَلَّفَاتِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM4/vocab/image13.png'
                ],
                [
                    'word' => 'تَوَازُنٌ',
                    'clean' => 'توازن',
                    'meaning' => 'تَنَاسُقٌ دَقِيقٌ بَيْنَ كُلِّ عَنَاصِرِ الطَّبِيعَةِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM4/vocab/image21.png'
                ],
                [
                    'word' => 'مُنَاخٌ',
                    'clean' => 'مناخ',
                    'meaning' => 'حَالَةُ الطَّقْسِ الْعَامَّةُ عَلَى مَدَى فَتَرَاتٍ طَوِيلَةٍ',
                    'image' => '/assets/lesson-images/N4/P5/SEM4/vocab/image58.png'
                ],
                [
                    'word' => 'اسْتِدَامَةٌ',
                    'clean' => 'استدامة',
                    'meaning' => 'اسْتِغْلَالُ الْمَوَارِدِ دُونَ إِضْرَارٍ بِمُسْتَقْبَلِ الْأَجْيَالِ',
                    'image' => '/assets/lesson-images/N4/P5/SEM4/vocab/image13.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم نهاية السنة الرابعة ابتدائي',
                'lesson_sentence' => 'الْحِفَاظُ عَلَى الْغَابَةِ وَالتَّنَوُّعِ الْبَيُولُوجِيِّ أَمَانَةٌ تَقَعُ عَلَى عَاتِقِنَا.',
                'vocabulary' => [
                [
                    'word' => 'أُكْسِجِينٌ',
                    'clean' => 'أكسجين',
                    'meaning' => 'نَفَسُ الْحَيَاةِ',
                    'image' => '/assets/lesson-images/N4/SEM5/vocab/image54.png'
                ],
                [
                    'word' => 'تَشْجِيرٌ',
                    'clean' => 'تشجير',
                    'meaning' => 'إِحْيَاءُ الْغَابَاتِ',
                    'image' => '/assets/lesson-images/N4/SEM5/vocab/image56.png'
                ],
                [
                    'word' => 'انْقِرَاضٌ',
                    'clean' => 'انقراض',
                    'meaning' => 'خَطَرُ الزَّوَالِ',
                    'image' => '/assets/lesson-images/N4/SEM5/vocab/image54.png'
                ],
                [
                    'word' => 'اسْتِدَامَةٌ',
                    'clean' => 'استدامة',
                    'meaning' => 'حِفْظُ الْمُسْتَقْبَلِ',
                    'image' => '/assets/lesson-images/N4/SEM5/vocab/image56.png'
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
