<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة السادسة ابتدائي (N6)
 * Unit: الوحدة 5: تحديات العالم المعاصر والتعاون الدولي (P5)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN6P5VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الأمن الغذائي وشح المياه والجفاف العالمي',
                'lesson_sentence' => 'تُمَثِّلُ مَحَطَّاتُ تَحْلِيَةِ مِيَاهِ الْبَحْرِ حَلّاً اسْتِرَاتِيجِيّاً لِمُوَاجَهَةِ شُحِّ الْمِيَاهِ.',
                'vocabulary' => [
                [
                    'word' => 'مَجَاعَةٌ',
                    'clean' => 'مجاعة',
                    'meaning' => 'نَقْصٌ شَدِيدٌ فِي الْغِذَاءِ يُهَدِّدُ حَيَاةَ الْمَلَايِينِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM1/vocab/image38.png'
                ],
                [
                    'word' => 'جَفَافٌ',
                    'clean' => 'جفاف',
                    'meaning' => 'انْحِبَاسُ الْمَطَرِ لِفَتَرَاتٍ طَوِيلَةٍ',
                    'image' => '/assets/lesson-images/N6/P5/SEM1/vocab/image39.png'
                ],
                [
                    'word' => 'تَحْلِيَةٌ',
                    'clean' => 'تحلية',
                    'meaning' => 'تَحْوِيلُ مِيَاهِ الْبَحْرِ الْمَالِحَةِ إِلَى مِيَاهٍ صَالِحَةٍ لِلشُّرْبِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM1/vocab/image40.png'
                ],
                [
                    'word' => 'مَحَاصِيلُ',
                    'clean' => 'محاصيل',
                    'meaning' => 'إِنْتَاجُ الْحُبُوبِ الْأَسَاسِيَّةِ لِقُوتِ الشُّعُوبِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM1/vocab/image38.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: السلام العالمي والتعايش وحوار الأديان',
                'lesson_sentence' => 'تَعْمَلُ الْمُنَظَّمَاتُ الدَّوْلِيَّةُ عَلَى إِحْلَالِ السَّلَامِ وَمَنْعِ كُلِّ النِّزَاعَاتِ الْمُسَلَّحَةِ.',
                'vocabulary' => [
                [
                    'word' => 'مِيثَاقٌ',
                    'clean' => 'ميثاق',
                    'meaning' => 'عَهْدٌ دَوْلِيٌّ يُلْزِمُ الدُّوَلَ بِاحْتِرَامِ السَّلَامِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM2/vocab/image40.png'
                ],
                [
                    'word' => 'سَلَامٌ',
                    'clean' => 'سلام',
                    'meaning' => 'أَمَانٌ وَطُمَأْنِينَةٌ وَانْعِدَامُ الْحُرُوبِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM2/vocab/image41.png'
                ],
                [
                    'word' => 'إِخَاءٌ',
                    'clean' => 'إخاء',
                    'meaning' => 'رَابِطَةُ الْمَوَدَّةِ وَالْمَصِيرِ الْمُشْتَرَكِ بَيْنَ كُلِّ الْبَشَرِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM2/vocab/image42.png'
                ],
                [
                    'word' => 'عَدْلٌ',
                    'clean' => 'عدل',
                    'meaning' => 'إِنْصَافٌ شَامِلٌ يَضْمَنُ حُقُوقَ الشُّعُوبِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM2/vocab/image40.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حماية التراث الطبيعي والثقافي المشترك',
                'lesson_sentence' => 'يَقَعُ عَلَى عَاتِقِ كُلِّ دُوَلِ الْعَالَمِ وَاجِبُ حِمَايَةِ الْإِرْثِ الْإِنْسَانِيِّ الْمُشْتَرَكِ.',
                'vocabulary' => [
                [
                    'word' => 'مَحْمِيَّةٌ',
                    'clean' => 'محمية',
                    'meaning' => 'فَضَاءٌ طَبِيعِيٌّ مَصُونٌ لِحِمَايَةِ الْحَيَاةِ الْفِطْرِيَّةِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM3/vocab/image39.png'
                ],
                [
                    'word' => 'حِفْظٌ',
                    'clean' => 'حفظ',
                    'meaning' => 'صَوْنُ الْمَعَالِمِ مِنَ التَّخْرِيبِ وَالْإِهْمَالِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM3/vocab/image40.png'
                ],
                [
                    'word' => 'تَوْثِيقٌ',
                    'clean' => 'توثيق',
                    'meaning' => 'تَسْجِيلُ التُّرَاثِ فِي مَوْسُوعَاتٍ دَوْلِيَّةٍ',
                    'image' => '/assets/lesson-images/N6/P5/SEM3/vocab/image41.png'
                ],
                [
                    'word' => 'إِرْثٌ',
                    'clean' => 'إرث',
                    'meaning' => 'مَا وَرِثْنَاهُ مِنْ مَجْدٍ حَضَارِيٍّ',
                    'image' => '/assets/lesson-images/N6/P5/SEM3/vocab/image39.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: تضامن الشعوب في الكوارث الطبيعية والأوبئة',
                'lesson_sentence' => 'يُبَرْهِنُ تَدَفُّقُ الْمُسَاعَدَاتِ الْإِنْسَانِيَّةِ فِي الْكِوَارِثِ عَلَى وَحْدَةِ الْمَصِيرِ الْبَشَرِيِّ.',
                'vocabulary' => [
                [
                    'word' => 'إِغَاثَةٌ',
                    'clean' => 'إغاثة',
                    'meaning' => 'تَقْدِيمُ الْمُسَاعَدَاتِ الْعَاجِلَةِ لِلْمَنْكُوبِينَ',
                    'image' => '/assets/lesson-images/N6/P5/SEM4/vocab/image19.jpg'
                ],
                [
                    'word' => 'تَكَاتُفٌ',
                    'clean' => 'تكاتف',
                    'meaning' => 'تَعَاوُنٌ وَثِيقٌ بَيْنَ حُكُومَاتِ وَشُعُوبِ الْأَرْضِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM4/vocab/image20.png'
                ],
                [
                    'word' => 'إِنْقَاذٌ',
                    'clean' => 'إنقاذ',
                    'meaning' => 'إِخْرَاجُ الْمُصَابِينَ مِنْ تَحْتِ الرُّكَامِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM4/vocab/image22.png'
                ],
                [
                    'word' => 'مُؤَازَرَةٌ',
                    'clean' => 'مؤازرة',
                    'meaning' => 'دَعْمٌ مَعْنَوِيٌّ وَمَادِّيٌّ لِتَجَاوُزِ الْمِحَنِ',
                    'image' => '/assets/lesson-images/N6/P5/SEM4/vocab/image19.jpg'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم الحصيلة الختامية للتعليم الابتدائي',
                'lesson_sentence' => 'يَا أَبْنَاءَ الْوَطَنِ، لَا سَبِيلَ لِلرِّفْعَةِ إِلَّا بِالْعِلْمِ وَالْأَخْلَاقِ وَالْعَمَلِ الْمُخْلِصِ.',
                'vocabulary' => [
                [
                    'word' => 'تَحْلِيَةٌ',
                    'clean' => 'تحلية',
                    'meaning' => 'حِمَايَةُ الْمِيَاهِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'مِيثَاقٌ',
                    'clean' => 'ميثاق',
                    'meaning' => 'عَهْدُ السَّلَامِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image76.jpg'
                ],
                [
                    'word' => 'إِرْثٌ',
                    'clean' => 'إرث',
                    'meaning' => 'أَمَانَةُ التَّارِيخِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image61.png'
                ],
                [
                    'word' => 'إِغَاثَةٌ',
                    'clean' => 'إغاثة',
                    'meaning' => 'نَجْدَةُ الْإِنْسَانِ',
                    'image' => '/assets/lesson-images/SEM5/vocab/image76.jpg'
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
