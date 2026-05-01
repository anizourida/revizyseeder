<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-3">
            <x-filament::button type="submit" icon="heroicon-o-check-circle">
                Save Credentials
            </x-filament::button>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Tip: paste the full cURL request from browser network tab.
            </p>
        </div>
    </form>
</x-filament-panels::page>
