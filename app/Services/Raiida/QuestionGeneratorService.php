<?php

namespace App\Services\Raiida;

class QuestionGeneratorService
{
    private const INSTRUCTIONS = [
        'image_select' => 'أختار الصورة المناسبة.',
        'card_select' => 'أختار البطاقة المناسبة.',
        'text_select' => 'أختار الكلمة المناسبة.',
        'audio_select' => 'أختار الصوت الصحيح.',
        'spelling_select' => 'أختار الكلمة الصحيحة.',
        'fill_text' => 'أكتب الكلمة المناسبة.',
        'letter_by_letter' => 'كوّن الكلمة حرفاً حرفاً.',
        'order_words' => 'رتّب الكلمات لتكوين جملة صحيحة.',
    ];

    private const GRADE_GRAMMAR_CONFIG = [
        1 => ['max_percent' => 0.15, 'types' => ['article_confusion'], 'max_word_len' => 7],
        2 => ['max_percent' => 0.25, 'types' => ['article_confusion', 'accent_variation'], 'max_word_len' => 8],
        3 => ['max_percent' => 0.35, 'types' => ['article_confusion', 'gender_agreement', 'basic_plural'], 'max_word_len' => 10],
        4 => ['max_percent' => 0.45, 'types' => ['article_confusion', 'gender_agreement', 'plural', 'simple_homophones'], 'max_word_len' => 12],
        5 => ['max_percent' => 0.50, 'types' => ['article_confusion', 'gender_agreement', 'plural', 'homophones', 'conjugation'], 'max_word_len' => 999],
        6 => ['max_percent' => 0.50, 'types' => ['article_confusion', 'gender_agreement', 'plural', 'homophones', 'conjugation'], 'max_word_len' => 999],
    ];

    private const COMPATIBLE_LEXICAL_GROUPS = [
        ['interjection', 'locution', 'phrase'],
        ['nom'],
        ['verbe'],
        ['adjectif'],
        ['pronom'],
    ];

    public function generateQuestions(
        array $target,
        array $allItems,
        bool $includeFillText = true,
        bool $includeLetterByLetter = true
    ): array
    {
        $gradeNum = $this->extractGradeNum((string) ($target['grade'] ?? 'N1'));
        $distractors = $this->selectDistractors($target, $allItems, 7);

        $questions = [];

        $q = $this->buildOrderWords($target, $gradeNum);
        if ($q !== null) {
            $questions[] = $q;
        }

        if (count($distractors) > 0) {
            $q = $this->buildUniversalTextToImage($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }

            $q = $this->buildUniversalImageToText($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }

            $q = $this->buildUniversalAudioToImage($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }

            $q = $this->buildUniversalImageToAudio($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }

            $q = $this->buildUniversalTextToImageAudioCard($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }

            $q = $this->buildUniversalImageAudioToText($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }

            $q = $this->buildUniversalAudioToTextImageCard($target, $distractors, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }
        }

        $q = $this->buildGrammarTrap($target, $gradeNum);
        if ($q !== null) {
            $questions[] = $q;
        }

        if ($includeFillText) {
            $q = $this->buildFillText($target, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }
        }

        if ($includeLetterByLetter) {
            $q = $this->buildLetterByLetter($target, $gradeNum);
            if ($q !== null) {
                $questions[] = $q;
            }
        }

        if (count($questions) > 10) {
            $questions = array_slice($questions, 0, 10);
        }

        return $this->postProcessQuestions($questions);
    }

