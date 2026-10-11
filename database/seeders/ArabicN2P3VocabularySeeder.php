<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثانية ابتدائي (N2)
 * Unit: الوحدة 3: التغذية والصحة والرياضة (P3)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN2P3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الوجبة الغذائية المتوازنة',
                'lesson_sentence' => 'الْوَجْبَةُ الصِّحِّيَّةُ تَمْنَحُ الْجِسْمَ الطَّاقَةَ وَالْحَيَوِيَّةَ.',
                'vocabulary' => [
                [
                    'word' => 'خُضَرٌ',
                    'clean' => 'خضر',
                    'meaning' => 'نَبَاتَاتٌ غِذَائِيَّةٌ مَفِيدَةٌ',
                    'image' => '/assets/lesson-images/N2/P3/SEM1/vocab/image57.png'
                ],
                [
                    'word' => 'فَوَاكِهُ',
                    'clean' => 'فواكه',
                    'meaning' => 'ثِمَارٌ حُلْوَةٌ طَازَجَةٌ',
                    'image' => '/assets/lesson-images/N2/P3/SEM1/vocab/image58.png'
                ],
                [
                    'word' => 'بَرُوتِينٌ',
                    'clean' => 'بروتين',
                    'meaning' => 'عُنْصُرٌ يُقَوِّي الْعَضَلَاتِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM1/vocab/image68.png'
                ],
                [
                    'word' => 'طَاقَةٌ',
                    'clean' => 'طاقة',
                    'meaning' => 'قُوَّةٌ لِلْحَرَكَةِ وَالنَّشَاطِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM1/vocab/image57.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: نظافة الجسم والوقاية من الجراثيم',
                'lesson_sentence' => 'أُنَظِّفُ أَسْنَانِي بِالْفُرْشَاةِ بَعْدَ كُلِّ وَجْبَةِ طَعَامٍ.',
                'vocabulary' => [
                [
                    'word' => 'اسْتِحْمَامٌ',
                    'clean' => 'استحمام',
                    'meaning' => 'غَسْلُ الْبَدَنِ كُلَّهُ بِالْمَاءِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM2/vocab/image26.png'
                ],
                [
                    'word' => 'فُرْشَاةٌ',
                    'clean' => 'فرشاة',
                    'meaning' => 'أَدَاةُ تَنْظِيفِ الْأَسْنَانِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM2/vocab/image27.png'
                ],
                [
                    'word' => 'سِنٌّ',
                    'clean' => 'سن',
                    'meaning' => 'عُضْوُ قَطْعِ الطَّعَامِ وَمَضْغِهِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM2/vocab/image28.png'
                ],
                [
                    'word' => 'طَهَارَةٌ',
                    'clean' => 'طهارة',
                    'meaning' => 'نَقَاءٌ وَنَظَافَةٌ شَامِلَةٌ',
                    'image' => '/assets/lesson-images/N2/P3/SEM2/vocab/image26.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الرياضة والنشاط البدني',
                'lesson_sentence' => 'الرِّيَاضَةُ الْيَوْمِيَّةُ تُقَوِّي الْعَضَلَاتِ وَتُنَشِّطُ الذِّهْنَ.',
                'vocabulary' => [
                [
                    'word' => 'جَرْيٌ',
                    'clean' => 'جري',
                    'meaning' => 'رَكْضٌ سَرِيعٌ يُنَشِّطُ الْقَلْبَ',
                    'image' => '/assets/lesson-images/N2/P3/SEM3/vocab/image16.jpg'
                ],
                [
                    'word' => 'سِبَاحَةٌ',
                    'clean' => 'سباحة',
                    'meaning' => 'رِيَاضَةُ الْعَوْمِ فِي الْمَاءِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM3/vocab/image19.png'
                ],
                [
                    'word' => 'لِيَاقَةٌ',
                    'clean' => 'لياقة',
                    'meaning' => 'قُوَّةٌ بَدَنِيَّةٌ وَخِفَّةٌ',
                    'image' => '/assets/lesson-images/N2/P3/SEM3/vocab/image25.png'
                ],
                [
                    'word' => 'مُبَارَاةٌ',
                    'clean' => 'مباراة',
                    'meaning' => 'مُنَافَسَةٌ رِيَاضِيَّةٌ شَرِيفَةٌ',
                    'image' => '/assets/lesson-images/N2/P3/SEM3/vocab/image16.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: زيارة الطبيب وتناول الدواء',
                'lesson_sentence' => 'يَفْحَصُ الطَّبِيبُ الطِّفْلَ بِلُطْفٍ وَيَصِفُ لَهُ الدَّوَاءَ.',
                'vocabulary' => [
                [
                    'word' => 'عِيَادَةٌ',
                    'clean' => 'عيادة',
                    'meaning' => 'مَكَانُ فَحْصِ الْمَرْضَى',
                    'image' => '/assets/lesson-images/N2/P3/SEM4/vocab/image33.png'
                ],
                [
                    'word' => 'فَحْصٌ',
                    'clean' => 'فحص',
                    'meaning' => 'مُعَايَنَةُ الطَّبِيبِ لِلْمَرِيضِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM4/vocab/image43.png'
                ],
                [
                    'word' => 'دَوَاءٌ',
                    'clean' => 'دواء',
                    'meaning' => 'عِلَاجٌ لِإِذْهَابِ الْمَرَضِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM4/vocab/image44.png'
                ],
                [
                    'word' => 'شِفَاءٌ',
                    'clean' => 'شفاء',
                    'meaning' => 'عَوْدَةُ الصِّحَّةِ وَالْعَافِيَةِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM4/vocab/image33.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 3',
                'lesson_sentence' => 'مُمَارَسَةُ الرِّيَاضَةِ وَالْغِذَاءُ الصِّحِّيُّ يَقِيَانِ مِنَ الْأَمْرَاضِ.',
                'vocabulary' => [
                [
                    'word' => 'خُضَرٌ',
                    'clean' => 'خضر',
                    'meaning' => 'غِذَاءُ الصِّحَّةِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM5/vocab/image11.png'
                ],
                [
                    'word' => 'فُرْشَاةٌ',
                    'clean' => 'فرشاة',
                    'meaning' => 'أَدَاةُ التَّنْظِيفِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM5/vocab/image45.png'
                ],
                [
                    'word' => 'سِبَاحَةٌ',
                    'clean' => 'سباحة',
                    'meaning' => 'رِيَاضَةٌ مُفِيدَةٌ',
                    'image' => '/assets/lesson-images/N2/P3/SEM5/vocab/image58.png'
                ],
                [
                    'word' => 'شِفَاءٌ',
                    'clean' => 'شفاء',
                    'meaning' => 'عَافِيَةُ الْبَدَنِ',
                    'image' => '/assets/lesson-images/N2/P3/SEM5/vocab/image11.png'
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
