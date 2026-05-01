<?php

namespace App\Services\Raiida;

use App\Models\Raiida\ApiProvider;
use App\Services\Raiida\Exceptions\RaiidaApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class GeminiClassificationService
{
    private const ALLOWED_LEXICAL_TYPES = [
        'nom',
        'verbe',
        'adjectif',
        'locution',
        'phrase',
        'interjection',
        'pronom',
    ];

    private const ALLOWED_GROUPS = [
        'action',
        'animal',
        'body_part',
        'clothing',
        'color',
        'description',
        'emotion',
        'family',
        'food',
        'health',
        'home',
        'leisure',
        'nature',
        'personal_info',
        'place',
        'position',
        'profession',
        'routine',
        'school_concept',
        'school_object',
        'sentence',
        'time',
        'vehicle',
    ];

    private const MODEL_FALLBACKS = [
        'gemini-2.5-flash',
        'gemini-2.0-flash',
        'gemini-2.0-flash-lite',
        'gemini-1.5-flash-latest',
        'gemini-1.5-flash',
    ];

    public function __construct(
        private readonly ApiProviderRegistryService $providers,
        private readonly ApiProviderUsageService $usage
    ) {
    }

    /**
     * @param  array<int,array{id:int,word:string}>  $items
     * @return array<int,array{lexical_type:?string,gender:?string,distractor_group:?string,distractor_subgroup:?string,confidence:float}>
     */
    public function classify(array $items): array
    {
        if ($items === []) {
            return [];
        }

        $providers = $this->resolveActiveGeminiProviders();
        if ($providers === []) {
            throw new RaiidaApiException('No active Gemini provider configured. Add one in Admin > API Providers.', 422);
        }

        $errors = [];
        $lastException = null;
        $total = count($providers);

        foreach ($providers as $index => $provider) {
            try {
                return $this->classifyUsingProvider($provider, $items);
            } catch (Throwable $exception) {
                $lastException = $exception;
                $errors[] = $this->formatProviderFailure($provider, $exception);

                $hasMoreProviders = $index < ($total - 1);
                if (! $this->shouldTryNextProvider($exception, $hasMoreProviders)) {
                    break;
                }
            }
        }

        $status = $lastException instanceof RaiidaApiException
            ? $lastException->statusCode()
            : 502;

        throw new RaiidaApiException(
            'All active Gemini providers failed. ' . implode(' | ', $errors),
            $status
        );
    }

    /**
     * @param  array<int,array{id:int,word:string}>  $items
     * @return array<int,array{lexical_type:?string,gender:?string,distractor_group:?string,distractor_subgroup:?string,confidence:float}>
     */
    private function classifyUsingProvider(ApiProvider $provider, array $items): array
    {
        if (! $provider->is_active) {
            throw new RaiidaApiException("Gemini provider {$provider->slug} is disabled", 422);
        }

        $apiKey = trim((string) ($provider->api_key ?? config('raiida.gemini.api_key', '')));
        if ($apiKey === '') {
            throw new RaiidaApiException("Gemini provider {$provider->slug} has no API key", 422);
        }

        $baseUrl = rtrim((string) ($provider->base_url ?: config('raiida.gemini.base_url', 'https://generativelanguage.googleapis.com')), '/');
        $model = trim((string) ($provider->model ?: config('raiida.gemini.model', 'gemini-2.0-flash')));
        if ($model === '') {
            throw new RaiidaApiException("Gemini provider {$provider->slug} has no model configured", 422);
        }

        $this->assertWithinBudget($provider);

        $prompt = $this->buildPrompt($items);
        $maxOutputTokens = $this->resolveMaxOutputTokens(count($items));

        $resolved = $this->requestClassification($provider, $baseUrl, $apiKey, $model, $prompt, $maxOutputTokens);
        $payload = $resolved['payload'];
        $model = $resolved['model'];
        $apiVersion = $resolved['api_version'];
        $requestAttempts = $resolved['request_attempts'];

        $this->persistWorkingModel($provider, $model, $apiVersion);

        $promptTokens = (int) data_get($payload, 'usageMetadata.promptTokenCount', 0);
        $outputTokens = (int) data_get($payload, 'usageMetadata.candidatesTokenCount', 0);
        $totalTokens = (int) data_get($payload, 'usageMetadata.totalTokenCount', 0);
        if ($totalTokens <= 0) {
            $totalTokens = max(0, $promptTokens + $outputTokens);
        }

        $this->usage->recordUsage(
            $provider,
            [
                'requests' => max(1, $requestAttempts),
                'input_tokens' => max(0, $promptTokens),
                'output_tokens' => max(0, $outputTokens),
                'total_tokens' => max(0, $totalTokens),
            ],
            null,
            null,
            [
                'model' => $model,
                'api_version' => $apiVersion,
            ]
        );

        $parts = data_get($payload, 'candidates.0.content.parts', []);
        $text = '';
        if (is_array($parts)) {
            foreach ($parts as $part) {
                $partText = data_get($part, 'text');
                if (is_string($partText) && trim($partText) !== '') {
                    $text .= ($text === '' ? '' : "\n") . $partText;
                }
            }
        }
        if (trim($text) === '') {
            throw new RaiidaApiException('Gemini returned empty classification output', 502);
        }

        $decoded = $this->decodeJsonArray($text);

        $normalized = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $lexicalType = $this->normalizeLexicalType((string) ($row['t'] ?? $row['lexical_type'] ?? ''));
            $normalizedGroup = $this->normalizeGroup(
                (string) ($row['gr'] ?? $row['group'] ?? $row['distractor_group'] ?? ''),
                $lexicalType
            );

            $normalized[$id] = [
                'lexical_type' => $lexicalType,
                'gender' => $this->normalizeGender((string) ($row['g'] ?? $row['gender'] ?? ''), $lexicalType),
                'distractor_group' => $normalizedGroup,
                'distractor_subgroup' => $this->normalizeSubgroup(
                    (string) ($row['sg'] ?? $row['subgroup'] ?? $row['distractor_subgroup'] ?? ''),
                    $normalizedGroup
                ),
                'confidence' => $this->normalizeConfidence($row['c'] ?? $row['confidence'] ?? null),
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int,ApiProvider>
     */
    private function resolveActiveGeminiProviders(): array
    {
        // Ensure built-in providers are synced before querying.
        $this->providers->all();

        $providers = ApiProvider::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query
                    ->whereRaw('LOWER(provider_type) = ?', ['gemini'])
                    ->orWhereRaw('LOWER(slug) = ?', ['gemini']);
            })
            ->orderByRaw("CASE WHEN LOWER(slug) = 'gemini' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->get();

        return $providers->all();
    }

    private function shouldTryNextProvider(Throwable $exception, bool $hasMoreProviders): bool
    {
        if (! $hasMoreProviders) {
            return false;
        }

        if ($exception instanceof RaiidaApiException) {
            return true;
        }

        $message = Str::lower($exception->getMessage());

        return str_contains($message, 'too many requests')
            || str_contains($message, 'quota')
            || str_contains($message, 'rate limit')
            || str_contains($message, 'timed out')
            || str_contains($message, 'could not resolve host')
            || str_contains($message, 'connection');
    }

    private function formatProviderFailure(ApiProvider $provider, Throwable $exception): string
    {
        $status = $exception instanceof RaiidaApiException
            ? $exception->statusCode()
            : (int) $exception->getCode();

        $statusText = $status > 0 ? ('HTTP ' . $status) : 'ERROR';
        $message = preg_replace('/\s+/u', ' ', trim($exception->getMessage())) ?? trim($exception->getMessage());

        return $provider->slug . ' [' . $statusText . ']: ' . mb_substr($message, 0, 180, 'UTF-8');
    }

    /**
     * @return array{
     *     payload:array<string,mixed>,
     *     model:string,
     *     api_version:string,
     *     request_attempts:int
     * }
     */
    private function requestClassification(
        ApiProvider $provider,
        string $baseUrl,
        string $apiKey,
        string $configuredModel,
        string $prompt,
        int $maxOutputTokens
    ): array {
        $versionPreference = trim((string) data_get($provider->metadata, 'gemini_api_version', ''));
        if ($versionPreference === '') {
            $versionPreference = trim((string) config('raiida.gemini.api_version', 'v1beta'));
        }
        if ($versionPreference === '') {
            $versionPreference = 'v1beta';
        }

        $versions = array_values(array_unique(array_filter([$versionPreference, 'v1beta', 'v1'])));
        $modelCandidates = $this->buildModelCandidates($configuredModel);
        $requestAttempts = 0;
        $attempted = [];
        $lastStatus = 0;
        $lastBody = '';

        foreach ($versions as $apiVersion) {
            $models = $modelCandidates;
            $discoveryDone = false;

            for ($index = 0; $index < count($models); $index++) {
                $model = $models[$index];
                if ($model === '') {
                    continue;
                }

                $attemptKey = $apiVersion . '|' . $model;
                if (isset($attempted[$attemptKey])) {
                    continue;
                }
                $attempted[$attemptKey] = true;
                $requestAttempts++;

                $response = $this->sendGenerateContentRequest(
                    $baseUrl,
                    $apiKey,
                    $apiVersion,
                    $model,
                    $prompt,
                    $maxOutputTokens
                );

                if ($response->successful()) {
                    $payload = $response->json();
                    if (! is_array($payload)) {
                        $this->usage->recordUsage(
                            $provider,
                            ['requests' => max(1, $requestAttempts)],
                            null,
                            'Gemini classify failed: invalid JSON response payload',
                            ['model' => $model, 'api_version' => $apiVersion]
                        );

                        throw new RaiidaApiException('Gemini returned invalid payload', 502);
                    }

                    return [
                        'payload' => $payload,
                        'model' => $model,
                        'api_version' => $apiVersion,
                        'request_attempts' => $requestAttempts,
                    ];
                }

                $status = $response->status();
                $body = (string) $response->body();
                $lastStatus = $status;
                $lastBody = $body;

                if ($this->isModelNotFoundError($status, $body)) {
                    if (! $discoveryDone) {
                        $discovered = $this->discoverModelCandidates($baseUrl, $apiKey, $apiVersion);
                        foreach ($discovered as $candidate) {
                            if (! in_array($candidate, $models, true)) {
                                $models[] = $candidate;
                            }
                        }
                        $discoveryDone = true;
                    }

                    continue;
                }

                $message = 'Gemini classify failed: HTTP ' . $status . ' - ' . $this->summarizeError($body);
                $this->usage->recordUsage(
                    $provider,
                    ['requests' => max(1, $requestAttempts)],
                    null,
                    $message,
                    [
                        'model' => $model,
                        'api_version' => $apiVersion,
                    ]
                );

                throw new RaiidaApiException($message, $status);
            }
        }

        $attemptedList = array_keys($attempted);
        $message = 'Gemini model not available for this API key/version. Attempted: '
            . implode(', ', $attemptedList)
            . ($lastStatus > 0 ? ' | last HTTP ' . $lastStatus . ' - ' . $this->summarizeError($lastBody) : '');

        $this->usage->recordUsage(
            $provider,
            ['requests' => max(1, $requestAttempts)],
            null,
            $message,
            [
                'configured_model' => $configuredModel,
                'attempted' => $attemptedList,
            ]
        );

        throw new RaiidaApiException($message, $lastStatus > 0 ? $lastStatus : 502);
    }

    /**
     * @return array<int,string>
     */
    private function buildModelCandidates(string $configuredModel): array
    {
        $candidates = [$configuredModel];
        foreach (self::MODEL_FALLBACKS as $fallback) {
            $candidates[] = $fallback;
        }

        $normalized = [];
        foreach ($candidates as $candidate) {
            $trimmed = trim((string) $candidate);
            if ($trimmed === '') {
                continue;
            }

            if (str_starts_with($trimmed, 'models/')) {
                $trimmed = Str::after($trimmed, 'models/');
            }

            if ($trimmed === '') {
                continue;
            }

            if (! in_array($trimmed, $normalized, true)) {
                $normalized[] = $trimmed;
            }
        }

        return $normalized;
    }

    /**
     * @return array<int,string>
     */
    private function discoverModelCandidates(string $baseUrl, string $apiKey, string $apiVersion): array
    {
        $url = $baseUrl . '/' . trim($apiVersion, '/') . '/models?key=' . urlencode($apiKey);

        try {
            $response = Http::timeout(45)
                ->retry(1, 600)
                ->acceptJson()
                ->get($url);
        } catch (Throwable) {
            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            return [];
        }

        $models = data_get($payload, 'models', []);
        if (! is_array($models)) {
            return [];
        }

        $discovered = [];
        foreach ($models as $row) {
            if (! is_array($row)) {
                continue;
            }

            $supported = data_get($row, 'supportedGenerationMethods', []);
            if (is_array($supported) && $supported !== [] && ! in_array('generateContent', $supported, true)) {
                continue;
            }

            $name = trim((string) data_get($row, 'name', ''));
            if ($name === '') {
                continue;
            }

            $name = str_starts_with($name, 'models/') ? Str::after($name, 'models/') : $name;
            if ($name === '') {
                continue;
            }

            $discovered[] = $name;
        }

        return array_values(array_unique($discovered));
    }

    private function sendGenerateContentRequest(
        string $baseUrl,
        string $apiKey,
        string $apiVersion,
        string $model,
        string $prompt,
        int $maxOutputTokens
    ): \Illuminate\Http\Client\Response {
        $url = $baseUrl
            . '/'
            . trim($apiVersion, '/')
            . '/models/'
            . rawurlencode($model)
            . ':generateContent?key='
            . urlencode($apiKey);

        return Http::timeout(90)
            ->retry(2, 700)
            ->acceptJson()
            ->post($url, [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $this->systemInstruction()],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0,
                    'topP' => 0.1,
                    'maxOutputTokens' => $maxOutputTokens,
                    'responseMimeType' => 'application/json',
                ],
            ]);
    }

    private function isModelNotFoundError(int $status, string $body): bool
    {
        if ($status !== 404) {
            return false;
        }

        $normalized = Str::lower($body);

        return str_contains($normalized, 'models/')
            && (str_contains($normalized, 'not found') || str_contains($normalized, 'not available'));
    }

    private function summarizeError(string $body): string
    {
        $flat = preg_replace('/\s+/u', ' ', trim($body)) ?? trim($body);
        if ($flat === '') {
            return 'empty response body';
        }

        return mb_substr($flat, 0, 280, 'UTF-8');
    }

    private function persistWorkingModel(ApiProvider $provider, string $model, string $apiVersion): void
    {
        $updates = [];

        if (trim((string) ($provider->model ?? '')) !== $model) {
            $updates['model'] = $model;
        }

        $metadata = is_array($provider->metadata) ? $provider->metadata : [];
        if (($metadata['gemini_api_version'] ?? null) !== $apiVersion) {
            $metadata['gemini_api_version'] = $apiVersion;
            $updates['metadata'] = $metadata;
        }

        if ($updates !== []) {
            $provider->fill($updates);
            $provider->save();
        }
    }

    private function systemInstruction(): string
    {
        return 'Classify French school vocabulary for distractor generation. Output must be strict JSON only.';
    }

    private function assertWithinBudget(ApiProvider $provider): void
    {
        if (strtolower((string) ($provider->limit_unit ?? '')) !== 'tokens') {
            return;
        }

        $summary = $this->usage->summary($provider);
        $limit = data_get($summary, 'usage.limit');
        $remaining = data_get($summary, 'usage.remaining');

        if ($limit !== null && $remaining !== null && (int) $remaining <= 0) {
            throw new RaiidaApiException('Gemini token limit reached for current month', 429);
        }
    }

    private function resolveMaxOutputTokens(int $itemCount): int
    {
        // Keep enough room for strict JSON output across multi-item batches.
        $configured = max(300, (int) config('raiida.gemini.max_output_tokens', 1400));
        $estimated = max(360, ($itemCount * 90) + 240);

        return min($configured, $estimated);
    }

    /**
     * @param  array<int,array{id:int,word:string}>  $items
     */
    private function buildPrompt(array $items): string
    {
        $allowedLexical = implode(', ', self::ALLOWED_LEXICAL_TYPES);
        $allowedGroups = implode(', ', self::ALLOWED_GROUPS);

        $lines = [];
        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);
            $word = trim((string) ($item['word'] ?? ''));
            if ($id <= 0 || $word === '') {
                continue;
            }
            $word = str_replace(["\r", "\n", '|'], [' ', ' ', '/'], $word);
            $word = preg_replace('/\s+/u', ' ', $word) ?? $word;
            $word = trim($word);
            if ($word === '') {
                continue;
            }

            $lines[] = $id . '|' . mb_substr($word, 0, 80, 'UTF-8');
        }

        return implode("\n", [
            'Goal: classify words for relevant distractors (e.g., "where do you live?" should not use "red").',
            'Return ONLY a JSON array. No markdown.',
            'Each item keys: id,t,g,gr,sg,c',
            't = lexical_type from: ' . $allowedLexical,
            'g = masculine|feminine|null (null for non-nouns or unknown)',
            'gr = distractor_group from: ' . $allowedGroups,
            'sg = short snake_case subgroup (or null)',
            'c = confidence 0..1',
            'Rules:',
            '- Infer noun gender from article when possible (le/un=masculine, la/une=feminine).',
            '- For verbs use action group; for adjectives use description group; for phrases/locutions often sentence group.',
            '- Keep groups semantically coherent for wrong-answer quality.',
            'Words:',
            implode("\n", $lines),
        ]);
    }

    /**
     * @return array<int,mixed>
     */
    private function decodeJsonArray(string $text): array
    {
        $clean = trim($text);
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $clean) ?? $clean;

        $firstArray = $this->extractFirstJsonArray($clean);
        if ($firstArray !== null) {
            $clean = $firstArray;
        }

        // First pass: decode as-is (valid array or object).
        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            if (array_is_list($decoded)) {
                return $decoded;
            }

            foreach (['items', 'data', 'results', 'rows'] as $listKey) {
                $maybeList = $decoded[$listKey] ?? null;
                if (is_array($maybeList) && array_is_list($maybeList)) {
                    return $maybeList;
                }
            }
        }

        if (! str_starts_with($clean, '[')) {
            if (preg_match('/\[[\s\S]*\]/', $clean, $matches) === 1) {
                $clean = $matches[0];
            }
        }

        // Second pass: decode extracted JSON array.
        $decoded = json_decode($clean, true);
        if (is_array($decoded) && array_is_list($decoded)) {
            return $decoded;
        }

        // Third pass: tolerate trailing commas.
        $withoutTrailingCommas = preg_replace('/,\s*([\]}])/m', '$1', $clean) ?? $clean;
        $decoded = json_decode($withoutTrailingCommas, true);
        if (is_array($decoded) && array_is_list($decoded)) {
            return $decoded;
        }

        // Last-resort salvage: extract complete JSON objects from truncated output.
        if (preg_match_all('/\{(?:[^{}"\\\\]|\\\\.|"(?:[^"\\\\]|\\\\.)*")*\}/s', $clean, $matches) > 0) {
            $rows = [];
            foreach (($matches[0] ?? []) as $chunk) {
                $row = json_decode($chunk, true);
                if (is_array($row)) {
                    $rows[] = $row;
                }
            }

            if ($rows !== []) {
                return $rows;
            }
        }

        throw new RaiidaApiException('Gemini returned non-JSON classification output', 502);
    }

    private function extractFirstJsonArray(string $text): ?string
    {
        $length = strlen($text);
        if ($length === 0) {
            return null;
        }

        $start = strpos($text, '[');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;

        for ($i = $start; $i < $length; $i++) {
            $char = $text[$i];

            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === '[') {
                $depth++;
                continue;
            }

            if ($char === ']') {
                $depth--;
                if ($depth === 0) {
                    return substr($text, $start, ($i - $start) + 1);
                }
            }
        }

        return null;
    }

    private function normalizeLexicalType(string $value): ?string
    {
        $normalized = Str::of($value)->lower()->ascii()->replace('-', '_')->replace(' ', '_')->trim()->toString();

        $map = [
            'noun' => 'nom',
            'nom' => 'nom',
            'verbe' => 'verbe',
            'verb' => 'verbe',
            'adjectif' => 'adjectif',
            'adjective' => 'adjectif',
            'locution' => 'locution',
            'phrase' => 'phrase',
            'interjection' => 'interjection',
            'pronom' => 'pronom',
            'pronoun' => 'pronom',
        ];

        $typed = $map[$normalized] ?? null;

        return in_array($typed, self::ALLOWED_LEXICAL_TYPES, true) ? $typed : null;
    }

    private function normalizeGender(string $value, ?string $lexicalType): ?string
    {
        if ($lexicalType !== 'nom') {
            return null;
        }

        $normalized = Str::of($value)->lower()->ascii()->replace('-', '_')->replace(' ', '_')->trim()->toString();

        return match ($normalized) {
            'masculine', 'masculin', 'm' => 'masculine',
            'feminine', 'feminin', 'f' => 'feminine',
            default => null,
        };
    }

    private function normalizeGroup(string $value, ?string $lexicalType): ?string
    {
        $normalized = Str::of($value)->lower()->ascii()->replace('-', '_')->replace(' ', '_')->trim()->toString();

        if (in_array($normalized, self::ALLOWED_GROUPS, true)) {
            return $normalized;
        }

        return match ($lexicalType) {
            'verbe' => 'action',
            'adjectif' => 'description',
            'locution', 'phrase', 'interjection' => 'sentence',
            'pronom' => 'personal_info',
            default => 'personal_info',
        };
    }

    private function normalizeSubgroup(string $value, ?string $group): ?string
    {
        if (trim($value) === '') {
            return $group;
        }

        $normalized = Str::of($value)
            ->lower()
            ->ascii()
            ->replace('-', '_')
            ->replace(' ', '_')
            ->replaceMatches('/[^a-z0-9_]/', '')
            ->trim('_')
            ->toString();

        if ($normalized === '') {
            return $group;
        }

        return mb_substr($normalized, 0, 50, 'UTF-8');
    }

    private function normalizeConfidence(mixed $value): float
    {
        if (is_numeric($value)) {
            $float = (float) $value;

            return max(0.0, min(1.0, $float));
        }

        return 0.65;
    }
}
