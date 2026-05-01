@php
    $classification = implode(' / ', array_values(array_filter([
        trim((string) ($record->lexical_type ?? '')),
        trim((string) ($record->gender ?? '')),
        trim((string) ($record->distractor_group ?? '')),
    ])));

    $jsonPayload = json_encode(
        $questions,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ) ?: '[]';

    $imagePreviewMap = is_array($imagePreviewMap ?? null) ? $imagePreviewMap : [];
    $audioPreviewMap = is_array($audioPreviewMap ?? null) ? $audioPreviewMap : [];

    $formatMedia = static function ($media): string {
        if (!is_array($media)) {
            return '-';
        }

        $image = trim((string) ($media['image'] ?? ''));
        $audio = trim((string) ($media['audio'] ?? ''));
        $parts = [];

        if ($image !== '') {
            $parts[] = 'img: '.$image;
        }

        if ($audio !== '') {
            $parts[] = 'audio: '.$audio;
        }

        return $parts === [] ? '-' : implode(' | ', $parts);
    };

    $renderColorTaggedText = static function (?string $text): string {
        $value = trim((string) $text);
        if ($value === '') {
            return '-';
        }

        $escaped = e($value);

        $escaped = preg_replace_callback(
            '/\[(BLUE|RED|GREEN|PINK)\]([\s\S]*?)\[\/\1\]/i',
            static function (array $matches): string {
                $color = strtoupper((string) ($matches[1] ?? ''));
                $content = (string) ($matches[2] ?? '');

                $style = match ($color) {
                    'BLUE' => 'color: #2E76B6; font-weight: 600;',
                    'RED' => 'color: #AF0A54; font-weight: 600;',
                    'GREEN' => 'color: #00AAA4; font-weight: 600;',
                    'PINK' => 'color: #DC03A2; font-weight: 600;',
                    default => '',
                };

                return '<span style="'.$style.'">'.$content.'</span>';
            },
            $escaped
        );

        return nl2br((string) $escaped, false);
    };
@endphp

