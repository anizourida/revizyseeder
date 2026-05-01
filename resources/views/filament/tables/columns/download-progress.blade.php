@php
    $payload = $getState() ?? [];
    $state = (string) ($payload['state'] ?? 'pending');
    $progress = (int) ($payload['progress'] ?? 0);

    $barColor = match ($state) {
        'failed' => 'rgb(239 68 68)',
        'downloaded' => 'rgb(34 197 94)',
        default => 'rgb(59 130 246)',
    };

    $trackColor = match ($state) {
        'failed' => 'rgb(254 226 226)',
        default => 'rgb(226 232 240)',
    };
@endphp

<div style="min-width: 140px;">
    <div style="height: 8px; width: 100%; background: {{ $trackColor }}; border-radius: 9999px; overflow: hidden;">
        <div
            style="height: 100%; width: {{ $progress }}%; background: {{ $barColor }}; transition: width 0.35s ease;"
        ></div>
    </div>
    <div style="margin-top: 4px; font-size: 11px; color: rgb(100 116 139); text-align: right;">
        {{ $progress }}%
    </div>
</div>
