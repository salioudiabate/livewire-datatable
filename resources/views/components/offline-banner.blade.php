@if ($this->showOfflineIndicator())
    <div wire:offline class="{{ $this->offlineBannerClasses() }}">
        <span class="relative flex h-2.5 w-2.5 shrink-0">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
        </span>

        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M8.5 16.5a5 5 0 016.6-.35M5.5 13a9 9 0 013.5-2.03M12 20h.01M18.5 13a8.96 8.96 0 00-1.8-1.7M2 8.5a13 13 0 015.5-3" />
        </svg>

        <div>
            <p class="font-medium">{{ __('livewire-datatable::livewire-datatable.offline') }}</p>
            <p class="text-xs text-amber-600">{{ __('livewire-datatable::livewire-datatable.offline_hint') }}</p>
        </div>
    </div>
@endif
