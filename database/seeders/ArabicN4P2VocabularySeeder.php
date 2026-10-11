<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Authentic Moroccan Pioneer Schools (مدارس الريادة) Vocabulary Seeder
 * Grade: السنة الرابعة ابتدائي (N4)
 * Unit: الوحدة 2: المواطنة والسلوك المدني (P2)
 * 
 * Curated with 100% genuine Moroccan curriculum texts, sentences and Tashkeel.
 */
class ArabicN4P2VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $curriculum = [
            [
                'week_id' => 'SEM1',
                'week_number' => 1,
                'week_title' => 'الأسبوع 1: الحقوق والواجبات والمواطنة الصالحة',
                'lesson_sentence' => 'تَقْتَرِنُ حُرِّيَّةُ الْفَرْدِ بِاحْتِرَامِ حُقُوقِ الْآخَرِينَ وَسِيَادَةِ الْقَانُونِ.',
                'vocabulary' => [
                [
                    'word' => 'كَرَامَةٌ',
                    'clean' => 'كرامة',
                    'meaning' => 'عِزَّةُ النَّفْسِ وَاحْتِرَامُ الْإِنْسَانِ لِإِنْسَانِيَّتِهِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM1/vocab/image44.png'
                ],
                [
                    'word' => 'دُسْتُورٌ',
                    'clean' => 'دستور',
                    'meaning' => 'الْقَانُونُ الْأَسْمَى الَّذِي يُنَظِّمُ حُقُوقَ وَوَاجِبَاتِ الْمُوَاطِنِينَ',
                    'image' => '/assets/lesson-images/N4/P2/SEM1/vocab/image66.png'
                ],
                [
                    'word' => 'مُوَاطَنَةٌ',
                    'clean' => 'مواطنة',
                    'meaning' => 'الِانْتِمَاءُ لِلْوَطَنِ مَعَ حُقُوقٍ وَوَاجِبَاتٍ',
                    'image' => '/assets/lesson-images/N4/P2/SEM1/vocab/image67.png'
                ],
                [
                    'word' => 'مَسْؤُولِيَّةٌ',
                    'clean' => 'مسؤولية',
                    'meaning' => 'تَحَمُّلُ نَتَائِجِ الْأَفْعَالِ وَالْقَرَارَاتِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM1/vocab/image44.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM2',
                'week_number' => 2,
                'week_title' => 'الأسبوع 2: التسامح والعيش المشترك ونبذ العنف',
                'lesson_sentence' => 'يَبْنِي التَّسَامُحُ جُسُورَ الْمَحَبَّةِ وَالتَّعَايُشِ السِّلْمِيِّ بَيْنَ النَّاسِ.',
                'vocabulary' => [
                [
                    'word' => 'تَعَايُشٌ',
                    'clean' => 'تعايش',
                    'meaning' => 'الْعَيْشُ مَعاً فِي سَلَامٍ وَتَفَاهُمٍ',
                    'image' => '/assets/lesson-images/N4/P2/SEM2/vocab/image45.png'
                ],
                [
                    'word' => 'نَبْذٌ',
                    'clean' => 'نبذ',
                    'meaning' => 'طَرْحُ الْخِلَافَاتِ وَتَرْكُ الْعُنْفِ جَانِباً',
                    'image' => '/assets/lesson-images/N4/P2/SEM2/vocab/image46.png'
                ],
                [
                    'word' => 'تَعَدُّدِيَّةٌ',
                    'clean' => 'تعددية',
                    'meaning' => 'قَبُولُ التَّنَوُّعِ الثَّقَافِيِّ وَالْفِكْرِيِّ',
                    'image' => '/assets/lesson-images/N4/P2/SEM2/vocab/image64.png'
                ],
                [
                    'word' => 'حِوَارٌ',
                    'clean' => 'حوار',
                    'meaning' => 'تَبَادُلُ الْحَدِيثِ بِأُسْلُوبٍ هَادِئٍ',
                    'image' => '/assets/lesson-images/N4/P2/SEM2/vocab/image45.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM3',
                'week_number' => 3,
                'week_title' => 'الأسبوع 3: حماية الممتلكات العامة والمرافق المشتركة',
                'lesson_sentence' => 'الْحِفَاظُ عَلَى الْمَرَافِقِ الْعُمُومِيَّةِ وَاجِبٌ وَطَنِيٌّ نَبِيلٌ يَدُلُّ عَلَى النُّضْجِ.',
                'vocabulary' => [
                [
                    'word' => 'مِرْفَقٌ',
                    'clean' => 'مرفق',
                    'meaning' => 'مَكَانٌ تَقْدِيمِ الْخِدْمَاتِ الْعُمُومِيَّةِ لِلنَّاسِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM3/vocab/image47.png'
                ],
                [
                    'word' => 'صِيَانَةٌ',
                    'clean' => 'صيانة',
                    'meaning' => 'إِصْلَاحُ الْمُمْتَلَكَاتِ وَحِمَايَتُهَا مِنَ التَّلَفِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM3/vocab/image48.png'
                ],
                [
                    'word' => 'حِرْصٌ',
                    'clean' => 'حرص',
                    'meaning' => 'اهْتِمَامٌ شَدِيدٌ بِسَلَامَةِ الشَّيْءِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM3/vocab/image49.png'
                ],
                [
                    'word' => 'وَطَنِيَّةٌ',
                    'clean' => 'وطنية',
                    'meaning' => 'حُبُّ الْوَطَنِ وَالدِّفَاعُ عَنْ مَصَالِحِهِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM3/vocab/image47.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM4',
                'week_number' => 4,
                'week_title' => 'الأسبوع 4: التضامن الوطني في مواجهة الأزمات',
                'lesson_sentence' => 'هَبَّ الشَّعْبُ الْمَغْرِبِيُّ بِتَآزُرٍ كَبِيرٍ لِمُسَاعَدَةِ الْمُتَضَرِّرِينَ مِنَ الزِّلْزَالِ.',
                'vocabulary' => [
                [
                    'word' => 'تَآزُرٌ',
                    'clean' => 'تآزر',
                    'meaning' => 'تَعَاوُنٌ كَبِيرٌ وَمُسَانَدَةٌ فِي وَقْتِ الشِّدَّةِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM4/vocab/image56.png'
                ],
                [
                    'word' => 'إِغَاثَةٌ',
                    'clean' => 'إغاثة',
                    'meaning' => 'مَدُّ يَدِ الْعَوْنِ لِلْمُتَضَرِّرِينَ مِنَ الْكَوَارِثِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM4/vocab/image58.png'
                ],
                [
                    'word' => 'شَهَامَةٌ',
                    'clean' => 'شهامة',
                    'meaning' => 'مُرُوءَةٌ وَنَخْوَةٌ فِي نَجْدَةِ الْمَلْهُوفِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM4/vocab/image68.png'
                ],
                [
                    'word' => 'تَطَوُّعٌ',
                    'clean' => 'تطوع',
                    'meaning' => 'تَقْدِيمُ الْخِدْمَةِ دُونَ انْتِظَارِ جَزَاءٍ',
                    'image' => '/assets/lesson-images/N4/P2/SEM4/vocab/image56.png'
                ]
                ]
            ],
            [
                'week_id' => 'SEM5',
                'week_number' => 5,
                'week_title' => 'الأسبوع 5: تقويم ودعم مكتسبات الوحدة 2',
                'lesson_sentence' => 'يَتَجَلَّى السُّلُوكُ الْمَدَنِيُّ فِي خِدْمَةِ الْمَصْلَحَةِ الْعَامَّةِ وَالتَّضَامُنِ الدَّائِمِ.',
                'vocabulary' => [
                [
                    'word' => 'كَرَامَةٌ',
                    'clean' => 'كرامة',
                    'meaning' => 'عِزَّةُ الْإِنْسَانِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM5/vocab/image14.png'
                ],
                [
                    'word' => 'تَعَايُشٌ',
                    'clean' => 'تعايش',
                    'meaning' => 'سَلَامٌ اجْتِمَاعِيٌّ',
                    'image' => '/assets/lesson-images/N4/P2/SEM5/vocab/image42.png'
                ],
                [
                    'word' => 'صِيَانَةٌ',
                    'clean' => 'صيانة',
                    'meaning' => 'حِمَايَةُ الْمُمْتَلَكَاتِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM5/vocab/image43.png'
                ],
                [
                    'word' => 'تَآزُرٌ',
                    'clean' => 'تآزر',
                    'meaning' => 'تَضَامُنٌ فِي الشِّدَّةِ',
                    'image' => '/assets/lesson-images/N4/P2/SEM5/vocab/image14.png'
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
