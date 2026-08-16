<div class="flex flex-col gap-1">
    <label class="text-xs font-medium text-slate-600">{{ $filter->label() }}</label>
    <select
        wire:model.live="filterValues.{{ $filter->key() }}"
        class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary, #4f46e5)]"
    >
        <option value="">{{ __('livewire-datatable::livewire-datatable.all') }}</option>
        <option value="1">{{ __('livewire-datatable::livewire-datatable.yes') }}</option>
        <option value="0">{{ __('livewire-datatable::livewire-datatable.no') }}</option>
    </select>
</div>
