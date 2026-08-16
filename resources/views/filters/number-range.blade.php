<div class="flex flex-col gap-1">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <div class="flex items-center gap-2">
        <input
            type="number"
            wire:model.live.debounce.300ms="filterValues.{{ $filter->fromKey() }}"
            class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary, #4f46e5)]"
        />
        <span class="text-xs text-slate-400">{{ __('livewire-datatable::livewire-datatable.range_separator') }}</span>
        <input
            type="number"
            wire:model.live.debounce.300ms="filterValues.{{ $filter->toKey() }}"
            class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary, #4f46e5)]"
        />
    </div>
</div>
