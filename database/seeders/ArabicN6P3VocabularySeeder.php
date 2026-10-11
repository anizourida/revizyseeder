<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة السادسة ابتدائي (N6)
 * Unit: الوحدة 3: العلم والتكنولوجيا والأمن السيبراني (P3)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN6P3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الأمن السيبراني وحماية البيانات الرقمية',
                'lesson_sentence' => 'يَحْمِي الْأَمْنُ السِّيبِرَانِيُّ الْمَعْلُومَاتِ الشَّخْصِيَّةَ كُلَّهَا مِنَ الِاخْتِرَاقِ وَالسَّرِقَةِ.',
                'vocabulary' => [
                [
                    'word' => 'أَمْنٌ',
                    'clean' => 'أمن',
                    'meaning' => 'سَلَامَةُ الْأَنْظِمَةِ مِنَ الِاخْتِرَاقِ وَالْعَبَثِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM1/vocab/image31.png'
                ],
                [
                    'word' => 'تَشْفِيرٌ',
                    'clean' => 'تشفير',
                    'meaning' => 'تَحْوِيلُ النُّصُوصِ لِرُمُوزٍ سِرِّيَّةٍ لَا يَفْهَمُهَا غَيْرُ الْمُرْسَلِ إِلَيْهِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM1/vocab/image32.png'
                ],
                [
                    'word' => 'خُصُوصِيَّةٌ',
                    'clean' => 'خصوصية',
                    'meaning' => 'حَقُّ الْفَرْدِ فِي سِرِّيَّةِ بَيَانَاتِهِ وَمَعْلُومَاتِهِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM1/vocab/image33.png'
                ],
                [
                    'word' => 'قَرْصَنَةٌ',
                    'clean' => 'قرصنة',
                    'meaning' => 'سَرِقَةُ الْبَيَانَاتِ وَاخْتِرَاقُ الْحَوَاسِيبِ بِطُرُقٍ غَيْرِ قَانُونِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P3/SEM1/vocab/image31.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: أخلاقيات التطور التكنولوجي ومسؤولية الباحثين',
                'lesson_sentence' => 'يَجِبُ أَنْ تُرَاعِيَ التَّكْنُولُوجْيَا الْحَدِيثَةُ كَرَامَةَ الْإِنْسَانِ وَسَلَامَتَهُ أَوَّلاً.',
                'vocabulary' => [
                [
                    'word' => 'مَسْؤُولِيَّةٌ',
                    'clean' => 'مسؤولية',
                    'meaning' => 'الْتِزَامٌ أَخْلَاقِيٌّ بِعَدَمِ اسْتِخْدَامِ الْعِلْمِ فِي التَّدْمِيرِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM2/vocab/image2.png'
                ],
                [
                    'word' => 'قِيَمٌ',
                    'clean' => 'قيم',
                    'meaning' => 'مَبَادِئُ سَامِيَةٌ تُوَجِّهُ السُّلُوكَ الْإِنْسَانِيَّ',
                    'image' => '/assets/lesson-images/N6/P3/SEM2/vocab/image3.png'
                ],
                [
                    'word' => 'ضَوَابِطُ',
                    'clean' => 'ضوابط',
                    'meaning' => 'قَوَاعِدُ صَارِمَةٌ لِحِمَايَةِ حُرْمَةِ الْحَيَاةِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM2/vocab/image4.png'
                ],
                [
                    'word' => 'إِنْسَانِيَّةٌ',
                    'clean' => 'إنسانية',
                    'meaning' => 'تَقْدِيرُ حَقِّ كُلِّ بَشَرِيٍّ فِي الْأَمَانِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM2/vocab/image2.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: تقنية النانو والاتصالات الدقيقة الفائقة',
                'lesson_sentence' => 'تُتِيحُ تَقْنِيَّةُ النَّانُو صِنَاعَةَ أَجْهِزَةٍ طِبِّيَّةٍ دَقِيقَةٍ جِدّاً ذَاتِ كَفَاءَةٍ عَالِيَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'نَانُو',
                    'clean' => 'نانو',
                    'meaning' => 'جُزْءٌ مِنْ مِلْيَارٍ مِنَ الْمِتْرِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM3/vocab/image46.png'
                ],
                [
                    'word' => 'تَقْنِيَّةٌ',
                    'clean' => 'تقنية',
                    'meaning' => 'تَطْبِيقٌ عَمَلِيٌّ لِلْمَعَارِفِ الْعِلْمِيَّةِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM3/vocab/image47.png'
                ],
                [
                    'word' => 'جُزَيْئَاتٌ',
                    'clean' => 'جزيئات',
                    'meaning' => 'وَحَدَاتٌ دَقِيقَةٌ جِدّاً تُشَكِّلُ الْمَادَّةَ',
                    'image' => '/assets/lesson-images/N6/P3/SEM3/vocab/image48.png'
                ],
                [
                    'word' => 'دِقَّةٌ',
                    'clean' => 'دقة',
                    'meaning' => 'إِحْكَامٌ مُتَنَاهٍ فِي الصُّنْعِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM3/vocab/image46.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: منصات التعليم المستقبلي والرقمنة التربوية',
                'lesson_sentence' => 'أَتَاحَ التَّعَلُّمُ الرَّقْمِيُّ لِلْمُتَعَلِّمِينَ فُرَصاً لَا حَصْرَ لَهَا لِتَطْوِيرِ مَعَارِفِهِمْ.',
                'vocabulary' => [
                [
                    'word' => 'رَقْمَنَةٌ',
                    'clean' => 'رقمنة',
                    'meaning' => 'تَحْوِيلُ الْمَنَاهِجِ وَالْكُتُبِ لِتَطْبِيقَاتٍ تَفَاعُلِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P3/SEM4/vocab/image2.png'
                ],
                [
                    'word' => 'مَنَصَّاتٌ',
                    'clean' => 'منصات',
                    'meaning' => 'مَوَاقِعُ تَعْلِيمِيَّةٌ تُمَكِّنُ مِنَ التَّعَلُّمِ عَنْ بُعْدٍ',
                    'image' => '/assets/lesson-images/N6/P3/SEM4/vocab/image3.png'
                ],
                [
                    'word' => 'تَعَلُّمٌ',
                    'clean' => 'تعلم',
                    'meaning' => 'اكْتِسَابُ الْمَهَارَاتِ بِشَكْلٍ مُسْتَمِرٍّ',
                    'image' => '/assets/lesson-images/N6/P3/SEM4/vocab/image4.png'
                ],
                [
                    'word' => 'تَكَامُلٌ',
                    'clean' => 'تكامل',
                    'meaning' => 'تَوَازُنٌ بَيْنَ حُضُورِيِّ الدِّرَاسَةِ وَرَقْمِيِّهَا',
                    'image' => '/assets/lesson-images/N6/P3/SEM4/vocab/image2.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 3',
                'lesson_sentence' => 'مَنْ يَجْتَهِدْ فِي تَحْصِيلِ الْعُلُومِ الرَّقْمِيَّةِ يَفُزْ بِمَكَانَةٍ عَالِيَةٍ فِي الْمُسْتَقْبَلِ.',
                'vocabulary' => [
                [
                    'word' => 'أَمْنٌ',
                    'clean' => 'أمن',
                    'meaning' => 'حِمَايَةُ الْبَيَانَاتِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM5/vocab/image19.jpg'
                ],
                [
                    'word' => 'تَشْفِيرٌ',
                    'clean' => 'تشفير',
                    'meaning' => 'أَمَانُ الرَّسَائِلِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM5/vocab/image34.png'
                ],
                [
                    'word' => 'نَانُو',
                    'clean' => 'نانو',
                    'meaning' => 'دِقَّةُ الصَّنِيعَةِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM5/vocab/image35.png'
                ],
                [
                    'word' => 'رَقْمَنَةٌ',
                    'clean' => 'رقمنة',
                    'meaning' => 'تَعْلِيمُ الْغَدِ',
                    'image' => '/assets/lesson-images/N6/P3/SEM5/vocab/image19.jpg'
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
