<div class="flex flex-col gap-1.5">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <div class="relative">
        <select
            wire:model.live="filterValues.{{ $filter->key() }}"
            class="w-full appearance-none bg-none rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm text-slate-700 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
        >
            <option value="">{{ __('livewire-datatable::livewire-datatable.all') }}</option>
            @foreach ($filter->getOptions() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>