    public function selectDistractors(array $target, array $allItems, int $maxCount = 3): array
    {
        $targetId = $target['id'] ?? null;
        $targetLexical = (string) ($target['lexical_type'] ?? '');
        $targetGroup = $target['distractor_group'] ?? null;
        $targetWeekNum = $this->extractWeekNum((string) ($target['week'] ?? 'SEM1'));
        $targetPeriodNum = $this->extractPeriodNum((string) ($target['period'] ?? 'P1'));
        $targetGrade = $target['grade'] ?? null;

        $compatibleTypes = $targetLexical !== '' ? $this->getCompatibleTypes($targetLexical) : [];

        $t1 = [];
        $t3 = [];
        $t5 = [];
        $t7 = [];

        foreach ($allItems as $item) {
            if (($item['id'] ?? null) === $targetId) {
                continue;
            }
            if (($item['grade'] ?? null) !== $targetGrade) {
                continue;
            }
            if (empty($item['revizy_image_file_id']) || empty($item['revizy_audio_file_id'])) {
                continue;
            }

            $itemLexical = (string) ($item['lexical_type'] ?? '');
            $itemPeriodNum = $this->extractPeriodNum((string) ($item['period'] ?? 'P0'));
            $itemWeekNum = $this->extractWeekNum((string) ($item['week'] ?? 'SEM0'));

            $sameGroup = (($item['distractor_group'] ?? null) === $targetGroup) && ! empty($targetGroup);
            $typeMatch = (($itemLexical === $targetLexical) && $targetLexical !== '')
                || (($targetLexical !== '') && in_array($itemLexical, $compatibleTypes, true));

            $isSameWeek = $itemPeriodNum === $targetPeriodNum && $itemWeekNum === $targetWeekNum;
            if ($sameGroup && $typeMatch) {
                if ($isSameWeek) {
                    $t1[] = $item;
                }
            } elseif ($sameGroup && ! $typeMatch) {
                if ($isSameWeek) {
                    $t3[] = $item;
                }
            } elseif ($typeMatch && ! $sameGroup) {
                if ($isSameWeek) {
                    $t5[] = $item;
                }
            }

            if ($isSameWeek) {
                $t7[] = $item;
            }
        }

        $selected = [];
        $seenIds = [];

        // Enforce same SEM distractors only to keep cross-SEM usage near zero.
        foreach ([$t1, $t3, $t5, $t7] as $tier) {
            foreach ($tier as $item) {
                if (count($selected) >= $maxCount) {
                    break;
                }

                $id = $item['id'] ?? null;
                if ($id !== null && ! isset($seenIds[$id])) {
                    $selected[] = $item;
                    $seenIds[$id] = true;
                }
            }

            if (count($selected) >= $maxCount) {
                break;
            }
        }

        return $selected;
    }

    private function buildUniversalTextToImage(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        $usefulDistractors = array_values(array_filter($distractors, static fn (array $d): bool => ! empty($d['revizy_image_file_id'])));
        if ($usefulDistractors === []) {
            return null;
        }

        $chosen = [$this->randomChoice($usefulDistractors)];

        $answers = [[
            'body' => null,
            'is_correct' => true,
            'media' => [
                'image' => $target['revizy_image_file_id'] ?? null,
                'audio' => null,
            ],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => null,
                'is_correct' => false,
                'media' => [
                    'image' => $d['revizy_image_file_id'] ?? null,
                    'audio' => null,
                ],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Trouve l\'image',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['image_select'],
                'body' => $word,
                'media' => ['image' => null, 'audio' => null],
                'answers' => $answers,
            ],
        ];
    }

    private function buildUniversalImageToText(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        if (empty($target['revizy_image_file_id'])) {
            return null;
        }

        $usefulDistractors = array_values(array_filter($distractors, static fn (array $d): bool => ! empty($d['word'])));
        if ($usefulDistractors === []) {
            return null;
        }

        $chosen = [$this->randomChoice($usefulDistractors)];

        $answers = [[
            'body' => $word,
            'is_correct' => true,
            'media' => ['image' => null, 'audio' => null],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => (string) ($d['word'] ?? ''),
                'is_correct' => false,
                'media' => ['image' => null, 'audio' => null],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Trouve le mot',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['text_select'],
                'body' => null,
                'media' => ['image' => $target['revizy_image_file_id'], 'audio' => null],
                'answers' => $answers,
            ],
        ];
    }

