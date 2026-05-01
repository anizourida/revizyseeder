<?php

namespace App\Services\Raiida;

class ConjugaisonTextAnalyzer
{
    private const SUBJECT_PRONOUNS = [
        "j'", "je ", "tu ", "il ", "elle ", "on ",
        "nous ", "vous ", "ils ", "elles ",
    ];

    private const DISCOVERY_MAP = [
        's\'appelle' => 's\'appeler',
        'm\'appelle' => 's\'appeler',
        't\'appelles' => 's\'appeler',
        'suis' => 'être',
        'est' => 'être',
        'sommes' => 'être',
        'êtes' => 'être',
        'sont' => 'être',
        'ai ' => 'avoir',
        'as ' => 'avoir',
        ' a ' => 'avoir',
        'avons' => 'avoir',
        'avez' => 'avoir',
        'ont ' => 'avoir',
        'vais' => 'aller',
        'vas' => 'aller',
        'va ' => 'aller',
        'allons' => 'aller',
        'allez' => 'aller',
        'vont' => 'aller',
    ];
    /**
     * @return array<string, mixed>|null
     */
    public function analyze(string $text): ?array
    {
        $clean = $this->normalizeWhitespace($text);
        if ($clean === '') {
            return null;
        }

        $wordCount = $this->wordCount($clean);
        if ($wordCount < 3 || $wordCount > 45) {
            return null;
        }

        $normalized = $this->normalizeForMatching($clean);
        if (! $this->looksLikeConjugaison($normalized)) {
            return null;
        }

        $verbe = $this->extractVerb($clean, $normalized);
        $tense = $this->extractTense($normalized);
        $score = $this->score($normalized, $verbe, $tense, $wordCount);

        if ($score < 1) {
            return null;
        }

        return [
            'name' => $clean,
            'raw_data' => $clean,
            'verbe' => $verbe,
            'tense' => $tense,
            'score' => $score,
            'word_count' => $wordCount,
            'normalized' => $normalized,
        ];
    }

