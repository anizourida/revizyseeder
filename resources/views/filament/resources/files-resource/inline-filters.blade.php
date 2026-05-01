@php
    $gradeOptions = $gradeOptions ?? [];
    $periodOptions = $periodOptions ?? [];
    $weekOptions = $weekOptions ?? [];
@endphp

<div class="w-full">
    <div class="grid gap-3 md:grid-cols-3">
        <label class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Grade</span>
            <select
                wire:model.live="tableFilters.grade_code.value"
                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-gray-900 dark:text-white"
            >
                <option value="">All</option>
                @foreach ($gradeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Period</span>
            <select
                wire:model.live="tableFilters.period_code.value"
                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-gray-900 dark:text-white"
            >
                <option value="">All</option>
                @foreach ($periodOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1">
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Week</span>
            <select
                wire:model.live="tableFilters.week_code.value"
                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-gray-900 dark:text-white"
            >
                <option value="">All</option>
                @foreach ($weekOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </div>
</div>
