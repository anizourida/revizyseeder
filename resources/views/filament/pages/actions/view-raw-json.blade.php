<div class="px-4 py-6 bg-gray-50 dark:bg-gray-800 rounded-lg overflow-y-auto max-h-[70vh] text-sm">
    @php
        $lines = is_string($data) ? preg_split('/\r?\n/', $data) : [];
    @endphp

    @if(count($lines) === 0 || (count($lines) === 1 && trim($lines[0]) === ''))
        <p class="text-gray-400 dark:text-gray-500 italic">No raw data available.</p>
    @else
        <div class="space-y-2">
            @foreach($lines as $i => $line)
                @if(trim($line) !== '')
                    <div class="flex items-start gap-3 py-1.5 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                        <span class="text-gray-400 dark:text-gray-500 text-xs font-mono select-none min-w-[24px] text-right mt-0.5">{{ $i + 1 }}</span>
                        <span class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $line }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
