<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الخامسة ابتدائي (N5)
 * Unit: الوحدة 3: العلم والتكنولوجيا والذكاء الاصطناعي (P3)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN5P3VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الثورة الرقمية وتطبيقات الذكاء الاصطناعي',
                'lesson_sentence' => 'أَحْدَثَ الذَّكَاءُ الِاصْطِنَاعِيُّ قَفْزَةً نَوْعِيَّةً فِي مَجَالَاتِ الْبَحْثِ الْعِلْمِيِّ.',
                'vocabulary' => [
                [
                    'word' => 'بَرْمَجَةٌ',
                    'clean' => 'برمجة',
                    'meaning' => 'كِتَابَةُ أَوَامِرَ دَقِيقَةٍ لِتَشْغِيلِ الْحَوَاسِيبِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM1/vocab/image15.png'
                ],
                [
                    'word' => 'خَوَارِزْمِيَّةٌ',
                    'clean' => 'خوارزمية',
                    'meaning' => 'خُطُوَاتٌ مَنْطِقِيَّةٌ مُتَسَلْسِلَةٌ لِحَلِّ الْمَسَائِلِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM1/vocab/image40.png'
                ],
                [
                    'word' => 'ذَكَاءٌ',
                    'clean' => 'ذكاء',
                    'meaning' => 'قُدْرَةُ الْآلَاتِ عَلَى مُحَاكَاةِ التَّفْكِيرِ الْبَشَرِيِّ',
                    'image' => '/assets/lesson-images/N5/P3/SEM1/vocab/image41.png'
                ],
                [
                    'word' => 'ابْتِكَارٌ',
                    'clean' => 'ابتكار',
                    'meaning' => 'إِبْدَاعُ حُلُولٍ وَاخْتِرَاعَاتٍ جَدِيدَةٍ',
                    'image' => '/assets/lesson-images/N5/P3/SEM1/vocab/image15.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: غزو الفضاء والأقمار الاصطناعية المغربية',
                'lesson_sentence' => 'تَرْصُدُ الْأَقْمَارُ الِاصْطِنَاعِيَّةُ التَّغَيُّرَاتِ الْمَنَاخِيَّةَ بِدِقَّةٍ مُتَنَاهِيَةٍ.',
                'vocabulary' => [
                [
                    'word' => 'مَدَارٌ',
                    'clean' => 'مدار',
                    'meaning' => 'مَسَارٌ دَائِرِيٌّ يَسْلُكُهُ الْقَمَرُ حَوْلَ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM2/vocab/image14.png'
                ],
                [
                    'word' => 'مَكُّوكٌ',
                    'clean' => 'مكوك',
                    'meaning' => 'مَرْكَبَةٌ فَضَائِيَّةٌ لِنَقْلِ الرُّوَّادِ وَالْمُعَدَّاتِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM2/vocab/image39.png'
                ],
                [
                    'word' => 'اسْتِكْشَافٌ',
                    'clean' => 'استكشاف',
                    'meaning' => 'سَبْرُ أَغْوَارِ الْفَضَاءِ وَمَعْرِفَةُ أَسْرَارِهِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM2/vocab/image40.png'
                ],
                [
                    'word' => 'كَوْكَبٌ',
                    'clean' => 'كوكب',
                    'meaning' => 'جِرْمٌ سَمَاوِيٌّ يَدُورُ حَوْلَ الشَّمْسِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM2/vocab/image14.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الطب الحديث والهندسة الجينية واللقاحات',
                'lesson_sentence' => 'تَوَصَّلَ الْعُلَمَاءُ إِلَى عِلَاجَاتٍ مُتَطَوِّرَةٍ بِفَضْلِ التَّقَدُّمِ الْجِينِيِّ الْمُذْهِلِ.',
                'vocabulary' => [
                [
                    'word' => 'عِلَاجٌ',
                    'clean' => 'علاج',
                    'meaning' => 'دَوَاءٌ وَطَرِيقَةٌ طِبِّيَّةٌ لِلْقَضَاءِ عَلَى الْمَرَضِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM3/vocab/image81.png'
                ],
                [
                    'word' => 'مِجْهَرٌ',
                    'clean' => 'مجهر',
                    'meaning' => 'آلَةٌ لِتَكْبِيرِ الْكَائِنَاتِ الدَّقِيقَةِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM3/vocab/image82.png'
                ],
                [
                    'word' => 'جِينِيٌّ',
                    'clean' => 'جيني',
                    'meaning' => 'مُرْتَبِطٌ بِالْوِرَاثَةِ وَالْخَلَايَا الْحَيَّةِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM3/vocab/image83.png'
                ],
                [
                    'word' => 'لِقَاحٌ',
                    'clean' => 'لقاح',
                    'meaning' => 'مَادَّةٌ تُعْطَى لِلْجِسْمِ لِتَحْفِيزِ مَنَاعَتِهِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM3/vocab/image81.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: شبكات الاتصال والألياف البصرية',
                'lesson_sentence' => 'جَعَلَتْ شَبَكَاتُ الِاتِّصَالِ الْحَدِيثَةُ الْعَالَمَ قَرْيَةً صَغِيرَةً مُتَرَابِطَةً.',
                'vocabulary' => [
                [
                    'word' => 'أَلْيَافٌ',
                    'clean' => 'ألياف',
                    'meaning' => 'خُيُوطٌ زُجَاجِيَّةٌ دَقِيقَةٌ لِنَقْلِ الْبَيَانَاتِ بِسُرْعَةِ الضَّوْءِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM4/vocab/image14.png'
                ],
                [
                    'word' => 'سُرْعَةٌ',
                    'clean' => 'سرعة',
                    'meaning' => 'تَدَفُّقٌ خَارِقٌ لِلْمَعْلُومَاتِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM4/vocab/image48.jpeg'
                ],
                [
                    'word' => 'مَعْلُومَاتٌ',
                    'clean' => 'معلومات',
                    'meaning' => 'بَيَانَاتٌ مُفِيدَةٌ وَمُعَالَجَةٌ رَقْمِيّاً',
                    'image' => '/assets/lesson-images/N5/P3/SEM4/vocab/image49.png'
                ],
                [
                    'word' => 'تَوَاصُلٌ',
                    'clean' => 'تواصل',
                    'meaning' => 'تَبَادُلُ الرَّسَائِلِ وَالْأَفْكَارِ عَبْرَ الشَّبَكَاتِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM4/vocab/image14.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 3',
                'lesson_sentence' => 'تَتَسَابَقُ الدُّوَلُ فِي ابْتِكَارِ التَّقْنِيَّاتِ لِتَسْهِيلِ حَيَاةِ الْبَشَرِ.',
                'vocabulary' => [
                [
                    'word' => 'بَرْمَجَةٌ',
                    'clean' => 'برمجة',
                    'meaning' => 'لُغَةُ الْعَصْرِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM5/vocab/image14.png'
                ],
                [
                    'word' => 'مَدَارٌ',
                    'clean' => 'مدار',
                    'meaning' => 'مَسَارُ الْأَقْمَارِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM5/vocab/image42.png'
                ],
                [
                    'word' => 'مِجْهَرٌ',
                    'clean' => 'مجهر',
                    'meaning' => 'أَدَاةُ الْعُلَمَاءِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM5/vocab/image46.jpeg'
                ],
                [
                    'word' => 'أَلْيَافٌ',
                    'clean' => 'ألياف',
                    'meaning' => 'شَرَايِينُ التَّوَاصُلِ',
                    'image' => '/assets/lesson-images/N5/P3/SEM5/vocab/image14.png'
                ]
                ]
            ],
        ];

        foreach ($curriculum as $week) {
            foreach ($week['vocabulary'] as $item) {
                // Insert or update vocabulary words safely without dropping tables
                DB::table('vocabularies')->updateOrInsert(
                    [
                        'grade' => 'N5',
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
