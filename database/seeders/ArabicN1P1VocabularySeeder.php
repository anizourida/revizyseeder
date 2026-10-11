<?php

namespace Database\Seeders;

use App\Models\Raiida\ArabicVocabularyItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArabicN1P1VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds for Arabic Grade N1, Period P1 (الوحدة 1: من نحن (الأسرة والمدرسة)).
     */
    public function run(): void
    {
        $items = [
            // ==========================================
            // الأسبوع 1: د (الدال)
            // ==========================================
            [
                'word' => 'مِحْفَظَةٌ',
                'clean_word' => 'مِحْفَظَةٌ',
                'raw_word' => 'محفظة',
                'example_sentence' => 'حقيبة مدرسية لحمل الأدوات والكتب',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM1_S1/image32.png',
                'linked_sentences' => [
                    'حقيبة مدرسية لحمل الأدوات والكتب',
                ],
            ],
            [
                'word' => 'دَفْتَرٌ',
                'clean_word' => 'دَفْتَرٌ',
                'raw_word' => 'دفتر',
                'example_sentence' => 'كراس لتسجيل وكتابة الدروس والتمارين',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM1_S1/image31.png',
                'linked_sentences' => [
                    'كراس لتسجيل وكتابة الدروس والتمارين',
                ],
            ],
            [
                'word' => 'مِقْلَمَةٌ',
                'clean_word' => 'مِقْلَمَةٌ',
                'raw_word' => 'مقلمة',
                'example_sentence' => 'محفظة صغيرة لحفظ الأقلام والأدوات',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM1_S1/image30.png',
                'linked_sentences' => [
                    'محفظة صغيرة لحفظ الأقلام والأدوات',
                ],
            ],
            [
                'word' => 'أَقْلامٌ مُلَوَّنَةٌ',
                'clean_word' => 'أَقْلامٌ مُلَوَّنَةٌ',
                'raw_word' => 'أقلام ملونة',
                'example_sentence' => 'أقلام خشبية للتلوين والرسم',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM1_S1/image106.png',
                'linked_sentences' => [
                    'أقلام خشبية للتلوين والرسم',
                ],
            ],
            [
                'word' => 'مَدْرَسَةٌ',
                'clean_word' => 'مَدْرَسَةٌ',
                'raw_word' => 'مدرسة',
                'example_sentence' => 'فضاء ومؤسسة التعلم والتربية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM1_S1/image100.png',
                'linked_sentences' => [
                    'فضاء ومؤسسة التعلم والتربية',
                ],
            ],
            [
                'word' => 'أَدَواتٌ مَدْرَسِيَّةٌ',
                'clean_word' => 'أَدَواتٌ مَدْرَسِيَّةٌ',
                'raw_word' => 'أدوات مدرسية',
                'example_sentence' => 'مجموع اللوازم المدرسية من دفاتر وأقلام ومقلمة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM1',
                'lesson_id' => 'AR_N1_P1_SEM1_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM1_S1/image107.png',
                'linked_sentences' => [
                    'مجموع اللوازم المدرسية من دفاتر وأقلام ومقلمة',
                ],
            ],
            // ==========================================
            // الأسبوع 2: م (الميم)
            // ==========================================
            [
                'word' => 'وِزْرَةٌ',
                'clean_word' => 'وِزْرَةٌ',
                'raw_word' => 'وزرة',
                'example_sentence' => 'لباس مدرسي موحد يرتديه التلميذ',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM2_S1/image75.png',
                'linked_sentences' => [
                    'لباس مدرسي موحد يرتديه التلميذ',
                ],
            ],
            [
                'word' => 'طاوِلَةٌ',
                'clean_word' => 'طاوِلَةٌ',
                'raw_word' => 'طاولة',
                'example_sentence' => 'طاولة الجلوس والكتابة في القسم',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM2_S1/image74.jpg',
                'linked_sentences' => [
                    'طاولة الجلوس والكتابة في القسم',
                ],
            ],
            [
                'word' => 'خِزانَةٌ',
                'clean_word' => 'خِزانَةٌ',
                'raw_word' => 'خزانة',
                'example_sentence' => 'خزانة القسم لترتيب الكتب والملفات',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM2_S1/image73.jpg',
                'linked_sentences' => [
                    'خزانة القسم لترتيب الكتب والملفات',
                ],
            ],
            [
                'word' => 'مَكْتَبٌ',
                'clean_word' => 'مَكْتَبٌ',
                'raw_word' => 'مكتب',
                'example_sentence' => 'مكتب الأستاذ في مقدمة الفصل',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM2_S1/image66.jpg',
                'linked_sentences' => [
                    'مكتب الأستاذ في مقدمة الفصل',
                ],
            ],
            [
                'word' => 'يُقَدِّمُ نَفْسَهُ',
                'clean_word' => 'يُقَدِّمُ نَفْسَهُ',
                'raw_word' => 'يقدم نفسه',
                'example_sentence' => 'يعرف باسمه وصفته أمام أصدقائه',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM2',
                'lesson_id' => 'AR_N1_P1_SEM2_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM2_S1/image61.png',
                'linked_sentences' => [
                    'يعرف باسمه وصفته أمام أصدقائه',
                ],
            ],
            // ==========================================
            // الأسبوع 3: ب (الباء)
            // ==========================================
            [
                'word' => 'ساحَةٌ',
                'clean_word' => 'ساحَةٌ',
                'raw_word' => 'ساحة',
                'example_sentence' => 'فناء المدرسة للعب والاستراحة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM3_S1/image69.jpg',
                'linked_sentences' => [
                    'فناء المدرسة للعب والاستراحة',
                ],
            ],
            [
                'word' => 'مَكْتَبَةٌ',
                'clean_word' => 'مَكْتَبَةٌ',
                'raw_word' => 'مكتبة',
                'example_sentence' => 'قاعة المطالعة واستعارة القصص والكتب',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM3_S1/image68.jpg',
                'linked_sentences' => [
                    'قاعة المطالعة واستعارة القصص والكتب',
                ],
            ],
            [
                'word' => 'مَسْرَحٌ',
                'clean_word' => 'مَسْرَحٌ',
                'raw_word' => 'مسرح',
                'example_sentence' => 'خشبة وفضاء العروض والأنشطة الثقافية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM3_S1/image67.jpg',
                'linked_sentences' => [
                    'خشبة وفضاء العروض والأنشطة الثقافية',
                ],
            ],
            [
                'word' => 'قاعَةُ الْأَساتِذَةِ',
                'clean_word' => 'قاعَةُ الْأَساتِذَةِ',
                'raw_word' => 'قاعة الأساتذة',
                'example_sentence' => 'قاعة استراحة واجتماعات المدرسين',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM3_S1/image59.png',
                'linked_sentences' => [
                    'قاعة استراحة واجتماعات المدرسين',
                ],
            ],
            [
                'word' => 'مَطْعَمٌ',
                'clean_word' => 'مَطْعَمٌ',
                'raw_word' => 'مطعم',
                'example_sentence' => 'مطعم المدرسة لتناول الوجبات',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM3_S1/image58.png',
                'linked_sentences' => [
                    'مطعم المدرسة لتناول الوجبات',
                ],
            ],
            [
                'word' => 'مَلْعَبٌ',
                'clean_word' => 'مَلْعَبٌ',
                'raw_word' => 'ملعب',
                'example_sentence' => 'فضاء ممارسة التربية البدنية والرياضة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM3',
                'lesson_id' => 'AR_N1_P1_SEM3_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM3_S1/image57.png',
                'linked_sentences' => [
                    'فضاء ممارسة التربية البدنية والرياضة',
                ],
            ],
            // ==========================================
            // الأسبوع 4: ف (الفاء)
            // ==========================================
            [
                'word' => 'حاسوبٌ',
                'clean_word' => 'حاسوبٌ',
                'raw_word' => 'حاسوب',
                'example_sentence' => 'جهاز كمبيوتر لإنجاز الأنشطة والتعلم',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM4_S1/image52.png',
                'linked_sentences' => [
                    'جهاز كمبيوتر لإنجاز الأنشطة والتعلم',
                ],
            ],
            [
                'word' => 'أَصْدِقاءُ',
                'clean_word' => 'أَصْدِقاءُ',
                'raw_word' => 'أصدقاء',
                'example_sentence' => 'رفقاء الفصل والدراسة واللعب',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM4_S1/image40.png',
                'linked_sentences' => [
                    'رفقاء الفصل والدراسة واللعب',
                ],
            ],
            [
                'word' => 'مُسابَقَةٌ',
                'clean_word' => 'مُسابَقَةٌ',
                'raw_word' => 'مسابقة',
                'example_sentence' => 'منافسة ثقافية وترفيهية بين التلاميذ',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM4_S1/image46.png',
                'linked_sentences' => [
                    'منافسة ثقافية وترفيهية بين التلاميذ',
                ],
            ],
            [
                'word' => 'يَفُوزُ',
                'clean_word' => 'يَفُوزُ',
                'raw_word' => 'يفوز',
                'example_sentence' => 'ينتصر ويحقق المرتبة الأولى في التحدي',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM4_S1/image98.png',
                'linked_sentences' => [
                    'ينتصر ويحقق المرتبة الأولى في التحدي',
                ],
            ],
            [
                'word' => 'جائِزَةٌ',
                'clean_word' => 'جائِزَةٌ',
                'raw_word' => 'جائزة',
                'example_sentence' => 'مكافأة وهدية التميز والتفوق',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM4',
                'lesson_id' => 'AR_N1_P1_SEM4_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM4_S1/image44.png',
                'linked_sentences' => [
                    'مكافأة وهدية التميز والتفوق',
                ],
            ],
            // ==========================================
            // الأسبوع 5: ر (الراء)
            // ==========================================
            [
                'word' => 'دُودُو',
                'clean_word' => 'دُودُو',
                'raw_word' => 'دودو',
                'example_sentence' => 'الكلب الصغير رفيق مراد في الحكاية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM5_S1/image84.png',
                'linked_sentences' => [
                    'الكلب الصغير رفيق مراد في الحكاية',
                ],
            ],
            [
                'word' => 'مِحْفَظَةٌ',
                'clean_word' => 'مِحْفَظَةٌ',
                'raw_word' => 'محفظة',
                'example_sentence' => 'محفظة مراد التي اختبأ فيها دُودُو',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM5',
                'lesson_id' => 'AR_N1_P1_SEM5_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM5_S1/image85.png',
                'linked_sentences' => [
                    'محفظة مراد التي اختبأ فيها دُودُو',
                ],
            ],
            // ==========================================
            // الأسبوع 6: إدماج ودعم توليفي
            // ==========================================
            [
                'word' => 'تَقْويمٌ',
                'clean_word' => 'تَقْويمٌ',
                'raw_word' => 'تقويم',
                'example_sentence' => 'قياس المكتسبات والمهارات المكتسبة',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'قياس المكتسبات والمهارات المكتسبة',
                ],
            ],
            [
                'word' => 'إِدْماجٌ',
                'clean_word' => 'إِدْماجٌ',
                'raw_word' => 'إدماج',
                'example_sentence' => 'تركيب المعارف اللغوية وتطبيقها في وضعيات تواصلية',
                'strategy' => 'معجم مصور',
                'grade' => 'N1',
                'subject' => 'AR',
                'period' => 'P1',
                'week' => 'SEM6',
                'lesson_id' => 'AR_N1_P1_SEM6_S1',
                'image_path' => 'vocab_assets/ar/AR_N1_P1_SEM6_S1/image70.png',
                'linked_sentences' => [
                    'تركيب المعارف اللغوية وتطبيقها في وضعيات تواصلية',
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
