<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN4VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N4, Period P1, Week SEM1.
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الحصة 1 (S1): معجم الحكاية «دَرْسُ ٱلْمُثابَرَةِ» واستراتيجية شبكة الكلمة
            // ==========================================
            [
                'word' => 'مُثابَرَةٌ',
                'clean_word' => 'مُثابَرَةٌ',
                'raw_word' => 'مثابرة',
                'root' => 'ث-ب-ر',
                'example_sentence' => 'مُثابَرَةٌ: اَلاِسْتِمْرارُ بِجِدٍّ رَغْمَ ٱلتَّحَدِّياتِ.',
                'strategy' => 'المعجم المساعد',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'slide_index' => 33,
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image81.jpeg',
                'linked_sentences' => [
                    'مُثابَرَةٌ : اَلاِسْتِمْرارُ بِجِدٍّ رَغْمَ ٱلتَّحَدِّياتِ.',
                    'كانَتْ لَيْلى تَتَمَسَّكُ بِقِيَمِ ٱلْإِصْرارِ وَٱلْمُثابَرَةِ.',
                    'عَلَّمَها ٱلْعُصْفورُ ٱلصَّغيرُ دَرْساً مُهِمّاً في ٱلْمُثابَرَةِ.',
                ],
            ],
            [
                'word' => 'تَفَوُّقٌ',
                'clean_word' => 'تَفَوُّقٌ',
                'raw_word' => 'تفوق',
                'root' => 'ف-و-ق',
                'example_sentence' => 'تَفَوُّقٌ: اَلنَّجاحُ بِشَكْلٍ مُمَيَّزٍ.',
                'strategy' => 'المعجم المساعد',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'slide_index' => 33,
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S1/image83.png',
                'linked_sentences' => [
                    'تَفَوُّقٌ : اَلنَّجاحُ بِشَكْلٍ مُمَيَّزٍ.',
                    'تُؤْمِنُ أَنَّ ٱلتَّفَوُّقَ لَيْسَ مُسْتَحيلاً.',
                ],
            ],
            [
                'word' => 'ذُهِلَ',
                'clean_word' => 'ذُهِلَ',
                'raw_word' => 'ذهل',
                'root' => 'ذ-ه-ل',
                'example_sentence' => 'ذُهِلَ: أَصابَتْهُ ٱلدَّهْشَةُ ٱلشَّديدَةُ.',
                'strategy' => 'المعجم المساعد',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'slide_index' => 33,
                'image_path' => null,
                'linked_sentences' => [
                    'ذُهِلَ : أَصابَتْهُ ٱلدَّهْشَةُ ٱلشَّديدَةُ.',
                ],
            ],
            [
                'word' => 'وَقْتٌ قِياسِيٌّ',
                'clean_word' => 'وَقْتٌ قِياسِيٌّ',
                'raw_word' => 'وقت قياسي',
                'root' => 'ق-ي-س',
                'example_sentence' => 'وَقْتٌ قِياسِيٌّ: اَلزَّمَنُ ٱلْأَقْصَرُ لِإِنْجازِ مُهِمَّةٍ.',
                'strategy' => 'المعجم المساعد',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'slide_index' => 33,
                'image_path' => null,
                'linked_sentences' => [
                    'وَقْتٌ قِياسِيٌّ : اَلزَّمَنُ ٱلْأَقْصَرُ لإِنْجازِ مُهِمَّةٍ.',
                    'تَمَكَّنَتْ مِنْ إِنْهاءِ تَمْرينِ ٱلرِّياضِيّاتِ في وَقْتٍ قِياسِيٍّ.',
                ],
            ],
            [
                'word' => 'اَلصَّداقَةُ',
                'clean_word' => 'اَلصَّداقَةُ',
                'raw_word' => 'الصداقة',
                'root' => 'ص-د-ق',
                'example_sentence' => 'شَبَكَةُ الصَّداقَةِ: حُبٌّ، ثِقَةٌ، تَعاوُنٌ، سَعادَةٌ، أمانٌ.',
                'strategy' => 'استراتيجية شبكة الكلمة',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'slide_index' => 23,
                'image_path' => null,
                'linked_sentences' => [
                    'شَبَكَةُ كَلِمَةِ "الصَّداقَةُ": حُبٌّ - ثِقَةٌ - نَمْرَحُ - نَتَعاوَنُ - سَعادَةٌ - أمانٌ - نَلْعَبُ.',
                ],
            ],
            [
                'word' => 'اِعْتِذارٌ',
                'clean_word' => 'اِعْتِذارٌ',
                'raw_word' => 'اعتذار',
                'root' => 'ع-ذ-ر',
                'example_sentence' => 'شَبَكَةُ كَلِمَةِ "اعتذار": صِدْقٌ، مُسامَحَةٌ، نَدَمٌ، أَسَفٌ.',
                'strategy' => 'استراتيجية شبكة الكلمة',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S1',
                'slide_index' => 27,
                'image_path' => null,
                'linked_sentences' => [
                    'شَبَكَةُ كَلِمَةِ "اِعْتِذارٌ": صِدْقٌ - أَصْدِقاءُ - حِلْمٌ - خَطَأٌ - مُسامَحَةٌ - نَدَمٌ - أَسَفٌ.',
                ],
            ],

            // ==========================================
            // الحصة 2 و 3 (S2-S3): معجم النص القرائي «دَرْسٌ في ٱلْحَياةِ»
            // ==========================================
            [
                'word' => 'يَتَرَصَّدُ',
                'clean_word' => 'يَتَرَصَّدُ',
                'raw_word' => 'يترصد',
                'root' => 'ر-ص-د',
                'example_sentence' => 'يَتَرَصَّدُ: يُراقِبُ وَيَتَتَبَّعُ بِانْتِباهٍ.',
                'strategy' => 'معجم النص القرائي (مرادفات)',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S3',
                'slide_index' => 19,
                'image_path' => 'vocab_assets/ar/AR_N4_P1_SEM1_S3/image83.png',
                'linked_sentences' => [
                    'يَتَرَصَّدُ: يُراقِبُ وَيَتَتَبَّعُ.',
                    'وَصَفَ ٱلْكاتِبُ عَيْنا إِيّادٍ كَعَيْنا صَقْرٍ يَتَرَصَّدُ فَريسَتَهُ.',
                ],
            ],
            [
                'word' => 'عَبَثاً',
                'clean_word' => 'عَبَثاً',
                'raw_word' => 'عبثا',
                'root' => 'ع-ب-ث',
                'example_sentence' => 'عَبَثاً: دونَ فائِدَةٍ وَبِلا جَدْوى.',
                'strategy' => 'معجم النص القرائي (مرادفات)',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S3',
                'slide_index' => 20,
                'image_path' => null,
                'linked_sentences' => [
                    'عَبَثاً: دونَ فائِدَةٍ.',
                ],
            ],
            [
                'word' => 'اِبْتِسامَةٌ',
                'clean_word' => 'اِبْتِسامَةٌ',
                'raw_word' => 'ابتسامة',
                'root' => 'ب-س-م',
                'example_sentence' => 'شَبَكَةُ كَلِمَةِ "ابتسامة": فَرَحٌ، سَعيدٌ، صَديقٌ، نَجاحٌ.',
                'strategy' => 'استراتيجية شبكة الكلمة',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S3',
                'slide_index' => 22,
                'image_path' => null,
                'linked_sentences' => [
                    'شَبَكَةُ كَلِمَةِ "اِبْتِسامَةٌ": فَرَحٌ - سَعيدٌ - صَديقٌ - أُحَيّي - أَسْتَمْتِعُ - اَلنَّجاحُ - اَلْعيدُ - مُضْحِكٌ.',
                ],
            ],
            [
                'word' => 'أَسَى',
                'clean_word' => 'أَسَى',
                'raw_word' => 'اسى',
                'root' => 'أ-س-و',
                'example_sentence' => 'شَعَرَ أَنَسٌ بِٱلْأَسَى لِأَنَّهُ ظَنَّ أَنَّ إِيّاداً لَنْ يُسامِحَهُ.',
                'strategy' => 'معجم الفهم القرائي',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S3',
                'slide_index' => 29,
                'image_path' => null,
                'linked_sentences' => [
                    'شَعَرَ أَنَسٌ بِٱلْأَسَى لِأَنَّهُ ظَنَّ أَنَّ إِيّاداً لَنْ يُسامِحَهُ.',
                ],
            ],

            // ==========================================
            // الحصة 4 و 5 (S4-S5): استثمار النص، القيم، واستراتيجية توسيع فكرة
            // ==========================================
            [
                'word' => 'أَحْرَجَ',
                'clean_word' => 'أَحْرَجَ',
                'raw_word' => 'احرج',
                'root' => 'ح-ر-ج',
                'example_sentence' => 'شَعَرَ أَنَسٌ بِٱلنَّدَمِ لِأَنَّهُ أَحْرَجَ صَديقَهُ إِيّاداً أَمامَ ٱلْجَميعِ.',
                'strategy' => 'معجم استثمار النص',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S5',
                'slide_index' => 19,
                'image_path' => null,
                'linked_sentences' => [
                    'شَعَرَ أَنَسٌ بِٱلنَّدَمِ لِأَنَّهُ أَحْرَجَ صَديقَهُ إِيّاداً.',
                    'لَمْ أَقْصِدْ أَنْ أُحْرِجَكَ أَوْ أُؤْذِيَ مَشاعِرَكَ.',
                ],
            ],
            [
                'word' => 'نَدَمٌ',
                'clean_word' => 'نَدَمٌ',
                'raw_word' => 'ندم',
                'root' => 'ن-د-م',
                'example_sentence' => 'شَعَرَ أَنَسٌ بِٱلنَّدَمِ بَعْدَ تَصَرُّفِهِ ٱلْخاطِئِ.',
                'strategy' => 'معجم استثمار النص',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S5',
                'slide_index' => 18,
                'image_path' => null,
                'linked_sentences' => [
                    'شَعَرَ أَنَسٌ بِٱلنَّدَمِ لِأَنَّهُ أَحْرَجَ صَديقَهُ.',
                    'كانَتْ كَلِماتي خاطِئَةً، وَأَنا نادِمٌ عَلَيْها.',
                ],
            ],
            [
                'word' => 'اِلْتَقى',
                'clean_word' => 'اِلْتَقى',
                'raw_word' => 'التقى',
                'root' => 'ل-ق-ي',
                'example_sentence' => 'اِلْتَقى ٱلْأَصْدِقاءُ في ساحَةِ ٱلْمَدْرَسَةِ وَقْتَ ٱلْاِسْتِراحَةِ.',
                'strategy' => 'إستراتيجية توسيع فكرة',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S4',
                'slide_index' => 36,
                'image_path' => null,
                'linked_sentences' => [
                    'اِلْتَقى ٱلْأَصْدِقاءُ في ساحَةِ ٱلْمَدْرَسَةِ وَقْتَ ٱلْاِسْتِراحَةِ.',
                ],
            ],

            // ==========================================
            // الحصة 6 (S6): شكل وفهم وقراءة إثرائية «اَلْأَصْدِقاءُ ٱلْأَرْبَعَةُ»
            // ==========================================
            [
                'word' => 'حَديقَةٌ',
                'clean_word' => 'حَديقَةٌ',
                'raw_word' => 'حديقة',
                'root' => 'ح-د-ق',
                'example_sentence' => 'شَبَكَةُ كَلِمَةِ "حَديقَةٌ": أَزْهارٌ، أَشْجارٌ، بُسْتانِيُّ، حارِسٌ، مُتْعَةٌ.',
                'strategy' => 'استراتيجية شبكة الكلمة',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S6',
                'slide_index' => 20,
                'image_path' => null,
                'linked_sentences' => [
                    'شَبَكَةُ كَلِمَةِ "حَديقَةٌ": أَزْهارٌ - أَشْجارٌ - بُسْتانِيُّ - حارِسٌ - مُتْعَةٌ - نَظافَةٌ - سِياجٌ.',
                    'أَنْشَأَ ٱلْأَصْدِقاءُ ٱلْأَرْبَعَةُ حَديقَةً جَميلَةً في حَيِّهِمْ.',
                    'أَصْبَحَتْ حَديقَتُهُمْ رَمْزاً لِلتَّعاوُنِ.',
                ],
            ],
            [
                'word' => 'مُبادَرَةٌ',
                'clean_word' => 'مُبادَرَةٌ',
                'raw_word' => 'مبادرة',
                'root' => 'ب-د-ر',
                'example_sentence' => 'اَلدّافِعُ وَراءَ مُبادَرَةِ ٱلْأَصْدِقاءِ ٱلْأَرْبَعَةِ أَنَّهُمْ يُحِبّونَ ٱلطَّبيعَةَ.',
                'strategy' => 'معجم الشكل والفهم',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S6',
                'slide_index' => 26,
                'image_path' => null,
                'linked_sentences' => [
                    'اَلدّافِعُ وَراءَ مُبادَرَةِ ٱلْأَصْدِقاءِ ٱلْأَرْبَعَةِ أَنَّهُمْ يُحِبّونَ ٱلطَّبيعَةَ.',
                ],
            ],
            [
                'word' => 'هِمَّةٌ',
                'clean_word' => 'هِمَّةٌ',
                'raw_word' => 'همة',
                'root' => 'ه-م-م',
                'example_sentence' => 'عَمِلَتْ لَيْلى بِهِمَّةٍ عالِيَةٍ فَتَمَكَّنَتْ مِنَ ٱلتَّفَوُّقِ.',
                'strategy' => 'معجم الطلاقة والقراءة',
                'grade' => 'N4',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N4_P1_SEM1_S3',
                'slide_index' => 13,
                'image_path' => null,
                'linked_sentences' => [
                    'عَمِلَتْ بِهِمَّةٍ عالِيَةٍ، فَتَمَكَّنَتْ مِنْ إِنْهاءِ تَمْرينِ ٱلرِّياضِيّاتِ.',
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
