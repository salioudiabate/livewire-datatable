<div class="flex flex-col gap-1.5">
    <label class="{{ $this->filterLabelClasses() }}">{{ $filter->label() }}</label>
    <select
        multiple
        wire:model.live="filterValues.{{ $filter->key() }}"
        class="{{ $filter->getCssClass() ?? $this->filterMultiSelectClasses() }}"
    >
        @foreach ($filter->getOptions() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
