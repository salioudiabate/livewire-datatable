<div class="flex flex-col gap-1.5">
    <label class="{{ $this->filterLabelClasses() }}">{{ $filter->label() }}</label>
    <input
        type="text"
        wire:model.live.debounce.300ms="filterValues.{{ $filter->key() }}"
        placeholder="{{ $filter->label() }}"
        class="{{ $filter->getCssClass() ?? $this->filterInputClasses() }}"
    />
</div>
