<div class="flex flex-col gap-1.5">
    <label class="{{ $this->filterLabelClasses() }}">{{ $filter->label() }}</label>
    <div class="relative">
        <select
            wire:model.live="filterValues.{{ $filter->key() }}"
            class="{{ $filter->getCssClass() ?? $this->filterSelectClasses() }}"
        >
            <option value="">{{ __('livewire-datatable::livewire-datatable.all') }}</option>
            <option value="1">{{ __('livewire-datatable::livewire-datatable.yes') }}</option>
            <option value="0">{{ __('livewire-datatable::livewire-datatable.no') }}</option>
        </select>
        <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>