    private function buildUniversalAudioToImage(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        if (empty($target['revizy_audio_file_id'])) {
            return null;
        }

        $usefulDistractors = array_values(array_filter($distractors, static fn (array $d): bool => ! empty($d['revizy_image_file_id'])));
        if ($usefulDistractors === []) {
            return null;
        }

        $chosen = [$this->randomChoice($usefulDistractors)];

        $answers = [[
            'body' => null,
            'is_correct' => true,
            'media' => ['image' => $target['revizy_image_file_id'] ?? null, 'audio' => null],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => null,
                'is_correct' => false,
                'media' => ['image' => $d['revizy_image_file_id'] ?? null, 'audio' => null],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Écoute et trouve l\'image',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['image_select'],
                'body' => null,
                'media' => ['image' => null, 'audio' => $target['revizy_audio_file_id']],
                'answers' => $answers,
            ],
        ];
    }

    private function buildUniversalImageToAudio(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        if (empty($target['revizy_image_file_id']) || empty($target['revizy_audio_file_id'])) {
            return null;
        }

        $usefulDistractors = array_values(array_filter($distractors, static fn (array $d): bool => ! empty($d['revizy_audio_file_id'])));
        if ($usefulDistractors === []) {
            return null;
        }

        $chosen = [$this->randomChoice($usefulDistractors)];

        $answers = [[
            'body' => null,
            'is_correct' => true,
            'media' => ['image' => null, 'audio' => $target['revizy_audio_file_id']],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => null,
                'is_correct' => false,
                'media' => ['image' => null, 'audio' => $d['revizy_audio_file_id'] ?? null],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Écoute et trouve le son',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['audio_select'],
                'body' => null,
                'media' => ['image' => $target['revizy_image_file_id'], 'audio' => null],
                'answers' => $answers,
            ],
        ];
    }

    private function buildUniversalTextToImageAudioCard(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        $useful = array_values(array_filter(
            $distractors,
            static fn (array $d): bool => ! empty($d['revizy_image_file_id']) && ! empty($d['revizy_audio_file_id'])
        ));

        if ($useful === [] || empty($target['revizy_image_file_id']) || empty($target['revizy_audio_file_id'])) {
            return null;
        }

        $chosen = [$this->randomChoice($useful)];

        $answers = [[
            'body' => null,
            'is_correct' => true,
            'media' => ['image' => $target['revizy_image_file_id'], 'audio' => $target['revizy_audio_file_id']],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => null,
                'is_correct' => false,
                'media' => [
                    'image' => $d['revizy_image_file_id'] ?? null,
                    'audio' => $d['revizy_audio_file_id'] ?? null,
                ],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Trouve la carte',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['card_select'],
                'body' => $word,
                'media' => ['image' => null, 'audio' => null],
                'answers' => $answers,
            ],
        ];
    }

    private function buildUniversalImageAudioToText(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        if (empty($target['revizy_image_file_id']) || empty($target['revizy_audio_file_id'])) {
            return null;
        }

        $useful = array_values(array_filter($distractors, static fn (array $d): bool => ! empty($d['word'])));
        if ($useful === []) {
            return null;
        }

        $chosen = [$this->randomChoice($useful)];

        $answers = [[
            'body' => $word,
            'is_correct' => true,
            'media' => ['image' => null, 'audio' => null],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => (string) ($d['word'] ?? ''),
                'is_correct' => false,
                'media' => ['image' => null, 'audio' => null],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Regarde et écoute',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['text_select'],
                'body' => null,
                'media' => [
                    'image' => $target['revizy_image_file_id'],
                    'audio' => $target['revizy_audio_file_id'],
                ],
                'answers' => $answers,
            ],
        ];
    }

