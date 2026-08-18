<div class="flex flex-col gap-1.5">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <div class="flex items-center gap-2">
        <input
            type="date"
            wire:model.live="filterValues.{{ $filter->fromKey() }}"
            class="w-full min-w-0 rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
        />
        <span class="shrink-0 text-xs text-slate-400">{{ __('livewire-datatable::livewire-datatable.range_separator') }}</span>
        <input
            type="date"
            wire:model.live="filterValues.{{ $filter->toKey() }}"
            class="w-full min-w-0 rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
        />
    </div>
</div>
