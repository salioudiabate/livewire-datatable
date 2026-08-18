<div class="flex flex-col gap-1.5">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <input
        type="text"
        wire:model.live.debounce.300ms="filterValues.{{ $filter->key() }}"
        placeholder="{{ $filter->label() }}"
        class="rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 transition-colors duration-150 placeholder:text-slate-400 hover:border-slate-300 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary, #4f46e5)]"
    />
</div>
