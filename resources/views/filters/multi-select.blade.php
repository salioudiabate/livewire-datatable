<div class="flex flex-col gap-1.5">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <select
        multiple
        wire:model.live="filterValues.{{ $filter->key() }}"
        class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary, #4f46e5)]"
    >
        @foreach ($filter->getOptions() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
