@php
    $summary = $summary ?? [];
    $downloading = (int) ($summary['downloading'] ?? 0);
    $pending = (int) ($summary['pending'] ?? 0);
    $queuedJobs = (int) ($summary['queued_jobs'] ?? 0);
    $queueWaiting = (bool) ($summary['queue_waiting'] ?? false);
    $activeTransfer = (bool) ($summary['active_transfer'] ?? false);
    $progress = max(0, min(100, (int) ($summary['progress'] ?? 0)));
    $activeDownloadedKb = (float) ($summary['active_downloaded_kb'] ?? 0);
    $allDownloaded = (bool) ($summary['all_downloaded'] ?? false);
    $statusMessage = (string) ($summary['status_message'] ?? 'Ready.');
    $workflowQueue = (string) ($summary['workflow_queue'] ?? '');

    $formatKb = static function (float $kb): string {
        if ($kb >= 1024 * 1024) {
            return number_format($kb / (1024 * 1024), 2) . ' GB';
        }

        if ($kb >= 1024) {
            return number_format($kb / 1024, 2) . ' MB';
        }

        return number_format($kb, 1) . ' KB';
    };

    $activeDownloadHuman = $formatKb($activeDownloadedKb);

    $statusTone = 'gray';
    if ($allDownloaded) {
        $statusTone = 'success';
    } elseif ($activeTransfer || $queueWaiting) {
        $statusTone = 'warning';
    }
@endphp

<div class="space-y-4" wire:poll.2s>
    <div
        @class([
            'rounded-xl border p-4',
            'border-success-300 bg-success-50 text-success-800' => $statusTone === 'success',
            'border-warning-300 bg-warning-50 text-warning-800' => $statusTone === 'warning',
            'border-gray-200 bg-gray-50 text-gray-800' => $statusTone === 'gray',
        ])
    >
        <div class="flex items-start justify-between gap-3">
            @if ($activeTransfer)
                <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-warning-500 animate-pulse"></span>
            @elseif ($queueWaiting)
                <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-warning-500"></span>
            @elseif ($allDownloaded)
                <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-success-500"></span>
            @else
                <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-gray-400"></span>
            @endif
            <div class="flex-1">
                <p class="text-xs font-medium uppercase tracking-wide opacity-80">Current Status</p>
                <p class="text-sm font-semibold">{{ $statusMessage }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium uppercase tracking-wide opacity-80">Queue</p>
                <p class="text-sm font-semibold">{{ $workflowQueue !== '' ? $workflowQueue : '-' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="rounded-lg border border-warning-200 bg-warning-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-warning-800">Active Downloads</p>
            <p class="mt-1 text-2xl font-semibold text-warning-800">{{ $downloading }}</p>
            <p class="mt-1 text-xs text-warning-800">
                Transferred so far: {{ $activeDownloadHuman }}
                @if ($activeTransfer && $activeDownloadedKb > 0)
                    (live)
                @endif
            </p>
            <p class="mt-1 text-[11px] text-warning-800/80">
                This is partial bytes written for the current active download.
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-700">Pending</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $pending }}</p>
            <p class="mt-1 text-xs text-gray-600">Queued jobs: {{ $queuedJobs }}</p>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="mb-2 flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Overall Progress</span>
            <span class="text-sm font-semibold text-gray-800">{{ $progress }}%</span>
        </div>
        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
            <div
                class="h-full transition-all duration-500 {{ $allDownloaded ? 'bg-success-500' : 'bg-primary-500' }}"
                style="width: {{ $progress }}%;"
            ></div>
        </div>
        @if ($allDownloaded)
            <p class="mt-2 text-sm font-semibold text-success-700">All downloaded.</p>
        @endif
    </div>

    @if ($queueWaiting)
        <div class="rounded-lg border border-warning-300 bg-warning-50 p-3 text-warning-800">
            <p class="text-xs font-semibold uppercase tracking-wide">Action Required</p>
            <p class="mt-1 text-sm">Queue worker appears inactive. Start it to continue downloads:</p>
            <code class="mt-2 block rounded bg-white px-2 py-1 text-[11px] text-gray-800">php artisan queue:work --queue={{ $workflowQueue }},default --tries=3 --timeout=900</code>
        </div>
    @endif
</div>