    private function buildUniversalAudioToTextImageCard(array $target, array $distractors, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        if (empty($target['revizy_audio_file_id']) || empty($target['revizy_image_file_id'])) {
            return null;
        }

        $useful = array_values(array_filter(
            $distractors,
            static fn (array $d): bool => ! empty($d['word']) && ! empty($d['revizy_image_file_id'])
        ));
        if ($useful === []) {
            return null;
        }

        $chosen = [$this->randomChoice($useful)];

        $answers = [[
            'body' => $word,
            'is_correct' => true,
            'media' => ['image' => $target['revizy_image_file_id'], 'audio' => null],
        ]];

        foreach ($chosen as $d) {
            $answers[] = [
                'body' => (string) ($d['word'] ?? ''),
                'is_correct' => false,
                'media' => ['image' => $d['revizy_image_file_id'] ?? null, 'audio' => null],
            ];
        }

        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Écoute et trouve la carte',
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['card_select'],
                'body' => null,
                'media' => ['image' => null, 'audio' => $target['revizy_audio_file_id']],
                'answers' => $answers,
            ],
        ];
    }

    private function buildGrammarTrap(array $target, int $gradeNum): ?array
    {
        if (($target['lexical_type'] ?? null) !== 'nom') {
            return null;
        }

        $gender = $target['gender'] ?? null;
        if (! $gender || empty($target['revizy_image_file_id'])) {
            return null;
        }

        $word = (string) ($target['word'] ?? '');
        $normalized = $this->normalizeVocabularyText($word);
        $lower = mb_strtolower($normalized, 'UTF-8');

        if (str_starts_with($lower, "l'")
            || str_starts_with($lower, 'les ')
            || str_starts_with($lower, 'des ')) {
            return null;
        }

        $bare = $this->bareNoun($word);
        if ($bare === '') {
            return null;
        }

        $useIndefinite = str_starts_with($lower, 'un ') || str_starts_with($lower, 'une ');
        $useDefinite = str_starts_with($lower, 'le ') || str_starts_with($lower, 'la ');

        if (! $useIndefinite && ! $useDefinite) {
            return null;
        }

        $gender = (string) $gender;
        if ($useIndefinite) {
            $correctArticle = $gender === 'feminine' ? 'Une' : 'Un';
            $incorrectArticle = $gender === 'feminine' ? 'Un' : 'Une';
        } else {
            $correctArticle = $gender === 'feminine' ? 'La' : 'Le';
            $incorrectArticle = $gender === 'feminine' ? 'Le' : 'La';
        }

        $config = self::GRADE_GRAMMAR_CONFIG[$gradeNum] ?? self::GRADE_GRAMMAR_CONFIG[6];
        if ($gradeNum <= 1 && mb_strlen($bare, 'UTF-8') > (int) $config['max_word_len']) {
            return null;
        }

        $correctText = $correctArticle . ' ' . $bare;
        $incorrectText = $incorrectArticle . ' ' . $bare;

        $answers = [
            ['body' => $correctText, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]],
            ['body' => $incorrectText, 'is_correct' => false, 'media' => ['image' => null, 'audio' => null]],
        ];
        shuffle($answers);

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $correctText . ' / ' . $incorrectText,
            'type' => 'universal',
            'data' => [
                'instruction' => self::INSTRUCTIONS['spelling_select'],
                'body' => null,
                'media' => ['image' => $target['revizy_image_file_id'], 'audio' => null],
                'answers' => $answers,
            ],
        ];
    }

    private function buildFillText(array $target, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        $bare = $this->bareNoun($word);

        if ($this->hasAccents($bare)) {
            return null;
        }

        if ($gradeNum <= 1 && mb_strlen($bare, 'UTF-8') > 5) {
            return null;
        }

        if (empty($target['revizy_image_file_id'])) {
            return null;
        }

        $answers = [
            ['body' => $word, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]],
        ];

        if (mb_strtolower($bare, 'UTF-8') !== mb_strtolower($word, 'UTF-8')) {
            $answers[] = ['body' => $bare, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]];
        }

        if (($target['lexical_type'] ?? null) === 'nom' && ! empty($target['gender'])) {
            $gender = (string) $target['gender'];
            $vowels = "aeiouhéèêëàâäùûüôîïAEIOUHÉÈÊËÀÂÄÙÛÜÔÎÏ";

            if ($bare !== '' && mb_strpos($vowels, mb_substr($bare, 0, 1, 'UTF-8'), 0, 'UTF-8') !== false) {
                $definiteForm = "L'" . $bare;
            } elseif ($gender === 'feminine') {
                $definiteForm = 'La ' . $bare;
            } else {
                $definiteForm = 'Le ' . $bare;
            }

            if (mb_strtolower($definiteForm, 'UTF-8') !== mb_strtolower($word, 'UTF-8')) {
                $answers[] = ['body' => $definiteForm, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]];
            }
        }

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Écriture libre',
            'type' => 'universal_fill_text',
            'data' => [
                'instruction' => self::INSTRUCTIONS['fill_text'],
                'body' => null,
                'media' => [
                    'image' => $target['revizy_image_file_id'] ?? null,
                    'audio' => $target['revizy_audio_file_id'] ?? null,
                ],
                'answers' => $answers,
            ],
        ];
    }

    private function buildLetterByLetter(array $target, int $gradeNum): ?array
    {
        $word = (string) ($target['word'] ?? '');
        $baseWord = trim((string) ($target['base_word'] ?? ''));
        $bare = $baseWord !== '' ? $baseWord : $this->bareNoun($word);

        if (mb_strlen($bare, 'UTF-8') >= 7) {
            return null;
        }

        if ($gradeNum <= 1 && mb_strlen($bare, 'UTF-8') > 5) {
            return null;
        }

        if (empty($target['revizy_image_file_id'])) {
            return null;
        }

        $baseWordAudioRevizyId = trim((string) ($target['base_word_audio_revizy_id'] ?? ''));
        if ($baseWordAudioRevizyId === '') {
            return null;
        }

        $answers = [
            ['body' => $bare, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]],
        ];

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - Lettre par lettre',
            'type' => 'letter_by_letter',
            'data' => [
                'instruction' => self::INSTRUCTIONS['letter_by_letter'],
                'body' => null,
                'media' => [
                    'image' => $target['revizy_image_file_id'] ?? null,
                    'audio' => $baseWordAudioRevizyId,
                ],
                'answers' => $answers,
            ],
        ];
    }

    private function buildOrderWords(array $target, int $gradeNum): ?array
    {
        $word = trim((string) ($target['word'] ?? ''));
        if ($word === '') {
            return null;
        }

        $tokens = preg_split('/\s+/u', $word, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($tokens) < 3) {
            return null;
        }

        $hasImage = ! empty($target['revizy_image_file_id']);
        $hasAudio = ! empty($target['revizy_audio_file_id']);
        if (! $hasImage && ! $hasAudio) {
            return null;
        }

        $answers = [
            ['body' => $word, 'is_correct' => true, 'media' => ['image' => null, 'audio' => null]],
        ];

        return [
            'concept_id' => $target['concept_id'] ?? null,
            'name' => $word . ' - ترتيب الكلمات',
            'type' => 'order_words',
            'data' => [
                'instruction' => self::INSTRUCTIONS['order_words'],
                'body' => null,
                'media' => [
                    'image' => $target['revizy_image_file_id'] ?? null,
                    'audio' => $target['revizy_audio_file_id'] ?? null,
                ],
                'answers' => $answers,
            ],
        ];
    }

    private function postProcessQuestions(array $questions): array
    {
        $typingTypes = ['universal_fill_text', 'letter_by_letter'];

        foreach ($questions as &$question) {
            $type = $question['type'] ?? '';
            $isTypingType = in_array($type, $typingTypes, true);

            if (! empty($question['name'])) {
                $question['name'] = $this->capitalizeArticle((string) $question['name']);
            }

            if (isset($question['data']['body']) && is_string($question['data']['body'])) {
                $question['data']['body'] = $this->autoColorArticles(
                    $this->capitalizeArticle((string) $question['data']['body'])
                );
            }

            if (! empty($question['data']['answers']) && is_array($question['data']['answers'])) {
                foreach ($question['data']['answers'] as &$answer) {
                    if (! empty($answer['body'])) {
                        $capitalized = $this->capitalizeArticle((string) $answer['body']);

                        if ($isTypingType) {
                            $answer['body'] = $capitalized;
                        } else {
                            $answer['body'] = $this->autoColorArticles($capitalized);
                        }
                    }
                }
                unset($answer);
            }
        }
        unset($question);

        return $questions;
    }

    private function autoColorArticles(string $text): string
    {
        if ($text === '') {
            return $text;
        }

        if (preg_match('/\[(BLUE|PINK|RED|GREEN|YELLOW|PURPLE|ORANGE)\]/i', $text) === 1) {
            return $text;
        }

        $text = preg_replace('/\b(Le|Un)\b(?=\s)/iu', '[BLUE]$1[/BLUE]', $text) ?? $text;

        return preg_replace('/\b(La|Une)\b(?=\s)/iu', '[PINK]$1[/PINK]', $text) ?? $text;
    }

    private function capitalizeArticle(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        $parts = explode(' ', $text, 2);
        $firstWord = mb_strtolower($parts[0], 'UTF-8');

        if (in_array($firstWord, ['un', 'une', 'le', 'la'], true)) {
            $capitalized = mb_strtoupper(mb_substr($parts[0], 0, 1, 'UTF-8'), 'UTF-8')
                . mb_strtolower(mb_substr($parts[0], 1, null, 'UTF-8'), 'UTF-8');

            return isset($parts[1]) ? $capitalized . ' ' . $parts[1] : $capitalized;
        }

        return $text;
    }

    private function hasAccents(string $text): bool
    {
        $accented = 'éèêëàâäùûüôîïçœæÉÈÊËÀÂÄÙÛÜÔÎÏÇŒÆ';

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($chars as $char) {
            if (mb_strpos($accented, $char, 0, 'UTF-8') !== false) {
                return true;
            }
        }

        return false;
    }

    private function getCompatibleTypes(string $lexicalType): array
    {
        foreach (self::COMPATIBLE_LEXICAL_GROUPS as $group) {
            if (in_array($lexicalType, $group, true)) {
                return $group;
            }
        }

        return [$lexicalType];
    }

    private function extractGradeNum(string $grade): int
    {
        $normalized = str_replace('N', '', $grade);
        if (! is_numeric($normalized)) {
            return 1;
        }

        return (int) $normalized;
    }

    private function extractWeekNum(string $week): int
    {
        $normalized = str_replace('SEM', '', $week);
        if (! is_numeric($normalized)) {
            return 1;
        }

        return (int) $normalized;
    }

    private function extractPeriodNum(string $period): int
    {
        $normalized = str_replace('P', '', $period);
        if (! is_numeric($normalized)) {
            return 1;
        }

        return (int) $normalized;
    }

    private function bareNoun(string $word): string
    {
        $word = $this->normalizeVocabularyText($word);

        $prefixes = [
            "L'", "l'", 'Le ', 'le ', 'La ', 'la ', 'Les ', 'les ',
            'Un ', 'un ', 'Une ', 'une ', 'Des ', 'des ',
            'Ou ', 'ou ',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($word, $prefix)) {
                return mb_substr($word, mb_strlen($prefix, 'UTF-8'), null, 'UTF-8');
            }
        }

        return $word;
    }

    private function normalizeVocabularyText(string $text): string
    {
        $text = str_replace("\u{2019}", "'", $text);
        $text = str_replace(["\u{00A0}", "\u{202F}"], ' ', $text);
        $text = trim($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }

    private function getArticleGender(string $gender): ?array
    {
        if ($gender === 'feminine') {
            return ['correct' => 'Une', 'incorrect' => 'Un'];
        }

        if ($gender === 'masculine') {
            return ['correct' => 'Un', 'incorrect' => 'Une'];
        }

        return null;
    }

    private function randomChoice(array $items): array
    {
        $index = array_rand($items);

        return $items[$index];
    }
}
