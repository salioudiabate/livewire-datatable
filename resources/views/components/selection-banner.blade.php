@if ($this->selectAll && ! $this->isAllFilteredSelected())
    <div
        class="mx-4 mb-4 flex flex-wrap items-center gap-2 rounded-xl border px-4 py-2.5 text-sm text-slate-700"
        style="border-color: var(--dt-primary, #4f46e5); background-color: var(--dt-primary-light, #eef2ff);"
    >
        <span>{{ __('livewire-datatable::livewire-datatable.page_selected', ['count' => count($this->selected)]) }}</span>
        <button
            type="button"
            wire:click="selectAllFiltered"
            class="font-semibold text-[var(--dt-primary, #4f46e5)] transition-colors duration-150 hover:underline"
        >
            {{ __('livewire-datatable::livewire-datatable.select_all_filtered', ['count' => $this->rows->total()]) }}
        </button>
    </div>
@elseif ($this->isAllFilteredSelected() && $this->rows->total() > $this->rows->count())
    <div
        class="mx-4 mb-4 rounded-xl border px-4 py-2.5 text-sm text-slate-700"
        style="border-color: var(--dt-primary, #4f46e5); background-color: var(--dt-primary-light, #eef2ff);"
    >
        <span>{{ __('livewire-datatable::livewire-datatable.all_filtered_selected', ['count' => count($this->selected)]) }}</span>
        <button
            type="button"
            wire:click="clearSelection"
            class="font-semibold text-[var(--dt-primary, #4f46e5)] transition-colors duration-150 hover:underline"
        >
            {{ __('livewire-datatable::livewire-datatable.clear_selection') }}
        </button>
    </div>
@endif
