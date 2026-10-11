<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثالثة ابتدائي (N3)
 * Unit: الوحدة 2: الحياة التعاونية والعمل التطوعي (P2)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN3P2VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: النواسخ الفعلية وزيارة المآثر التاريخية',
                'lesson_sentence' => 'أَسْمَعُ صَخَبَ السُّيَّاحِ وَهُمْ يَزُورُونَ الْمَآثِرَ الْمَهِيبَةَ.',
                'vocabulary' => [
                [
                    'word' => 'مَآثِرُ',
                    'clean' => 'مآثر',
                    'meaning' => 'مَبَانٍ تَارِيخِيَّةٌ خَالِدَةٌ تَشْهَدُ عَلَى الْحَضَارَةِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM1/vocab/image46.png'
                ],
                [
                    'word' => 'حَفَاوَةٌ',
                    'clean' => 'حفاوة',
                    'meaning' => 'كَرَمُ الضِّيَافَةِ وَالتَّرْحِيبُ الْحَارُّ',
                    'image' => '/assets/lesson-images/N3/P2/SEM1/vocab/image47.png'
                ],
                [
                    'word' => 'صَخَبٌ',
                    'clean' => 'صخب',
                    'meaning' => 'أَصْوَاتٌ كَثِيرَةٌ مُخْتَلِطَةٌ تَمْلَأُ الْمَكَانَ',
                    'image' => '/assets/lesson-images/N3/P2/SEM1/vocab/image48.png'
                ],
                [
                    'word' => 'مَهِيبٌ',
                    'clean' => 'مهيب',
                    'meaning' => 'عَظِيمُ الشَّأْنِ يَبْعَثُ عَلَى الِاحْتِرَامِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM1/vocab/image46.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: تنظيف الحي وتزيين الجدران',
                'lesson_sentence' => 'تَعَاوَنَ سُكَّانُ الْحَيِّ عَلَى غَرْسِ الشُّجَيْرَاتِ وَطِلَاءِ الْجُدْرَانِ.',
                'vocabulary' => [
                [
                    'word' => 'تَطَوُّعٌ',
                    'clean' => 'تطوع',
                    'meaning' => 'عَمَلٌ خَيْرِيٌّ يَقُومُ بِهِ الْإِنْسَانُ دُونَ مُقَابِلٍ',
                    'image' => '/assets/lesson-images/N3/P2/SEM2/vocab/image44.png'
                ],
                [
                    'word' => 'غَرْسٌ',
                    'clean' => 'غرس',
                    'meaning' => 'وَضْعُ الشُّجَيْرَاتِ فِي التُّرْبَةِ لِتَنْمُوَ',
                    'image' => '/assets/lesson-images/N3/P2/SEM2/vocab/image45.png'
                ],
                [
                    'word' => 'طِلَاءٌ',
                    'clean' => 'طلاء',
                    'meaning' => 'صِبَاغَةُ الْجُدْرَانِ بِأَلْوَانٍ بَهِيجَةٍ',
                    'image' => '/assets/lesson-images/N3/P2/SEM2/vocab/image46.png'
                ],
                [
                    'word' => 'بَهْجَةٌ',
                    'clean' => 'بهجة',
                    'meaning' => 'فَرَحٌ شَدِيدٌ يَمْلَأُ الْقُلُوبَ',
                    'image' => '/assets/lesson-images/N3/P2/SEM2/vocab/image44.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: التضامن في القرية وحملات الإحسان',
                'lesson_sentence' => 'شَارَكَ الْأَهَالِي فِي حَمْلَةِ التَّبَرُّعِ بِالْكُتُبِ لِأَبْنَاءِ الْقَرْيَةِ.',
                'vocabulary' => [
                [
                    'word' => 'حَمْلَةٌ',
                    'clean' => 'حملة',
                    'meaning' => 'نَشَاطٌ جَمَاعِيٌّ مُنَظَّمٌ لِمُسَاعَدَةِ الْمُحْتَاجِينَ',
                    'image' => '/assets/lesson-images/N3/P2/SEM3/vocab/image27.png'
                ],
                [
                    'word' => 'تَبَرُّعٌ',
                    'clean' => 'تبرع',
                    'meaning' => 'إِعْطَاءُ الْمَالِ أَوِ الثِّيَابِ عَنْ طِيبِ خَاطِرٍ',
                    'image' => '/assets/lesson-images/N3/P2/SEM3/vocab/image45.png'
                ],
                [
                    'word' => 'مَحَبَّةٌ',
                    'clean' => 'محبة',
                    'meaning' => 'شُعُورٌ صَادِقٌ بِالْوُدِّ وَالتَّآخِي',
                    'image' => '/assets/lesson-images/N3/P2/SEM3/vocab/image46.png'
                ],
                [
                    'word' => 'إِخَاءٌ',
                    'clean' => 'إخاء',
                    'meaning' => 'رَابِطَةُ الْمَوَدَّةِ الصَّادِقَةِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM3/vocab/image27.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: ورشة التعاون المدرسي وحديقة القسم',
                'lesson_sentence' => 'أَصْبَحَتْ حَدِيقَةُ الْمَدْرَسَةِ خَضْرَاءَ بِفَضْلِ عَمَلِ التَّلَامِيذِ الْمُتْقَنِ.',
                'vocabulary' => [
                [
                    'word' => 'شَتْلَةٌ',
                    'clean' => 'شتلة',
                    'meaning' => 'نَبْتَةٌ صَغِيرَةٌ تُغْرَسُ فِي الْأَرْضِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM4/vocab/image45.png'
                ],
                [
                    'word' => 'مَشْرُوعٌ',
                    'clean' => 'مشروع',
                    'meaning' => 'خُطَّةُ عَمَلٍ تَعَاوُنِيَّةٌ لِتَحْقِيقِ هَدَفٍ',
                    'image' => '/assets/lesson-images/N3/P2/SEM4/vocab/image46.png'
                ],
                [
                    'word' => 'إِتْقَانٌ',
                    'clean' => 'إتقان',
                    'meaning' => 'إِنْجَازُ الْعَمَلِ بِدِقَّةٍ وَمَهَارَةٍ عَالِيَةٍ',
                    'image' => '/assets/lesson-images/N3/P2/SEM4/vocab/image52.png'
                ],
                [
                    'word' => 'نَجَاحٌ',
                    'clean' => 'نجاح',
                    'meaning' => 'الْفَوْزُ بِالنَّتِيجَةِ الْمَرْجُوَّةِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM4/vocab/image45.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 2',
                'lesson_sentence' => 'كَانَ التَّعَاوُنُ سَبَباً فِي نَجَاحِ كُلِّ الْأَنْشِطَةِ الْمَدْرَسِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'مَآثِرُ',
                    'clean' => 'مآثر',
                    'meaning' => 'مَعَالِمُ الْحَضَارَةِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM5/vocab/image45.png'
                ],
                [
                    'word' => 'تَطَوُّعٌ',
                    'clean' => 'تطوع',
                    'meaning' => 'بَذْلُ الْعَطَاءِ',
                    'image' => '/assets/lesson-images/N3/P2/SEM5/vocab/image46.png'
                ],
                [
                    'word' => 'حَمْلَةٌ',
                    'clean' => 'حملة',
                    'meaning' => 'عَمَلٌ خَيْرِيٌّ',
                    'image' => '/assets/lesson-images/N3/P2/SEM5/vocab/image47.jpeg'
                ],
                [
                    'word' => 'شَتْلَةٌ',
                    'clean' => 'شتلة',
                    'meaning' => 'نَبْتَةٌ صَغِيرَةٌ',
                    'image' => '/assets/lesson-images/N3/P2/SEM5/vocab/image45.png'
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
