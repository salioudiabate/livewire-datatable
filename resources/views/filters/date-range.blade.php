<div class="flex flex-col gap-1.5">
    <label class="{{ $this->filterLabelClasses() }}">{{ $filter->label() }}</label>
    <div class="flex items-center gap-2">
        <input
            type="date"
            wire:model.live="filterValues.{{ $filter->fromKey() }}"
            class="{{ $filter->getCssClass() ?? $this->filterInputClasses() }}"
        />
        <span class="shrink-0 text-xs text-slate-400">{{ __('livewire-datatable::livewire-datatable.range_separator') }}</span>
        <input
            type="date"
            wire:model.live="filterValues.{{ $filter->toKey() }}"
            class="{{ $filter->getCssClass() ?? $this->filterInputClasses() }}"
        />
    </div>
</div>
