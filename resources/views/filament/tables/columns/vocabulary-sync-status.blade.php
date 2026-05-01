@php
    /** @var \App\Models\Raiida\VocabularyItem $record */
    $record = $getRecord();

    $ri = trim((string) $record->revizy_image_file_id) !== '';
    $ra = trim((string) $record->revizy_audio_file_id) !== '';
    $wi = trim((string) $record->walidio_image_id) !== '';
    $walidioConfigured = trim((string) config('raiida.walidio.public_key', '')) !== ''
        && trim((string) config('raiida.walidio.base_url', '')) !== '';
    $wiConfigMissing = ! $walidioConfigured && ! $wi;
    $wiBlocked = ! $ri && ! $wi;
@endphp

<div class="flex items-center gap-3 text-xs whitespace-nowrap">
    <span class="inline-flex items-center gap-1" title="{{ $ri ? 'Revizy Image uploaded' : 'Revizy Image missing' }}">
        @if ($ri)
            <x-heroicon-o-photo class="w-4 h-4 text-success-600 dark:text-success-400" />
        @else
            <x-heroicon-o-photo class="w-4 h-4 text-gray-400 dark:text-gray-500" />
        @endif
    </span>

    <span class="inline-flex items-center gap-1" title="{{ $ra ? 'Revizy Audio uploaded' : 'Revizy Audio missing' }}">
        @if ($ra)
            <x-heroicon-o-speaker-wave class="w-4 h-4 text-success-600 dark:text-success-400" />
        @else
            <x-heroicon-o-speaker-wave class="w-4 h-4 text-gray-400 dark:text-gray-500" />
        @endif
    </span>

    <span class="inline-flex items-center gap-1" title="{{ $wi ? 'Walidio image uploaded' : ($wiConfigMissing ? 'WALIDIO_PUBLIC_KEY is not configured' : ($wiBlocked ? 'Blocked: sync image to Revizy first' : 'Walidio image missing')) }}">
        @if ($wi)
            <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-success-600 dark:text-success-400" />
        @else
            <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-gray-400 dark:text-gray-500" />
        @endif
    </span>
</div>
