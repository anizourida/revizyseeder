<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الثالثة ابتدائي (N3)
 * Unit: الوحدة 4: الحرف والمهن والصناعة التقليدية (P4)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN3P4VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الصانع التقليدي المغربي ونقش الجبس',
                'lesson_sentence' => 'يُبْدِعُ الصَّانِعُ التَّقْلِيدِيُّ فِي نَقْشِ الْجِبْسِ وَصُنْعِ الْخَزَفِ.',
                'vocabulary' => [
                [
                    'word' => 'خَزَفٌ',
                    'clean' => 'خزف',
                    'meaning' => 'طِينٌ مُجَفَّفٌ وَمَصْقُولٌ بِأَلْوَانٍ بَدِيعَةٍ',
                    'image' => '/assets/lesson-images/N3/P4/SEM1/vocab/image59.png'
                ],
                [
                    'word' => 'نَقْشٌ',
                    'clean' => 'نقش',
                    'meaning' => 'حَفْرُ زَخَارِفَ جَمِيلَةٍ عَلَى الْجِبْسِ أَوِ الْخَشَبِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM1/vocab/image62.png'
                ],
                [
                    'word' => 'جِبْسٌ',
                    'clean' => 'جبس',
                    'meaning' => 'مَادَّةٌ بَيْضَاءُ لِتَزْيِينِ الْأَسْقُفِ وَالْجُدْرَانِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM1/vocab/image63.png'
                ],
                [
                    'word' => 'أَصَالَةٌ',
                    'clean' => 'أصالة',
                    'meaning' => 'عَرَاقَةٌ وَتَمَسُّكٌ بِالتُّرَاثِ الْأَصِيلِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM1/vocab/image59.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: حياكة الزرابي المغربية العريقة',
                'lesson_sentence' => 'تَنْسِجُ الصَّانِعَةُ زَرْبِيَّةً مُزَرْكَشَةً بِأَلْوَانٍ بَهِيَّةٍ.',
                'vocabulary' => [
                [
                    'word' => 'نَوْلٌ',
                    'clean' => 'نول',
                    'meaning' => 'آلَةٌ خَشَبِيَّةٌ تَقْلِيدِيَّةٌ لِنَسْجِ الزَّرَابِي',
                    'image' => '/assets/lesson-images/N3/P4/SEM2/vocab/image43.png'
                ],
                [
                    'word' => 'صُوفٌ',
                    'clean' => 'صوف',
                    'meaning' => 'شَعْرُ الْخِرَافِ الْمَغْزُولُ لِلْحِيَاكَةِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM2/vocab/image44.png'
                ],
                [
                    'word' => 'أَلْوَانٌ',
                    'clean' => 'ألوان',
                    'meaning' => 'صِبَاغَاتٌ زَاهِيَةٌ تُزَيِّنُ النَّسِيجَ',
                    'image' => '/assets/lesson-images/N3/P4/SEM2/vocab/image45.png'
                ],
                [
                    'word' => 'مَهَارَةٌ',
                    'clean' => 'مهارة',
                    'meaning' => 'حِذْقٌ وَبَرَاعَةٌ فِي الْعَمَلِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM2/vocab/image43.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: الحدادة والنجارة والصناعات المعدنية',
                'lesson_sentence' => 'يَطْرُقُ الْحَدَّادُ الْحَدِيدَ لِيَصْنَعَ أَبْوَاباً مُزَخْرَفَةً وَقَوِيَّةً.',
                'vocabulary' => [
                [
                    'word' => 'مِطْرَقَةٌ',
                    'clean' => 'مطرقة',
                    'meaning' => 'أَدَاةُ طَرْقِ الْحَدِيدِ وَتَشْكِيلِهِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM3/vocab/image51.png'
                ],
                [
                    'word' => 'سِنْدَانٌ',
                    'clean' => 'سندان',
                    'meaning' => 'قِطْعَةُ حَدِيدٍ ثَقِيلَةٌ يُطْرَقُ عَلَيْهَا الْمَعْدِنُ',
                    'image' => '/assets/lesson-images/N3/P4/SEM3/vocab/image52.png'
                ],
                [
                    'word' => 'خَشَبٌ',
                    'clean' => 'خشب',
                    'meaning' => 'مَادَّةٌ طَبِيعِيَّةٌ مِنَ الْأَشْجَارِ لِلصِّنَاعَةِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM3/vocab/image53.png'
                ],
                [
                    'word' => 'إِتْقَانٌ',
                    'clean' => 'إتقان',
                    'meaning' => 'إِحْكَامُ الصُّنْعِ وَتَجْوِيدُهُ',
                    'image' => '/assets/lesson-images/N3/P4/SEM3/vocab/image51.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: الأسواق العتيقة وتجارة المنتجات المحلية',
                'lesson_sentence' => 'يَمْتَلِئُ السُّوقُ الْعَتِيقُ بِأَنْفَسِ الْمَصْنُوعَاتِ التَّقْلِيدِيَّةِ الْمَغْرِبِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'دُكَّانٌ',
                    'clean' => 'دكان',
                    'meaning' => 'مَحَلٌّ لِبَيْعِ الْبَضَائِعِ وَالْمَصْنُوعَاتِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM4/vocab/image32.png'
                ],
                [
                    'word' => 'بَضَائِعُ',
                    'clean' => 'بضائع',
                    'meaning' => 'سِلَعٌ مُتَنَوِّعَةٌ مَعْرُوضَةٌ لِلْبَيْعِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM4/vocab/image33.png'
                ],
                [
                    'word' => 'زَبُونٌ',
                    'clean' => 'زبون',
                    'meaning' => 'مُشْتَرٍ يَقْصِدُ السُّوقَ لِلتَّسَوُّقِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM4/vocab/image34.png'
                ],
                [
                    'word' => 'تَسَوُّقٌ',
                    'clean' => 'تسوق',
                    'meaning' => 'شِرَاءُ الْحَاجِيَاتِ مِنَ الْمَتَاجِرِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM4/vocab/image32.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 4',
                'lesson_sentence' => 'أَبْدَعَ الصُّنَّاعُ فِي تَقْدِيمِ أَرْوَعِ الْمَصْنُوعَاتِ التُّرَاثِيَّةِ.',
                'vocabulary' => [
                [
                    'word' => 'خَزَفٌ',
                    'clean' => 'خزف',
                    'meaning' => 'فَخَّارٌ مَصْقُولٌ',
                    'image' => '/assets/lesson-images/N3/P4/SEM5/vocab/image26.jpg'
                ],
                [
                    'word' => 'نَوْلٌ',
                    'clean' => 'نول',
                    'meaning' => 'آلَةُ النَّسِيجِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM5/vocab/image28.png'
                ],
                [
                    'word' => 'مِطْرَقَةٌ',
                    'clean' => 'مطرقة',
                    'meaning' => 'أَدَاةُ الْحَدَّادِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM5/vocab/image29.png'
                ],
                [
                    'word' => 'دُكَّانٌ',
                    'clean' => 'دكان',
                    'meaning' => 'مَحَلُّ التِّجَارَةِ',
                    'image' => '/assets/lesson-images/N3/P4/SEM5/vocab/image26.jpg'
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
