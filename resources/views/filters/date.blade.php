<div class="flex flex-col gap-1.5">
    <label class="{{ $this->filterLabelClasses() }}">{{ $filter->label() }}</label>
    <input
        type="date"
        wire:model.live="filterValues.{{ $filter->key() }}"
        class="{{ $filter->getCssClass() ?? $this->filterInputClasses() }}"
    />
</div>