<div class="space-y-4">
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-800">
        <div><strong>Word:</strong> {{ $record->word }}</div>
        <div><strong>Scope:</strong> {{ $record->grade }} / {{ $record->period }} / {{ $record->week }}</div>
        <div><strong>Type / Gender / Group:</strong> {{ $classification !== '' ? $classification : '-' }}</div>
        <div><strong>Mode:</strong> Standard only (`universal`)</div>
    </div>

    @if ($error)
        <div class="rounded-xl border border-danger-300 bg-danger-50 p-3 text-sm text-danger-700">
            {{ $error }}
        </div>
    @elseif ($questions === [])
        <div class="rounded-xl border border-warning-300 bg-warning-50 p-3 text-sm text-warning-700">
            No standard questions generated for this item.
        </div>
    @else
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <div class="mb-2 text-sm font-semibold text-gray-900">
                Generated {{ count($questions) }} standard question(s)
            </div>
            <div class="max-h-[28rem] space-y-3 overflow-y-auto">
                @foreach ($questions as $index => $question)
                    @php
                        $questionData = is_array($question['data'] ?? null) ? $question['data'] : [];
                        $answers = is_array($questionData['answers'] ?? null) ? $questionData['answers'] : [];
                        $answersCount = count($answers);
                        $questionBody = trim((string) ($questionData['body'] ?? ''));
                        $instruction = trim((string) ($questionData['instruction'] ?? ''));
                        $questionMedia = $formatMedia($questionData['media'] ?? null);
                        $questionImageId = trim((string) ($questionData['media']['image'] ?? ''));
                        $questionAudioId = trim((string) ($questionData['media']['audio'] ?? ''));
                        $questionImageUrl = $questionImageId !== '' ? ($imagePreviewMap[$questionImageId] ?? null) : null;
                        $questionAudioUrl = $questionAudioId !== '' ? ($audioPreviewMap[$questionAudioId] ?? null) : null;
                    @endphp
                    <div class="rounded-lg border border-gray-200 p-3">
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $index + 1 }}. {{ $question['name'] ?? 'Untitled Question' }}
                        </div>
                        <div class="mt-1 text-xs text-gray-600">
                            type: {{ $question['type'] ?? '-' }} | answers: {{ $answersCount }}
                        </div>
                        <div class="mt-2 space-y-1 text-xs text-gray-700">
                            <div><strong>Instruction:</strong> {{ $instruction !== '' ? $instruction : '-' }}</div>
                            <div>
                                <strong>Question Body:</strong>
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                                    {!! $renderColorTaggedText($questionBody) !!}
                                </span>
                            </div>
                            <div><strong>Question Media:</strong> {{ $questionMedia }}</div>
                        </div>
                        @if ($questionImageId !== '' || $questionAudioId !== '')
                            <div class="mt-2 rounded-md border border-gray-100 bg-gray-50 p-2 space-y-2">
                                @if ($questionImageId !== '')
                                    <div class="text-xs text-gray-700">
                                        <strong>Image Preview ({{ $questionImageId }}):</strong>
                                    </div>
                                    @if ($questionImageUrl)
                                        <img src="{{ $questionImageUrl }}" alt="Question image preview" class="rounded border border-gray-200 object-contain" style="width: 120px; height: 120px;">
                                    @else
                                        <div class="text-xs text-gray-500">Image preview not available locally.</div>
                                    @endif
                                @endif

                                @if ($questionAudioId !== '')
                                    <div class="text-xs text-gray-700">
                                        <strong>Audio Preview ({{ $questionAudioId }}):</strong>
                                    </div>
                                    @if ($questionAudioUrl)
                                        <audio controls preload="none" class="w-full">
                                            <source src="{{ $questionAudioUrl }}">
                                        </audio>
                                    @else
                                        <div class="text-xs text-gray-500">Audio preview not available locally.</div>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <div class="mt-3 rounded-md border border-gray-100 bg-gray-50 p-2">
                            <div class="mb-2 text-xs font-semibold text-gray-700">Answers</div>
                            <div class="space-y-2">
                                @foreach ($answers as $answerIndex => $answer)
                                    @php
                                        $answerBody = trim((string) ($answer['body'] ?? ''));
                                        $answerMedia = $formatMedia($answer['media'] ?? null);
                                        $isCorrect = (bool) ($answer['is_correct'] ?? false);
                                        $answerImageId = trim((string) ($answer['media']['image'] ?? ''));
                                        $answerAudioId = trim((string) ($answer['media']['audio'] ?? ''));
                                        $answerImageUrl = $answerImageId !== '' ? ($imagePreviewMap[$answerImageId] ?? null) : null;
                                        $answerAudioUrl = $answerAudioId !== '' ? ($audioPreviewMap[$answerAudioId] ?? null) : null;
                                    @endphp
                                    <div class="rounded border border-gray-200 bg-white p-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-medium text-gray-800">#{{ $answerIndex + 1 }}</span>
                                            <span class="{{ $isCorrect ? 'text-green-700' : 'text-red-700' }}">
                                                {{ $isCorrect ? 'Correct' : 'Wrong' }}
                                            </span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-700">
                                            <div>
                                                <strong>Body:</strong>
                                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {!! $renderColorTaggedText($answerBody) !!}
                                                </span>
                                            </div>
                                            <div><strong>Media:</strong> {{ $answerMedia }}</div>
                                        </div>
                                        @if ($answerImageId !== '' || $answerAudioId !== '')
                                            <div class="mt-2 space-y-2">
                                                @if ($answerImageId !== '')
                                                    <div class="text-xs text-gray-700">
                                                        <strong>Image Preview ({{ $answerImageId }}):</strong>
                                                    </div>
                                                    @if ($answerImageUrl)
                                                        <img src="{{ $answerImageUrl }}" alt="Answer image preview" class="rounded border border-gray-200 object-contain" style="width: 120px; height: 120px;">
                                                    @else
                                                        <div class="text-xs text-gray-500">Image preview not available locally.</div>
                                                    @endif
                                                @endif

                                                @if ($answerAudioId !== '')
                                                    <div class="text-xs text-gray-700">
                                                        <strong>Audio Preview ({{ $answerAudioId }}):</strong>
                                                    </div>
                                                    @if ($answerAudioUrl)
                                                        <audio controls preload="none" class="w-full">
                                                            <source src="{{ $answerAudioUrl }}">
                                                        </audio>
                                                    @else
                                                        <div class="text-xs text-gray-500">Audio preview not available locally.</div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-xs font-medium text-gray-600">Raw JSON</label>
            <textarea
                readonly
                rows="18"
                class="w-full rounded-lg border border-gray-300 bg-white p-3 font-mono text-xs text-black"
                style="background-color: #ffffff; color: #000000;"
            >{{ $jsonPayload }}</textarea>
        </div>
    @endif
</div>
