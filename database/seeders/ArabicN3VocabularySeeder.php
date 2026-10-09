<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN3VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N3, Period P1, Week SEM1.
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الحصة 1 (S1): معجم المفردات الأساسية المصورة
            // ==========================================
            [
                'word' => 'غُرورٌ',
                'clean_word' => 'غُرورٌ',
                'raw_word' => 'غرور',
                'root' => 'غ-ر-ر',
                'example_sentence' => 'شَعَرَ ٱلطِّفْلُ بِالْغُرورِ أَمامَ زُمَلائِهِ.',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 29,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image72.png',
                'linked_sentences' => [
                    'شَعَرَ ٱلطِّفْلُ بِالْغُرورِ أَمامَ زُمَلائِهِ.',
                    'غُرورٌ: إِعْجابُ ٱلشَّخْصِ بِنَفْسِهِ.',
                    'عاتَبَ الْأُسْتاذُ التَّلْميذَ عَلى غُرورِهِ وَطَلَبَ مِنْهُ الاِعْتِذارَ لِزُمَلائِهِ.',
                ],
            ],
            [
                'word' => 'عِتابٌ',
                'clean_word' => 'عِتابٌ',
                'raw_word' => 'عتاب',
                'root' => 'ع-ت-ب',
                'example_sentence' => 'تُعاتِبُ ٱلْأُمُّ ٱبْنَها لِأَنَّهُ يُهْمِلُ دُروسَهُ.',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 22,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image67.png',
                'linked_sentences' => [
                    'تُعاتِبُ ٱلْأُمُّ ٱبْنَها لِأَنَّهُ يُهْمِلُ دُروسَهُ.',
                    'عاتَبَ: لامَ بِرِفْقٍ.',
                ],
            ],
            [
                'word' => 'اِعْتِذارٌ',
                'clean_word' => 'اِعْتِذارٌ',
                'raw_word' => 'اعتذار',
                'root' => 'ع-ذ-ر',
                'example_sentence' => 'اِعْتَذَرَتِ ٱلْفَتاةُ لِزَميلِها بَعْدَما أَسْقَطَتْ أَدَواتِهِ.',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 25,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image70.png',
                'linked_sentences' => [
                    'اِعْتَذَرَتِ ٱلْفَتاةُ لِزَميلِها بَعْدَما أَسْقَطَتْ أَدَواتِهِ.',
                    'اِعْتَذَرَ: طَلَبَ ٱلْعَفْوَ وَٱلسَّماحَ.',
                ],
            ],
            [
                'word' => 'اَلْأَسَفُ',
                'clean_word' => 'اَلْأَسَفُ',
                'raw_word' => 'الاسف',
                'root' => 'أ-س-ف',
                'example_sentence' => 'شَعَرَ مَجْدٌ بِٱلْأَسَفِ عَلى حالِ ٱلْكَلْبِ ٱلْجَريحِ.',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 34,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image74.jpeg',
                'linked_sentences' => [
                    'شَعَرَ مَجْدٌ بِٱلْأَسَفِ عَلى حالِ ٱلْكَلْبِ ٱلْجَريحِ.',
                    'أَسَفٌ : شُعورٌ بِٱلْحُزْنِ وَٱلنَّدَمِ.',
                ],
            ],
            [
                'word' => 'اِحْتِرامٌ',
                'clean_word' => 'اِحْتِرامٌ',
                'raw_word' => 'احترام',
                'root' => 'ح-ر-م',
                'example_sentence' => 'اَلاِبْنُ يَحْتَرِمُ والِدَتَهُ.',
                'strategy' => 'معجم مصور',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 37,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image75.jpeg',
                'linked_sentences' => [
                    'اَلاِبْنُ يَحْتَرِمُ والِدَتَهُ.',
                    'اِحْتَرَمَ: قَدَّرَهُ وَٱعْتَرَفَ بِقيمَتِهِ.',
                ],
            ],

            // ==========================================
            // الحصة 1 (S1): معجم الحكاية «سِرُّ الصداقة»
            // ==========================================
            [
                'word' => 'اَلْمَوَدَّةُ',
                'clean_word' => 'اَلْمَوَدَّةُ',
                'raw_word' => 'المودة',
                'root' => 'و-د-د',
                'example_sentence' => 'مَوَدَّةٌ: مَحَبَّةٌ وَوِئامٌ بَيْنَ ٱلْأَصْدِقاءِ.',
                'strategy' => 'معجم الحكاية',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 51,
                'image_path' => null,
                'linked_sentences' => [
                    'مَوَدَّةٌ: مَحَبَّةٌ.',
                ],
            ],
            [
                'word' => 'اَلْغَيْرَةُ',
                'clean_word' => 'اَلْغَيْرَةُ',
                'raw_word' => 'الغيرة',
                'root' => 'غ-ي-ر',
                'example_sentence' => 'اَلْغَيْرَةُ: شُعورٌ بِٱلْقَلَقِ وَٱلْخَوْفِ مِنْ خُسْرانِ شَيْءٍ عَزيزٍ.',
                'strategy' => 'معجم الحكاية',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 49,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image82.png',
                'linked_sentences' => [
                    'اَلْغَيْرَةُ: شُعورٌ بِٱلْقَلَقِ وَٱلْخَوْفِ مِنْ خُسْرانِ شَيْءٍ عَزيزٍ.',
                ],
            ],
            [
                'word' => 'اَلْأَنانِيُّ',
                'clean_word' => 'اَلْأَنانِيُّ',
                'raw_word' => 'الاناني',
                'root' => 'أ-ن-ن',
                'example_sentence' => 'اَلْأَنانِيُّ: مَنْ يُحِبُّ نَفْسَهُ حُبّاً مُفْرِطاً وَلا يُفَكِّرُ في غَيْرِهِ.',
                'strategy' => 'معجم الحكاية',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 50,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image84.jpeg',
                'linked_sentences' => [
                    'اَلْأَنانِيُّ: مَنْ يُحِبُّ نَفْسَهُ حُبّاً مُفْرِطاً وَلا يُفَكِّرُ في غَيْرِهِ.',
                ],
            ],
            [
                'word' => 'اَلْفُضولُ',
                'clean_word' => 'اَلْفُضولُ',
                'raw_word' => 'الفضول',
                'root' => 'ف-ض-ل',
                'example_sentence' => 'اَلْفُضولُ :حُبُّ ٱلاطِّلاعِ وَٱلرَّغْبَةُ في مَعْرِفَةِ شَيْءٍ جَديدٍ.',
                'strategy' => 'معجم الحكاية',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S1',
                'slide_index' => 48,
                'image_path' => 'vocab_assets/ar/AR_N3_P1_SEM1_S1/image81.png',
                'linked_sentences' => [
                    'اَلْفُضولُ :حُبُّ ٱلاطِّلاعِ وَٱلرَّغْبَةُ في مَعْرِفَةِ شَيْءٍ جَديدٍ.',
                ],
            ],

            // ==========================================
            // الحصة 2 (S2): معجم النص القرائي «اَلصَّديقُ ٱلْجَديدُ»
            // ==========================================
            [
                'word' => 'باغَتَ',
                'clean_word' => 'باغَتَ',
                'raw_word' => 'باغت',
                'root' => 'ب-غ-ت',
                'example_sentence' => 'باغَتَ: فاجَأَ، قامَ بِٱلشَّيْءِ عَلى غَيْرِ ٱلْمُتَوَقَّعِ.',
                'strategy' => 'معجم النص القرائي',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S2',
                'slide_index' => 31,
                'image_path' => null,
                'linked_sentences' => [
                    'باغَتَ: فاجَأَ، قامَ بِٱلشَّيْءِ عَلى غَيْرِ ٱلْمُتَوَقَّعِ.',
                ],
            ],
            [
                'word' => 'اِقْتَرَفَ',
                'clean_word' => 'اِقْتَرَفَ',
                'raw_word' => 'اقترف',
                'root' => 'ق-ر-ف',
                'example_sentence' => 'اِقْتَرَفَ: اِرْتَكَبَ ذَنْباً أَوْ خَطَأً بِشَكْلٍ مُتَعَمَّدٍ.',
                'strategy' => 'معجم النص القرائي',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S2',
                'slide_index' => 32,
                'image_path' => null,
                'linked_sentences' => [
                    'اِقْتَرَفَ: اِرْتَكَبَ ذَنْباً أَوْ خَطَأً بِشَكْلٍ مُتَعَمَّدٍ.',
                ],
            ],
            [
                'word' => 'هَرَعَ',
                'clean_word' => 'هَرَعَ',
                'raw_word' => 'هرع',
                'root' => 'ه-ر-ع',
                'example_sentence' => 'هَرَعَ: هَرْوَلَ وَمَشى إِلَيْهِ بِسُرْعَةٍ.',
                'strategy' => 'معجم النص القرائي',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S2',
                'slide_index' => 33,
                'image_path' => null,
                'linked_sentences' => [
                    'هَرَعَ: هَرْوَلَ وَمَشى إِلَيْهِ بِسُرْعَةٍ.',
                ],
            ],

            // ==========================================
            // الحصة 3 و 4 (S3-S4): استراتيجية الفهم وتوسيع المعجم
            // ==========================================
            [
                'word' => 'تَقاذَفَ',
                'clean_word' => 'تَقاذَفَ',
                'raw_word' => 'تقاذف',
                'root' => 'ق-ذ-ف',
                'example_sentence' => 'تَقاذَفَ ٱلْأَطْفالُ ٱلْكُرَةَ.',
                'strategy' => 'إستراتيجية الكلمات المفاتيح',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S3',
                'slide_index' => 59,
                'image_path' => null,
                'linked_sentences' => [
                    'تَقاذَفَ ٱلْأَطْفالُ ٱلْكُرَةَ.',
                ],
            ],
            [
                'word' => 'تَحَلَّقَ',
                'clean_word' => 'تَحَلَّقَ',
                'raw_word' => 'تحلق',
                'root' => 'ح-ل-ق',
                'example_sentence' => 'تَحَلَّقَ ٱلْأَطْفالُ حَوْلَ مُرادٍ.',
                'strategy' => 'معجم النص القرائي',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S3',
                'slide_index' => 62,
                'image_path' => null,
                'linked_sentences' => [
                    'تَحَلَّقَ ٱلْأَطْفالُ حَوْلَ مُرادٍ.',
                ],
            ],
            [
                'word' => 'اِنْطَلَقَتْ',
                'clean_word' => 'اِنْطَلَقَتْ',
                'raw_word' => 'انطلقت',
                'root' => 'ط-ل-ق',
                'example_sentence' => 'اِنْطَلَقَتِ ٱلْمُباراةُ بَيْنَ ٱلْفَريقَيْنِ (ضد: اِنْتَهَتْ).',
                'strategy' => 'أستثمر المعجم (تضاد)',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S4',
                'slide_index' => 24,
                'image_path' => null,
                'linked_sentences' => [
                    'اِنْطَلَقَتِ ٱلْمُباراةُ (ضد: اِنْتَهَتْ).',
                ],
            ],
            [
                'word' => 'اِقْتَرَبَ',
                'clean_word' => 'اِقْتَرَبَ',
                'raw_word' => 'اقترب',
                'root' => 'ق-ر-ب',
                'example_sentence' => 'عِنْدَما ٱقْتَرَبَ مُرادٌ مِنَ ٱلْأَطْفالِ، جَلَسَ يُراقِبُهُمْ في صَمْتٍ (ضد: اِبْتَعَدَ).',
                'strategy' => 'أستثمر المعجم (تضاد)',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S4',
                'slide_index' => 21,
                'image_path' => null,
                'linked_sentences' => [
                    'عِنْدَما ٱقْتَرَبَ مُرادٌ مِنَ ٱلْأَطْفالِ، جَلَسَ يُراقِبُهُمْ في صَمْتٍ.',
                ],
            ],
            [
                'word' => 'إِيثارٌ',
                'clean_word' => 'إِيثارٌ',
                'raw_word' => 'ايثار',
                'root' => 'أ-ث-ر',
                'example_sentence' => 'صَديقي عَلِيٌّ مِثالٌ رائِعٌ لِلْإِيثارِ! يُقَدِّمُ حاجاتي عَلى حاجاتِهِ دائِماً.',
                'strategy' => 'معجم الطلاقة والقراءة',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S4',
                'slide_index' => 11,
                'image_path' => null,
                'linked_sentences' => [
                    'صَديقي عَلِيٌّ مِثالٌ رائِعٌ لِلْإِيثارِ! يُقَدِّمُ حاجاتي عَلى حاجاتِهِ دائِماً.',
                ],
            ],
            [
                'word' => 'صَداقَةٌ',
                'clean_word' => 'صَداقَةٌ',
                'raw_word' => 'صداقة',
                'root' => 'ص-د-ق',
                'example_sentence' => 'اَلصَّداقَةُ كَنْزٌ ثَمينٌ.',
                'strategy' => 'لعبة المفردات (تركيب مقطعي)',
                'grade' => 'N3',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N3_P1_SEM1_S3',
                'slide_index' => 14,
                'image_path' => null,
                'linked_sentences' => [
                    'اَلصَّداقَةُ كَنْزٌ ثَمينٌ.',
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
