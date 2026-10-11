<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثالثة ابتدائي (N3)
 * Unit: الوحدة 3: الوقاية والصحة والبيئة السليمة (P3)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN3P3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: النظافة الشخصية وحصن الوقاية',
                'lesson_sentence' => 'إِنَّ النَّظَافَةَ حِصْنٌ مَنِيعٌ يَقِي الْجِسْمَ مِنَ الْجَرَاثِيمِ.',
                'vocabulary' => [
                [
                    'word' => 'تَعْقِيمٌ',
                    'clean' => 'تعقيم',
                    'meaning' => 'تَطْهِيرُ الْأَدَوَاتِ وَالْأَيْدِي مِنَ الْمِيكْرُوبَاتِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM1/vocab/image47.png'
                ],
                [
                    'word' => 'فَيْرُوسٌ',
                    'clean' => 'فيروس',
                    'meaning' => 'كَائِنٌ دَقِيقٌ يُسَبِّبُ الْعَدْوَى وَالْمَرَضَ',
                    'image' => '/assets/lesson-images/N3/P3/SEM1/vocab/image48.png'
                ],
                [
                    'word' => 'جَرَاثِيمُ',
                    'clean' => 'جراثيم',
                    'meaning' => 'مَخْلُوقَاتٌ صَغِيرَةٌ لَا تُرَى بِالْعَيْنِ الْمُجَرَّدَةِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM1/vocab/image49.jpeg'
                ],
                [
                    'word' => 'وِقَايَةٌ',
                    'clean' => 'وقاية',
                    'meaning' => 'حِمَايَةُ الْجِسْمِ قَبْلَ وُقُوعِ الْمَرَضِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM1/vocab/image47.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: الغذاء المتكامل وجهاز المناعة',
                'lesson_sentence' => 'لَعَلَّ الْأَغْذِيَةَ الطَّازَجَةَ تُقَوِّي جِهَازَ الْمَنَاعَةِ لَدَى الْأَطْفَالِ.',
                'vocabulary' => [
                [
                    'word' => 'فِيتَامِينٌ',
                    'clean' => 'فيتامين',
                    'meaning' => 'عُنْصُرٌ غِذَائِيٌّ ضَرُورِيٌّ لِنُمُوِّ الْجِسْمِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM2/vocab/image43.png'
                ],
                [
                    'word' => 'مَنَاعَةٌ',
                    'clean' => 'مناعة',
                    'meaning' => 'قُدْرَةُ الْجِسْمِ عَلَى مُقَاوَمَةِ الْأَمْرَاضِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM2/vocab/image44.png'
                ],
                [
                    'word' => 'خُضْرَاوَاتٌ',
                    'clean' => 'خضراوات',
                    'meaning' => 'أَغْذِيَةٌ نَبَاتِيَّةٌ طَازَجَةٌ',
                    'image' => '/assets/lesson-images/N3/P3/SEM2/vocab/image45.png'
                ],
                [
                    'word' => 'نَشَاطٌ',
                    'clean' => 'نشاط',
                    'meaning' => 'قُوَّةٌ وَحَيَوِيَّةٌ فِي الْحَرَكَةِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM2/vocab/image43.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: صحة الأسنان والبدن والرعاية الطبية',
                'lesson_sentence' => 'فَحَصَ طَبِيبُ الْأَسْنَانِ فَمَ الطِّفْلِ بِعِنَايَةٍ وَقَدَّمَ لَهُ نَصَائِحَ.',
                'vocabulary' => [
                [
                    'word' => 'فُرْشَاةٌ',
                    'clean' => 'فرشاة',
                    'meaning' => 'أَدَاةُ تَنْظِيفِ الْأَسْنَانِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM3/vocab/image52.png'
                ],
                [
                    'word' => 'تَسَوُّسٌ',
                    'clean' => 'تسوس',
                    'meaning' => 'تَآكُلُ السِّنِّ بِسَبَبِ إِهْمَالِ النَّظَافَةِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM3/vocab/image53.png'
                ],
                [
                    'word' => 'عِيَادَةٌ',
                    'clean' => 'عيادة',
                    'meaning' => 'مَقَرُّ الْعِلَاجِ وَالْفَحْصِ الطِّبِّيِّ',
                    'image' => '/assets/lesson-images/N3/P3/SEM3/vocab/image54.png'
                ],
                [
                    'word' => 'رِعَايَةٌ',
                    'clean' => 'رعاية',
                    'meaning' => 'عِنَايَةٌ وَاهْتِمَامٌ بِالصِّحَّةِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM3/vocab/image52.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الإسعافات الأولية وسرعة الإنقاذ',
                'lesson_sentence' => 'قَدَّمَ الْمُسْعِفُ الْعِلَاجَ الْأَوَّلِيَّ لِلْمُصَابِ بِسُرْعَةٍ وَمَهَارَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'ضِمَادٌ',
                    'clean' => 'ضماد',
                    'meaning' => 'شَرِيطٌ لِتَغْطِيَةِ الْجُرُوحِ وَحِمَايَتِهَا',
                    'image' => '/assets/lesson-images/N3/P3/SEM4/vocab/image17.png'
                ],
                [
                    'word' => 'حَذَرٌ',
                    'clean' => 'حذر',
                    'meaning' => 'انْتِبَاهٌ وَاحْتِرَاسٌ مِنَ الْأَخْطَارِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM4/vocab/image49.jpeg'
                ],
                [
                    'word' => 'سَلَامَةٌ',
                    'clean' => 'سلامة',
                    'meaning' => 'نَجَاةٌ مِنَ الْأَذَى',
                    'image' => '/assets/lesson-images/N3/P3/SEM4/vocab/image50.png'
                ],
                [
                    'word' => 'إِنْقَاذٌ',
                    'clean' => 'إنقاذ',
                    'meaning' => 'تَخْلِيصُ الْمُصَابِ مِنَ الْخَطَرِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM4/vocab/image17.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 3',
                'lesson_sentence' => 'إِنَّ الْوِقَايَةَ خَيْرٌ مِنَ الْعِلَاجِ لِكُلِّ إِنْسَانٍ.',
                'vocabulary' => [
                [
                    'word' => 'تَعْقِيمٌ',
                    'clean' => 'تعقيم',
                    'meaning' => 'تَطْهِيرُ الْبَدَنِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM5/vocab/image35.png'
                ],
                [
                    'word' => 'مَنَاعَةٌ',
                    'clean' => 'مناعة',
                    'meaning' => 'مُقَاوَمَةُ الْمَرَضِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM5/vocab/image36.png'
                ],
                [
                    'word' => 'تَسَوُّسٌ',
                    'clean' => 'تسوس',
                    'meaning' => 'تَلَفُ السِّنِّ',
                    'image' => '/assets/lesson-images/N3/P3/SEM5/vocab/image37.png'
                ],
                [
                    'word' => 'إِنْقَاذٌ',
                    'clean' => 'إنقاذ',
                    'meaning' => 'إِسْعَافُ الْمُصَابِ',
                    'image' => '/assets/lesson-images/N3/P3/SEM5/vocab/image35.png'
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
