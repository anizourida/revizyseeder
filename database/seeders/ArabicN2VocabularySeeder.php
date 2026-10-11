<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN2VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N2, Period P1 (المنهاج الوطني المغربي / مدارس الريادة).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: س / ص (السين والصاد)
            // ==========================================
            [
                'word' => 'تَعاوَنَ',
                'clean_word' => 'تَعاوَنَ',
                'raw_word' => 'تعاون',
                'example_sentence' => 'تَعاوَنَ الأَصْدِقاءُ عَلى حَمْلِ الكُتُبِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM1_S1/image66.jpeg',
                'linked_sentences' => [
                    'تَعاوَنَ الأَصْدِقاءُ عَلى حَمْلِ الكُتُبِ',
                ],
            ],
            [
                'word' => 'رافَقَ',
                'clean_word' => 'رافَقَ',
                'raw_word' => 'رافق',
                'example_sentence' => 'رافَقَ الأَبُ ابْنَهُ إِلى المَدْرَسَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM1_S1/image69.png',
                'linked_sentences' => [
                    'رافَقَ الأَبُ ابْنَهُ إِلى المَدْرَسَةِ',
                ],
            ],
            [
                'word' => 'مُتَحَمِّسَةٌ',
                'clean_word' => 'مُتَحَمِّسَةٌ',
                'raw_word' => 'متحمسة',
                'example_sentence' => 'فَتاةٌ مَليئَةٌ بِالنَّشاطِ وَالفَرَحِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM1_S1/image70.png',
                'linked_sentences' => [
                    'فَتاةٌ مَليئَةٌ بِالنَّشاطِ وَالفَرَحِ',
                ],
            ],
            [
                'word' => 'أَجَّلَ',
                'clean_word' => 'أَجَّلَ',
                'raw_word' => 'أجل',
                'example_sentence' => 'أَجَّلَ عَمَلَ اليَوْمِ إِلى الغَدِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM1_S1/image71.jpeg',
                'linked_sentences' => [
                    'أَجَّلَ عَمَلَ اليَوْمِ إِلى الغَدِ',
                ],
            ],
            [
                'word' => 'مُغامَرَةٌ',
                'clean_word' => 'مُغامَرَةٌ',
                'raw_word' => 'مغامرة',
                'example_sentence' => 'تَجْرِبَةٌ جَديدَةٌ وَمُثيرَةٌ في عالَمِ الحَيَوانِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM1_S1/image74.png',
                'linked_sentences' => [
                    'تَجْرِبَةٌ جَديدَةٌ وَمُثيرَةٌ في عالَمِ الحَيَوانِ',
                ],
            ],
            [
                'word' => 'مُتَشَوِّقَةٌ',
                'clean_word' => 'مُتَشَوِّقَةٌ',
                'raw_word' => 'متشوقة',
                'example_sentence' => 'مُتَلَهِّفَةٌ لِرُؤْيَةِ أَصْدِقائِها الجُدُدِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N2_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM1_S1/image75.jpeg',
                'linked_sentences' => [
                    'مُتَلَهِّفَةٌ لِرُؤْيَةِ أَصْدِقائِها الجُدُدِ',
                ],
            ],
            // ==========================================
            // الأسبوع 2: ذ / ز (الذال والزاي)
            // ==========================================
            [
                'word' => 'اِسْتَقْبَلَ',
                'clean_word' => 'اِسْتَقْبَلَ',
                'raw_word' => 'استقبل',
                'example_sentence' => 'اِسْتَقْبَلَ المُديرُ التَّلاميذَ بِتَرْحابٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM2_S1/image63.jpg',
                'linked_sentences' => [
                    'اِسْتَقْبَلَ المُديرُ التَّلاميذَ بِتَرْحابٍ',
                ],
            ],
            [
                'word' => 'رَحَّبَ',
                'clean_word' => 'رَحَّبَ',
                'raw_word' => 'رحب',
                'example_sentence' => 'رَحَّبَ الجَدُّ بِالضُّيوفِ في بَيْتِهِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM2_S1/image65.jpg',
                'linked_sentences' => [
                    'رَحَّبَ الجَدُّ بِالضُّيوفِ في بَيْتِهِ',
                ],
            ],
            [
                'word' => 'ضِيافَةٌ',
                'clean_word' => 'ضِيافَةٌ',
                'raw_word' => 'ضيافة',
                'example_sentence' => 'كَرَمُ اسْتِقْبالِ الزّائِرينَ وَإِكْرامُهُمْ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM2_S1/image63.jpg',
                'linked_sentences' => [
                    'كَرَمُ اسْتِقْبالِ الزّائِرينَ وَإِكْرامُهُمْ',
                ],
            ],
            [
                'word' => 'بَشوشٌ',
                'clean_word' => 'بَشوشٌ',
                'raw_word' => 'بشوش',
                'example_sentence' => 'وَجْهٌ باسِمٌ يَبْعَثُ عَلى الاطْمِئْنانِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM2_S1/image70.png',
                'linked_sentences' => [
                    'وَجْهٌ باسِمٌ يَبْعَثُ عَلى الاطْمِئْنانِ',
                ],
            ],
            [
                'word' => 'مَأْدُبَةٌ',
                'clean_word' => 'مَأْدُبَةٌ',
                'raw_word' => 'مأدبة',
                'example_sentence' => 'وَلِيمَةُ طَعامٍ شَهِيٍّ يُدْعى إِلَيْها الأَحِبَّةُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N2_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM2_S1/image71.jpg',
                'linked_sentences' => [
                    'وَلِيمَةُ طَعامٍ شَهِيٍّ يُدْعى إِلَيْها الأَحِبَّةُ',
                ],
            ],
            // ==========================================
            // الأسبوع 3: ط / ظ (الطاء والظاء)
            // ==========================================
            [
                'word' => 'فَواكِهُ جافَّةٌ',
                'clean_word' => 'فَواكِهُ جافَّةٌ',
                'raw_word' => 'فواكه جافة',
                'example_sentence' => 'صَحْنٌ مَليءٌ بِاللَّوْزِ وَالجَوْزِ وَالتَّمْرِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM3_S1/image67.jpg',
                'linked_sentences' => [
                    'صَحْنٌ مَليءٌ بِاللَّوْزِ وَالجَوْزِ وَالتَّمْرِ',
                ],
            ],
            [
                'word' => 'تَراشَقَ',
                'clean_word' => 'تَراشَقَ',
                'raw_word' => 'تراشق',
                'example_sentence' => 'تَراشَقَ الأَطْفالُ بِالماءِ فَرَحاً',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM3_S1/image68.jpg',
                'linked_sentences' => [
                    'تَراشَقَ الأَطْفالُ بِالماءِ فَرَحاً',
                ],
            ],
            [
                'word' => 'تَوَسَّلَ',
                'clean_word' => 'تَوَسَّلَ',
                'raw_word' => 'توسل',
                'example_sentence' => 'طَلَبَ المَعونَةَ بِرَجاءٍ وَلُطْفٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM3_S1/image69.jpg',
                'linked_sentences' => [
                    'طَلَبَ المَعونَةَ بِرَجاءٍ وَلُطْفٍ',
                ],
            ],
            [
                'word' => 'شَهِيَّةٌ',
                'clean_word' => 'شَهِيَّةٌ',
                'raw_word' => 'شهية',
                'example_sentence' => 'أَطْعِمَةٌ لَذيذَةٌ تُحِبُّها العائِلَةُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM3_S1/image71.jpg',
                'linked_sentences' => [
                    'أَطْعِمَةٌ لَذيذَةٌ تُحِبُّها العائِلَةُ',
                ],
            ],
            [
                'word' => 'مِظَلَّةٌ',
                'clean_word' => 'مِظَلَّةٌ',
                'raw_word' => 'مظلة',
                'example_sentence' => 'مِظَلَّةٌ تَقِي مِنَ المَطَرِ وَالشَّمْسِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N2_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM3_S1/image72.jpg',
                'linked_sentences' => [
                    'مِظَلَّةٌ تَقِي مِنَ المَطَرِ وَالشَّمْسِ',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ت / ث (التاء والثاء)
            // ==========================================
            [
                'word' => 'تَحْمي',
                'clean_word' => 'تَحْمي',
                'raw_word' => 'تحمي',
                'example_sentence' => 'تَحْمي الأُمُّ صِغارَها مِنَ الأَخْطارِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM4_S1/image62.png',
                'linked_sentences' => [
                    'تَحْمي الأُمُّ صِغارَها مِنَ الأَخْطارِ',
                ],
            ],
            [
                'word' => 'يُقَلِّدُ',
                'clean_word' => 'يُقَلِّدُ',
                'raw_word' => 'يقلد',
                'example_sentence' => 'يُقَلِّدُ البَهْلَوانُ حَرَكاتِ القِرْدِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM4_S1/image64.png',
                'linked_sentences' => [
                    'يُقَلِّدُ البَهْلَوانُ حَرَكاتِ القِرْدِ',
                ],
            ],
            [
                'word' => 'اَلْمُتَهَوِّرُ',
                'clean_word' => 'اَلْمُتَهَوِّرُ',
                'raw_word' => 'المتهور',
                'example_sentence' => 'عَبَرَ الطِّفْلُ المُتَهَوِّرُ الشّارِعَ دونَ انْتِباهٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM4_S1/image65.png',
                'linked_sentences' => [
                    'عَبَرَ الطِّفْلُ المُتَهَوِّرُ الشّارِعَ دونَ انْتِباهٍ',
                ],
            ],
            [
                'word' => 'أَنْقَذَ',
                'clean_word' => 'أَنْقَذَ',
                'raw_word' => 'أنقذ',
                'example_sentence' => 'أَنْقَذَ الإِطْفائِيُّ الطِّفْلَ مِنَ الحَريقِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM4_S1/image66.png',
                'linked_sentences' => [
                    'أَنْقَذَ الإِطْفائِيُّ الطِّفْلَ مِنَ الحَريقِ',
                ],
            ],
            [
                'word' => 'اَلْجِسْمُ النَّحيلُ',
                'clean_word' => 'اَلْجِسْمُ النَّحيلُ',
                'raw_word' => 'الجسم النحيل',
                'example_sentence' => 'جِسْمٌ رَقيقٌ خَفيفُ الوَزْنِ وَالحَرَكَةِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N2_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM4_S1/image67.png',
                'linked_sentences' => [
                    'جِسْمٌ رَقيقٌ خَفيفُ الوَزْنِ وَالحَرَكَةِ',
                ],
            ],
            // ==========================================
            // الأسبوع 5: دعم وتقويم المقاطع
            // ==========================================
            [
                'word' => 'حَفْلَةٌ',
                'clean_word' => 'حَفْلَةٌ',
                'raw_word' => 'حفلة',
                'example_sentence' => 'مُناسَبَةٌ سَعيدَةٌ يَجْتَمِعُ فيها الأَصْدِقاءُ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM5_S1/image56.png',
                'linked_sentences' => [
                    'مُناسَبَةٌ سَعيدَةٌ يَجْتَمِعُ فيها الأَصْدِقاءُ',
                ],
            ],
            [
                'word' => 'مُساعَدَةٌ',
                'clean_word' => 'مُساعَدَةٌ',
                'raw_word' => 'مساعدة',
                'example_sentence' => 'مَدُّ يَدِ العَوْنِ لِلْمُحْتاجِ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM5_S1/image56.png',
                'linked_sentences' => [
                    'مَدُّ يَدِ العَوْنِ لِلْمُحْتاجِ',
                ],
            ],
            [
                'word' => 'أَجْمَلُ',
                'clean_word' => 'أَجْمَلُ',
                'raw_word' => 'أجمل',
                'example_sentence' => 'أَجْمَلُ لَوْحَةٍ رَسَمَها مَجْدٌ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N2_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM5_S1/image56.png',
                'linked_sentences' => [
                    'أَجْمَلُ لَوْحَةٍ رَسَمَها مَجْدٌ',
                ],
            ],
            // ==========================================
            // الأسبوع 6: روائز الطلاقة والتحدث
            // ==========================================
            [
                'word' => 'طَلاقَةٌ',
                'clean_word' => 'طَلاقَةٌ',
                'raw_word' => 'طلاقة',
                'example_sentence' => 'القِراءَةُ بِسُرْعَةٍ وَدِقَّةٍ وَتَعْبيرٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'القِراءَةُ بِسُرْعَةٍ وَدِقَّةٍ وَتَعْبيرٍ',
                ],
            ],
            [
                'word' => 'تَحَدُّثٌ',
                'clean_word' => 'تَحَدُّثٌ',
                'raw_word' => 'تحدث',
                'example_sentence' => 'التَّواصُلُ الشَّفَهِيُّ بِجُمَلٍ تامَّةٍ',
                'strategy' => 'معجم مصور',
                'grade' => 'N2',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N2_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N2_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'التَّواصُلُ الشَّفَهِيُّ بِجُمَلٍ تامَّةٍ',
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
