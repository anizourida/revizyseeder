<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN5P1VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N5, Period P1 (الوحدة 1: مغاربة العالم والإشعاع الحضاري).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: مغاربة العالم: النواسخ الفعلية والحرفية
            // ==========================================
            [
                'word' => 'اَلْمَهْجَرُ',
                'clean_word' => 'اَلْمَهْجَرُ',
                'raw_word' => 'المهجر',
                'example_sentence' => 'بِلاَدُ الإِقامَةِ وَالعَمَلِ خارِجَ الوَطَنِ الأُمِّ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM1_S1/image12.png',
                'linked_sentences' => [
                    'بِلاَدُ الإِقامَةِ وَالعَمَلِ خارِجَ الوَطَنِ الأُمِّ',
                ],
            ],
            [
                'word' => 'حَنينٌ',
                'clean_word' => 'حَنينٌ',
                'raw_word' => 'حنين',
                'example_sentence' => 'شَوْقٌ عَميقٌ إِلى مَسْقِطِ الرَّأْسِ وَالأَهْلِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM1_S1/image3.png',
                'linked_sentences' => [
                    'شَوْقٌ عَميقٌ إِلى مَسْقِطِ الرَّأْسِ وَالأَهْلِ',
                ],
            ],
            [
                'word' => 'كِفاءَةٌ',
                'clean_word' => 'كِفاءَةٌ',
                'raw_word' => 'كفاءة',
                'example_sentence' => 'قُدْرَةٌ وَخِبْرَةٌ عالِيَةٌ في مَجالِ التَّخَصُّصِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM1_S1/image45.png',
                'linked_sentences' => [
                    'قُدْرَةٌ وَخِبْرَةٌ عالِيَةٌ في مَجالِ التَّخَصُّصِ',
                ],
            ],
            [
                'word' => 'إِشْعاعٌ',
                'clean_word' => 'إِشْعاعٌ',
                'raw_word' => 'إشعاع',
                'example_sentence' => 'نَشْرُ المَعْرِفَةِ وَالقِيَمِ الإيجابِيَّةِ في العالَمِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM1_S1/image51.png',
                'linked_sentences' => [
                    'نَشْرُ المَعْرِفَةِ وَالقِيَمِ الإيجابِيَّةِ في العالَمِ',
                ],
            ],
            [
                'word' => 'انْدِماجٌ',
                'clean_word' => 'انْدِماجٌ',
                'raw_word' => 'اندماج',
                'example_sentence' => 'التَّوافُقُ مَعَ المُجْتَمَعِ الجَديدِ مَعَ حِفْظِ الهُوِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N5_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM1_S1/image52.png',
                'linked_sentences' => [
                    'التَّوافُقُ مَعَ المُجْتَمَعِ الجَديدِ مَعَ حِفْظِ الهُوِيَّةِ',
                ],
            ],
            // ==========================================
            // الأسبوع 2: الفعل المجرد والمزيد وميزانه
            // ==========================================
            [
                'word' => 'عِصامِيٌّ',
                'clean_word' => 'عِصامِيٌّ',
                'raw_word' => 'عصامي',
                'example_sentence' => 'مَن يَبْني نَجاحَهُ بِجُهْدِهِ وَكَدِّهِ الذّاتِيِّ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM2_S1/image80.png',
                'linked_sentences' => [
                    'مَن يَبْني نَجاحَهُ بِجُهْدِهِ وَكَدِّهِ الذّاتِيِّ',
                ],
            ],
            [
                'word' => 'عَبْقَرِيَّةٌ',
                'clean_word' => 'عَبْقَرِيَّةٌ',
                'raw_word' => 'عبقرية',
                'example_sentence' => 'تَمَيُّزٌ فِكْرِيٌّ وَذَكاءٌ خارِقٌ في الابْتِكارِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM2_S1/image81.png',
                'linked_sentences' => [
                    'تَمَيُّزٌ فِكْرِيٌّ وَذَكاءٌ خارِقٌ في الابْتِكارِ',
                ],
            ],
            [
                'word' => 'اخْتِراعٌ',
                'clean_word' => 'اخْتِراعٌ',
                'raw_word' => 'اختراع',
                'example_sentence' => 'ابْتِكارُ آلاتٍ وَحُلولٍ لَمْ تَكُنْ مَوْجودَةً',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM2_S1/image82.png',
                'linked_sentences' => [
                    'ابْتِكارُ آلاتٍ وَحُلولٍ لَمْ تَكُنْ مَوْجودَةً',
                ],
            ],
            [
                'word' => 'بَحْثٌ عِلْمِيٌّ',
                'clean_word' => 'بَحْثٌ عِلْمِيٌّ',
                'raw_word' => 'بحث علمي',
                'example_sentence' => 'اسْتِقْصاءٌ مَنْهَجِيٌّ لِكَشْفِ الحَقائِقِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N5_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM2_S1/image78.png',
                'linked_sentences' => [
                    'اسْتِقْصاءٌ مَنْهَجِيٌّ لِكَشْفِ الحَقائِقِ',
                ],
            ],
            // ==========================================
            // الأسبوع 3: المفعول به والمفعول المطلق
            // ==========================================
            [
                'word' => 'تَضامُنٌ',
                'clean_word' => 'تَضامُنٌ',
                'raw_word' => 'تضامن',
                'example_sentence' => 'التَّآزُرُ بَيْنَ أَبْناءِ الوَطَنِ في الدّاخِلِ وَالخارِجِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM3_S1/image72.png',
                'linked_sentences' => [
                    'التَّآزُرُ بَيْنَ أَبْناءِ الوَطَنِ في الدّاخِلِ وَالخارِجِ',
                ],
            ],
            [
                'word' => 'اسْتِثْمارٌ',
                'clean_word' => 'اسْتِثْمارٌ',
                'raw_word' => 'استثمار',
                'example_sentence' => 'تَوْظيفُ الخِبْراتِ وَالأَمْوالِ لِتَنْمِيَةِ البِلادِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM3_S1/image73.png',
                'linked_sentences' => [
                    'تَوْظيفُ الخِبْراتِ وَالأَمْوالِ لِتَنْمِيَةِ البِلادِ',
                ],
            ],
            [
                'word' => 'مُواطَنَةٌ',
                'clean_word' => 'مُواطَنَةٌ',
                'raw_word' => 'مواطنة',
                'example_sentence' => 'حُبُّ الوَطَنِ وَالإِخْلاصُ لَهُ قَوْلاً وَعَمَلاً',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM3_S1/image74.png',
                'linked_sentences' => [
                    'حُبُّ الوَطَنِ وَالإِخْلاصُ لَهُ قَوْلاً وَعَمَلاً',
                ],
            ],
            [
                'word' => 'هُوِيَّةٌ',
                'clean_word' => 'هُوِيَّةٌ',
                'raw_word' => 'هوية',
                'example_sentence' => 'الثَّوابِتُ الدّينِيَّةُ وَاللُّغَوِيَّةُ وَالثَّقافِيَّةُ المَغْرِبِيَّةُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N5_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM3_S1/image75.png',
                'linked_sentences' => [
                    'الثَّوابِتُ الدّينِيَّةُ وَاللُّغَوِيَّةُ وَالثَّقافِيَّةُ المَغْرِبِيَّةُ',
                ],
            ],
            // ==========================================
            // الأسبوع 4: الهمزة المتوسطة والمتطرفة
            // ==========================================
            [
                'word' => 'سَفيرٌ',
                'clean_word' => 'سَفيرٌ',
                'raw_word' => 'سفير',
                'example_sentence' => 'مُمَثِّلٌ لِبِلادِهِ يَنْشُرُ صورَتَها المُشَرِّفَةَ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM4_S1/image30.jpg',
                'linked_sentences' => [
                    'مُمَثِّلٌ لِبِلادِهِ يَنْشُرُ صورَتَها المُشَرِّفَةَ',
                ],
            ],
            [
                'word' => 'رِيادَةٌ',
                'clean_word' => 'رِيادَةٌ',
                'raw_word' => 'ريادة',
                'example_sentence' => 'التَّقَدُّمُ في الصَّفِّ الأَوَّلِ في المَجالاتِ العِلْمِيَّةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM4_S1/image14.png',
                'linked_sentences' => [
                    'التَّقَدُّمُ في الصَّفِّ الأَوَّلِ في المَجالاتِ العِلْمِيَّةِ',
                ],
            ],
            [
                'word' => 'جِسْرُ التَّواصُلِ',
                'clean_word' => 'جِسْرُ التَّواصُلِ',
                'raw_word' => 'جسر التواصل',
                'example_sentence' => 'رَبْطُ العَلاقاتِ الثَّقافِيَّةِ بَيْنَ الشُّعوبِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N5_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM4_S1/image33.png',
                'linked_sentences' => [
                    'رَبْطُ العَلاقاتِ الثَّقافِيَّةِ بَيْنَ الشُّعوبِ',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم سنوي للظواهر اللغوية
            // ==========================================
            [
                'word' => 'عائِلَةُ الكَلِمَةِ',
                'clean_word' => 'عائِلَةُ الكَلِمَةِ',
                'raw_word' => 'عائلة الكلمة',
                'example_sentence' => 'اشْتِقاقُ اسْمِ الفاعِلِ، المَفْعولِ، وَالمَصْدَرِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM5_S1/image39.png',
                'linked_sentences' => [
                    'اشْتِقاقُ اسْمِ الفاعِلِ، المَفْعولِ، وَالمَصْدَرِ',
                ],
            ],
            [
                'word' => 'تَضادٌّ وَتَرادُفٌ',
                'clean_word' => 'تَضادٌّ وَتَرادُفٌ',
                'raw_word' => 'تضاد وترادف',
                'example_sentence' => 'إِدْراكُ الفُروقِ الدَّقيقَةِ في مَعاني الأَلْفاظِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N5_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM5_S1/image41.png',
                'linked_sentences' => [
                    'إِدْراكُ الفُروقِ الدَّقيقَةِ في مَعاني الأَلْفاظِ',
                ],
            ],
            // ==========================================
            // الأسبوع 6: تقويم إجمالي لوحدة مغاربة العالم
            // ==========================================
            [
                'word' => 'تَلْخيصٌ',
                'clean_word' => 'تَلْخيصٌ',
                'raw_word' => 'تلخيص',
                'example_sentence' => 'اخْتِصارُ النَّصِّ مَعَ الحِفاظِ عَلى فِكْرَتِهِ الرَّئيسَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N5',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N5_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N5_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'اخْتِصارُ النَّصِّ مَعَ الحِفاظِ عَلى فِكْرَتِهِ الرَّئيسَةِ',
                ],
            ],
        ];

        $now = now();

        foreach ($items as $itemData) {
            $linkedSentences = $itemData['linked_sentences'] ?? [];
            unset($itemData['linked_sentences']);

            $itemData['extracted_at'] = $now;

            $vocab = ArabicVocabularyItem::query()->updateOrCreate(
                [
                    'word' => $itemData['word'],
                    'lesson_id' => $itemData['lesson_id'],
                    'grade' => $itemData['grade'],
                ],
                $itemData
            );

            // Record linked sentences into vocabulary_sentences
            foreach ($linkedSentences as $sentence) {
                DB::table('vocabulary_sentences')->updateOrInsert(
                    [
                        'word' => $itemData['word'],
                        'sentence' => $sentence,
                        'lesson_id' => $itemData['lesson_id'],
                        'grade' => $itemData['grade'],
                    ],
                    [
                        'vocabulary_item_id' => null,
                        'base_word' => $itemData['raw_word'],
                        'subject' => 'AR',
                        'period' => $itemData['period'],
                        'week' => $itemData['week'],
                        'source_type' => 'slide_sentence',
                        'image_path' => $itemData['image_path'] ?? null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