    public function discoverVerbFromText(string $text): ?string
    {
        $normalized = $this->normalizeForMatching($text);
        foreach (self::DISCOVERY_MAP as $form => $infinitive) {
            if (str_contains($normalized, $form)) {
                return $infinitive;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function analyzeQuestion(string $text, ?string $expectedVerb = null, ?string $expectedTense = null): ?array
    {
        $clean = $this->normalizeWhitespace($text);
        if ($clean === '') {
            return null;
        }

        $wordCount = $this->wordCount($clean);
        if ($wordCount < 4 || $wordCount > 60) {
            return null;
        }

        $normalized = $this->normalizeForMatching($clean);
        if (! $this->looksLikeQuestionPrompt($normalized)) {
            return null;
        }

        $score = 0;
        if (str_contains($normalized, '?')) {
            $score += 5;
        }

        if ($this->containsAny($normalized, [
            'écrivez',
            'ecrivez',
            'complétez',
            'completez',
            'choisis',
            'choisissez',
            'reliez',
            'transforme',
            'transformez',
            'mets',
            'mettez',
            'conjugue',
            'conjuguer',
            'donne',
            'donnez',
            'trouve',
            'trouvez',
            'surligne',
            'souligne',
            'barre',
            'réécris',
            'reecris',
            'recopie',
            'lisez',
        ])) {
            $score += 6;
        }

        if (str_contains($normalized, 'verbe') || str_contains($normalized, 'conjug')) {
            $score += 2;
        }

        $expectedVerb = $expectedVerb !== null ? $this->normalizeForMatching($expectedVerb) : null;
        $expectedTense = $expectedTense !== null ? $this->normalizeForMatching($expectedTense) : null;

        if ($expectedVerb !== null && $expectedVerb !== '' && str_contains($normalized, $expectedVerb)) {
            $score += 5;
        }

        if ($expectedTense !== null && $expectedTense !== '' && str_contains($normalized, $expectedTense)) {
            $score += 3;
        }

        if ($score < 5) {
            return null;
        }

        return [
            'question' => $clean,
            'score' => $score,
            'word_count' => $wordCount,
            'normalized' => $normalized,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function analyzeExampleSentence(string $text, ?string $expectedVerb = null): ?array
    {
        $clean = $this->normalizeWhitespace($text);
        if ($clean === '') {
            return null;
        }

        $wordCount = $this->wordCount($clean);
        if ($wordCount < 3 || $wordCount > 30) {
            return null;
        }

        $sentence = $this->extractBestSentenceCandidate($clean);
        if ($sentence === '') {
            return null;
        }

        $normalizedSentence = $this->normalizeForMatching($sentence);
        if (str_contains($normalizedSentence, '?')) {
            return null;
        }

        if ($this->containsAny($normalizedSentence, [
            'qui veut',
            'levez la main',
            'écrivez',
            'ecrivez',
            'complétez',
            'completez',
            'choisis',
            'choisissez',
            'reliez',
            'transforme',
            'transformez',
            'conjugue',
            'conjuguer',
            'trouve',
            'trouvez',
            'surligne',
            'souligne',
            'barre',
            'réécris',
            'reecris',
            'recopie',
            'image',
            'espace reserve',
            'espace réservé',
        ])) {
                return null;
        }

        if ($this->isPedagogicalIndication($normalizedSentence)) {
            return null;
        }

        if (! $this->looksLikeConjugatedSentence($normalizedSentence)) {
            return null;
        }

        $score = 3;
        if ($this->hasSubjectPronoun($normalizedSentence)) {
            $score += 4;
        }

        $expectedVerbNormalized = $expectedVerb !== null ? $this->normalizeForMatching($expectedVerb) : '';
        if ($expectedVerbNormalized !== '' && $this->containsExpectedVerbFamily($normalizedSentence, $expectedVerbNormalized)) {
            $score += 5;
        }

        if ($this->wordCount($sentence) >= 5) {
            $score += 1;
        }

        if ($score < 6) {
            return null;
        }

        return [
            'sentence' => $sentence,
            'score' => $score,
            'word_count' => $this->wordCount($sentence),
            'normalized' => $normalizedSentence,
        ];
    }

    private function looksLikeConjugaison(string $normalized): bool
    {
        $hasCoreKeyword = $this->containsAny($normalized, [
            'verbe',
            'verbes',
            'conjugaison',
            'conjuguer',
            'conjugue',
            'conjugaison :',
            'groupe',
        ]);

        if (! $hasCoreKeyword) {
            return false;
        }

        if ($this->containsAny($normalized, [
            'présent',
            'present',
            'futur',
            'imparfait',
            'passé',
            'passe',
            'conditionnel',
            'subjonctif',
            'impératif',
            'imperatif',
            'indicatif',
            'progressif',
            'continu',
        ])) {
            return true;
        }

        return $this->containsAny($normalized, [
            'utiliser le verbe',
            'conjugaison :',
            'conjuguer le verbe',
            'les verbes du',
        ]);
    }

    private function looksLikeQuestionPrompt(string $normalized): bool
    {
        $hasPromptSignal = str_contains($normalized, '?')
            || $this->containsAny($normalized, [
                'écrivez',
                'ecrivez',
                'complétez',
                'completez',
                'choisis',
                'choisissez',
                'transforme',
                'transformez',
                'mets',
                'mettez',
                'conjugue',
                'conjuguer',
                'qui veut',
                'donne',
                'donnez',
                'trouve',
                'trouvez',
                'reliez',
                'surligne',
                'souligne',
                'barre',
                'réécris',
                'reecris',
                'recopie',
                'lisez',
            ]);

        if (! $hasPromptSignal) {
            return false;
        }

        return $this->containsAny($normalized, [
            'verbe',
            'verbes',
            'conjug',
            'présent',
            'present',
            'futur',
            'imparfait',
            'passé',
            'passe',
            'conditionnel',
            'subjonctif',
            'impératif',
            'imperatif',
            'indicatif',
            'progressif',
            'continu',
        ]);
    }

    private function extractVerb(string $clean, string $normalized): ?string
    {
        $quoted = str_replace(['«', '»', '“', '”', '"', '’', '‘', '`'], "'", $clean);

        $groupPatterns = [
            '/\bverbes?\s+du\s+(1er|2e|2eme|2ème|3e|3eme|3ème|premier|deuxième|troisième|1|2|3)\s+groupe\b/ui',
            '/\bles\s+verbes?\s+du\s+(1er|2e|2eme|2ème|3e|3eme|3ème|premier|deuxième|troisième|1|2|3)\s+groupe\b/ui',
        ];

        foreach ($groupPatterns as $pattern) {
            if (preg_match($pattern, $quoted, $matches) === 1) {
                return $this->normalizeGroupLabel((string) $matches[1]);
            }
        }

        if (preg_match('/\bverbes?\s+termin[ée]s?\s+en\s+(er|ir|re|oir|ger|cer|yer)\b/ui', $quoted, $matches) === 1) {
            return 'terminés en ' . mb_strtolower((string) $matches[1]);
        }

        $verbPatterns = [
            '/\b(?:le\s+)?verbe\s+\'\s*([\p{L}][\p{L}\s\-\']{1,50}?)\s*\'\s+(?:au|aux|a\s+l\'|à\s+l\'|du|de\s+l\')\b/ui',
            '/\b(?:le\s+)?verbe\s+([\p{L}][\p{L}\s\-\']{1,50}?)\s+(?:au|aux|a\s+l\'|à\s+l\'|du|de\s+l\')\b/ui',
            '/\bconjuguer\s+(?:le\s+)?verbe\s+\'\s*([\p{L}][\p{L}\s\-\']{1,50}?)\s*\'/ui',
            '/\bconjuguer\s+(?:le\s+)?verbe\s+([\p{L}][\p{L}\s\-\']{1,50}?)(?:\s+au\b|\.|$)/ui',
            '/\bconjugaison\s*[:;]\s*(?:le\s+)?(?:verbe\s+)?([\p{L}][\p{L}\s\-\']{1,50}?)(?:\s+au\b|\.|$)/ui',
            '/^([\p{L}][\p{L}\s\-\']{1,50}?)\s+(?:au|aux|a\s+l\'|à\s+l\')\b/ui',
        ];

        foreach ($verbPatterns as $pattern) {
            if (preg_match($pattern, $quoted, $matches) === 1) {
                $candidate = $this->cleanupVerbCandidate((string) $matches[1]);
                if ($candidate !== null) {
                    return $candidate;
                }
            }
        }

        if (preg_match('/\b(?:être\s+et\s+avoir|avoir\s+et\s+être)\b/ui', $normalized, $matches) === 1) {
            return mb_strtolower((string) $matches[0]);
        }

        return null;
    }

    private function extractTense(string $normalized): ?string
    {
        $tenseMap = [
            'passé composé' => '/\bpass[ée]\s+compos[ée]\b/ui',
            'plus-que-parfait' => '/\bplus[\s\-]que[\s\-]parfait\b/ui',
            'passé simple' => '/\bpass[ée]\s+simple\b/ui',
            'passé récent' => '/\bpass[ée]\s+r[ée]cent\b/ui',
            'conditionnel présent' => '/\bconditionnel\s+pr[ée]sent\b/ui',
            'futur simple' => '/\bfutur\s+simple\b/ui',
            'futur proche' => '/\bfutur\s+proche\b/ui',
            'présent progressif' => '/\bpr[ée]sent\s+(?:progressif|continu)\b/ui',
            'imparfait' => '/\bimparfait\b/ui',
            'présent' => '/\bpr[ée]sent\b/ui',
            'futur' => '/\bfutur\b/ui',
            'conditionnel' => '/\bconditionnel\b/ui',
            'subjonctif' => '/\bsubjonctif\b/ui',
            'impératif' => '/\bimp[ée]ratif\b/ui',
            'indicatif' => '/\bindicatif\b/ui',
        ];

        foreach ($tenseMap as $canonical => $pattern) {
            if (preg_match($pattern, $normalized) === 1) {
                return $canonical;
            }
        }

        return null;
    }

    private function score(string $normalized, ?string $verbe, ?string $tense, int $wordCount): int
    {
        $score = 0;

        if (str_starts_with($normalized, 'conjugaison')) {
            $score += 6;
        }

        if (str_starts_with($normalized, 'utiliser ')) {
            $score += 5;
        }

        if (str_contains($normalized, 'nous allons apprendre à conjuguer')) {
            $score += 4;
        }

        if (str_contains($normalized, 'le verbe') || str_contains($normalized, 'les verbes')) {
            $score += 3;
        }

        if (str_contains($normalized, 'du 1er groupe') || str_contains($normalized, 'du 2') || str_contains($normalized, 'du 3')) {
            $score += 2;
        }

        if ($verbe !== null) {
            $score += 3;
        }

        if ($tense !== null) {
            $score += 3;
        }

        if ($verbe !== null && $tense !== null) {
            $score += 2;
        }

        if ($wordCount > 24) {
            $score -= 3;
        }

        if ($this->containsAny($normalized, [
            'écrivez',
            'ecrivez',
            'complétez',
            'completez',
            'qui veut',
            'levez la main',
            'sur vos ardoises',
            'exercice',
            'item',
            'reliez',
            'souligne',
            'barre',
            'récris',
            'recopie',
            'lisez',
        ])) {
            $score -= 7;
        }

        if (str_contains($normalized, '?')) {
            $score -= 2;
        }

        return $score;
    }

    private function normalizeGroupLabel(string $value): string
    {
        $normalized = mb_strtolower($this->normalizeWhitespace($value));

        return match ($normalized) {
            '1', '1er', 'premier' => '1er groupe',
            '2', '2e', '2eme', '2ème', 'deuxième' => '2e groupe',
            default => '3e groupe',
        };
    }

    private function cleanupVerbCandidate(string $value): ?string
    {
        $value = $this->normalizeWhitespace($value);
        $value = trim($value, "\t\n\r\0\x0B .,:;!?\"'«»()[]");
        $value = mb_strtolower($value);

        if ($value === '' || mb_strlen($value) < 2 || mb_strlen($value) > 45) {
            return null;
        }

        $wordCount = $this->wordCount($value);
        if ($wordCount > 3) {
            return null;
        }

        $blocked = [
            'du',
            'de',
            'des',
            'au',
            'aux',
            'la',
            'le',
            'les',
            'présent',
            'present',
            'passé',
            'passe',
            'futur',
            'imparfait',
            'conditionnel',
            'indicatif',
            'subjonctif',
            'impératif',
            'imperatif',
            'progressif',
            'continu',
            'verbe',
            'verbes',
        ];

        if (in_array($value, $blocked, true)) {
            return null;
        }

        if (preg_match('/^(?:au|aux|du|de|des|a|à|la|le|les)\b/ui', $value) === 1) {
            return null;
        }

        if (preg_match('/\b(?:présent|present|pass[ée]|passe|futur|imparfait|conditionnel|indicatif|subjonctif|imp[ée]ratif|progressif|continu)\b/ui', $value) === 1) {
            return null;
        }

        return $value;
    }

    private function normalizeWhitespace(string $value): string
    {
        $value = str_replace(["\u{00A0}", "\u{202F}"], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim((string) $value);
    }

    private function extractBestSentenceCandidate(string $text): string
    {
        $parts = preg_split('/(?<=[\.\!\?])\s+/u', $text) ?: [];
        foreach ($parts as $part) {
            $part = trim((string) $part, " \t\n\r\0\x0B\"'«»");
            if ($part === '') {
                continue;
            }

            $normalized = $this->normalizeForMatching($part);
            if ($this->looksLikeConjugatedSentence($normalized)) {
                return $part;
            }
        }

        return trim($text, " \t\n\r\0\x0B\"'«»");
    }

    private function normalizeForMatching(string $value): string
    {
        $value = str_replace(['’', '‘', '`'], "'", $value);
        $value = str_replace(['“', '”', '«', '»'], '"', $value);
        $value = mb_strtolower($value);

        return $this->normalizeWhitespace($value);
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function wordCount(string $value): int
    {
        $parts = preg_split('/\s+/u', trim($value));

        return $parts === false ? 0 : count(array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    private function looksLikeConjugatedSentence(string $normalized): bool
    {
        if ($this->containsAny($normalized, [
            'verbe',
            'conjug',
            'temps',
            'présent',
            'present',
            'futur',
            'imparfait',
            'passé',
            'passe',
            'conditionnel',
            'subjonctif',
            'impératif',
            'imperatif',
        ])) {
            return false;
        }

        return $this->hasSubjectPronoun($normalized)
            || preg_match('/\b(?:les|des)\s+\p{L}+\s+\p{L}{3,}(?:ent|ons|ez)\b/ui', $normalized) === 1;
    }

    private function hasSubjectPronoun(string $normalized): bool
    {
        return preg_match('/\bj\'[\p{L}\']{2,}\b/ui', $normalized) === 1
            || preg_match('/\b(?:je|tu|il|elle|on|nous|vous|ils|elles)\s+[\p{L}\']{2,}\b/ui', $normalized) === 1;
    }

    private function isPedagogicalIndication(string $normalized): bool
    {
        if (preg_match('/^aujourd[\'’]?hui\b/ui', $normalized) === 1) {
            return true;
        }

        if (preg_match('/^chacun\s+r[ée]fl[ée]chit\b/ui', $normalized) === 1) {
            return true;
        }

        if ($this->containsAny($normalized, [
            'nous allons apprendre',
            'on va apprendre',
            'vous allez apprendre',
            'dialogue en français',
            'dialogue en francais',
            'avec majd et nada',
            'il convient de lancer le mode diaporama',
            'mode diaporama',
            'diffusion de la leçon en classe',
            'diffusion de la lecon en classe',
            'on continue à répéter ensemble le dialogue',
            'on continue a repeter ensemble le dialogue',
            'répéter ensemble le dialogue',
            'repeter ensemble le dialogue',
            'je vais vous expliquer',
            'je vais vous expliquer la situation',
            'je vais vous expliquer cette situation',
        ])) {
            return true;
        }

        // Reject "future proche + infinitive" instruction-like formulations.
        return preg_match('/\b(?:je|j\'|tu|il|elle|on|nous|vous|ils|elles)\s+(?:vais|vas|va|allons|allez|vont)\s+\p{L}{3,}(?:er|ir|re|oir)\b/ui', $normalized) === 1;
    }

    private function containsExpectedVerbFamily(string $normalizedSentence, string $expectedVerb): bool
    {
        if ($expectedVerb === '' || preg_match('/\bgroupe\b/ui', $expectedVerb) === 1) {
            return false;
        }

        $roots = $this->buildVerbRoots($expectedVerb);
        if ($roots === []) {
            return false;
        }

        $tokens = preg_split('/[^\p{L}\']+/u', $normalizedSentence) ?: [];
        foreach ($tokens as $token) {
            $token = trim((string) $token, "'");
            if (str_starts_with($token, "j'")) {
                $token = mb_substr($token, 2);
            }
            if ($token === '' || mb_strlen($token) < 3) {
                continue;
            }

            foreach ($roots as $root) {
                if (mb_strlen($root) < 3) {
                    continue;
                }

                if (str_starts_with($token, $root)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function buildVerbRoots(string $expectedVerb): array
    {
        $verb = trim(mb_strtolower($expectedVerb));
        if ($verb === '') {
            return [];
        }

        $roots = [$verb];

        foreach (['oir', 'er', 'ir', 're'] as $ending) {
            if (str_ends_with($verb, $ending) && mb_strlen($verb) > mb_strlen($ending) + 1) {
                $stem = mb_substr($verb, 0, mb_strlen($verb) - mb_strlen($ending));
                $roots[] = $stem;

                if (mb_strlen($stem) > 4) {
                    $roots[] = mb_substr($stem, 0, mb_strlen($stem) - 1);
                }
            }
        }

        if (mb_strlen($verb) >= 5) {
            $roots[] = mb_substr($verb, 0, 4);
        }

        return array_values(array_unique(array_filter($roots, static fn (string $value): bool => $value !== '')));
    }
}
