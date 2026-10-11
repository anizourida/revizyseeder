<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الأولى ابتدائي (N1)
 * Unit: الوحدة 3: الهندام والنظافة والتغذية (P3)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN1P3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: حرف الجيم (ج) وَالتَّغْذِيَةُ السَّلِيمَةُ',
                'lesson_sentence' => 'آكُلُ جَزَراً طَازَجاً لِتَقْوِيَةِ جِسْمِي.',
                'vocabulary' => [
                [
                    'word' => 'جَزَرٌ',
                    'clean' => 'جزر',
                    'meaning' => 'خُضَارٌ جَذْرِيٌّ مُفِيدٌ لِلْبَصَرِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM1/vocab/image69.png'
                ],
                [
                    'word' => 'جُبْنٌ',
                    'clean' => 'جبن',
                    'meaning' => 'غِذَاءٌ مَصْنُوعٌ مِنَ الْحَلِيبِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM1/vocab/image70.png'
                ],
                [
                    'word' => 'جِسْمٌ',
                    'clean' => 'جسم',
                    'meaning' => 'بَدَنُ الْإِنْسَانِ السَّلِيمُ',
                    'image' => '/assets/lesson-images/N1/P3/SEM1/vocab/image71.png'
                ],
                [
                    'word' => 'جَوْرَبٌ',
                    'clean' => 'جورب',
                    'meaning' => 'لِبَاسُ الْقَدَمِ الدَّافِئُ',
                    'image' => '/assets/lesson-images/N1/P3/SEM1/vocab/image69.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حرف الحاء (ح) وَالنَّظَافَةُ الصَّبَاحِيَّةُ',
                'lesson_sentence' => 'أَشْرَبُ كُوبَ حَلِيبٍ دَافِئٍ فِي الصَّبَاحِ.',
                'vocabulary' => [
                [
                    'word' => 'حَلِيبٌ',
                    'clean' => 'حليب',
                    'meaning' => 'شَرَابٌ أَبْيَضُ يُقَوِّي الْعِظَامَ',
                    'image' => '/assets/lesson-images/N1/P3/SEM2/vocab/image108.png'
                ],
                [
                    'word' => 'حَمَّامٌ',
                    'clean' => 'حمام',
                    'meaning' => 'مَكَانُ الِاغْتِسَالِ وَالتَّطَهُّرِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM2/vocab/image109.png'
                ],
                [
                    'word' => 'حَوْضٌ',
                    'clean' => 'حوض',
                    'meaning' => 'وِعَاءُ الْمَاءِ لِغَسْلِ الْيَدَيْنِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM2/vocab/image15.png'
                ],
                [
                    'word' => 'حَبْلٌ',
                    'clean' => 'حبل',
                    'meaning' => 'أَدَاةُ لَعِبِ الْقَفْزِ الرِّيَاضِيِّ',
                    'image' => '/assets/lesson-images/N1/P3/SEM2/vocab/image108.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حرف الخاء (خ) وَالْغِذَاءُ الْيَوْمِيُّ',
                'lesson_sentence' => 'وَضَعْتُ ثِيَابِي النَّظِيفَةَ فِي الْخِزَانَةِ.',
                'vocabulary' => [
                [
                    'word' => 'خُبْزٌ',
                    'clean' => 'خبز',
                    'meaning' => 'غِذَاءٌ رَئِيسِيٌّ مِنَ الْقَمْحِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM3/vocab/image55.png'
                ],
                [
                    'word' => 'خِزَانَةٌ',
                    'clean' => 'خزانة',
                    'meaning' => 'مَكَانُ حِفْظِ الثِّيَابِ النَّظِيفَةِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM3/vocab/image56.png'
                ],
                [
                    'word' => 'خِيَارٌ',
                    'clean' => 'خيار',
                    'meaning' => 'خُضَارٌ مُنْعِشٌ لِلصَّيْفِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM3/vocab/image57.png'
                ],
                [
                    'word' => 'خَاتَمٌ',
                    'clean' => 'خاتم',
                    'meaning' => 'حِلْيَةٌ تُوضَعُ فِي الْإِصْبَعِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM3/vocab/image55.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: حرف الصاد (ص) وَغَسْلُ الْيَدَيْنِ',
                'lesson_sentence' => 'أَغْسِلُ يَدَيَّ بِالْمَاءِ وَالصَّابُونِ جَيِّداً.',
                'vocabulary' => [
                [
                    'word' => 'صَابُونٌ',
                    'clean' => 'صابون',
                    'meaning' => 'مَادَّةُ تَنْظِيفِ الْبَدَنِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM4/vocab/image108.png'
                ],
                [
                    'word' => 'صَحْنٌ',
                    'clean' => 'صحن',
                    'meaning' => 'إِنَاءُ تَقْدِيمِ الطَّعَامِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM4/vocab/image109.png'
                ],
                [
                    'word' => 'صُنْدُوقٌ',
                    'clean' => 'صندوق',
                    'meaning' => 'وِعَاءُ حِفْظِ الْأَشْيَاءِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM4/vocab/image15.png'
                ],
                [
                    'word' => 'صَبَاحٌ',
                    'clean' => 'صباح',
                    'meaning' => 'أَوَّلُ النَّهَارِ الْمُشْرِقُ',
                    'image' => '/assets/lesson-images/N1/P3/SEM4/vocab/image108.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: حرف الضاد (ض) وَصِحَّةُ الْأَسْنَانِ',
                'lesson_sentence' => 'أُنَظِّفُ أَضْرَاسِي بِالْفُرْشَاةِ كُلَّ يَوْمٍ.',
                'vocabulary' => [
                [
                    'word' => 'ضِرْسٌ',
                    'clean' => 'ضرس',
                    'meaning' => 'سِنٌّ خَلْفِيٌّ لِطَحْنِ الطَّعَامِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM5/vocab/image28.png'
                ],
                [
                    'word' => 'ضِمَادَةٌ',
                    'clean' => 'ضمادة',
                    'meaning' => 'لِفَافَةُ حِمَايَةِ الْجُرْحِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM5/vocab/image45.png'
                ],
                [
                    'word' => 'ضَوْءٌ',
                    'clean' => 'ضوء',
                    'meaning' => 'نُورٌ يُضِيءُ الْمَكَانَ',
                    'image' => '/assets/lesson-images/N1/P3/SEM5/vocab/image59.png'
                ],
                [
                    'word' => 'ضَيْفٌ',
                    'clean' => 'ضيف',
                    'meaning' => 'زَائِرٌ عَزِيزٌ فِي الْبَيْتِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM5/vocab/image28.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM6',
                'week_number' => 6,
                'week_title' => 'الأسبوع 6: تقويم ودعم الحروف (ج، ح، خ، ص، ض)',
                'lesson_sentence' => 'النَّظَافَةُ وَالْغِذَاءُ السَّلِيمُ يَحْمِيَانِ جِسْمِي.',
                'vocabulary' => [
                [
                    'word' => 'صَابُونٌ',
                    'clean' => 'صابون',
                    'meaning' => 'أَدَاةُ التَّنْظِيفِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM6/vocab/image63.png'
                ],
                [
                    'word' => 'حَلِيبٌ',
                    'clean' => 'حليب',
                    'meaning' => 'شَرَابُ الصِّحَّةِ',
                    'image' => '/assets/lesson-images/N1/P3/SEM6/vocab/image64.png'
                ],
                [
                    'word' => 'جَزَرٌ',
                    'clean' => 'جزر',
                    'meaning' => 'خُضَارٌ طَازَجٌ',
                    'image' => '/assets/lesson-images/N1/P3/SEM6/vocab/image63.png'
                ],
                [
                    'word' => 'ضِرْسٌ',
                    'clean' => 'ضرس',
                    'meaning' => 'سِنٌّ قَوِيٌّ',
                    'image' => '/assets/lesson-images/N1/P3/SEM6/vocab/image64.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N1',
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
