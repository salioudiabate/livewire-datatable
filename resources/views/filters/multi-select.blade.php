<div class="flex flex-col gap-1">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <select
        multiple
        wire:model.live="filterValues.{{ $filter->key() }}"
        class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary, #4f46e5)]"
    >
        @foreach ($filter->getOptions() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
