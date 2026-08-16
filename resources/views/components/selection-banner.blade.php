@if ($this->selectAll && ! $this->isAllFilteredSelected())
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-[var(--dt-primary-light, #eef2ff)] px-4 py-2 text-sm text-slate-700">
        <span>{{ __('livewire-datatable::livewire-datatable.page_selected', ['count' => count($this->selected)]) }}</span>
        <button
            type="button"
            wire:click="selectAllFiltered"
            class="font-medium text-[var(--dt-primary, #4f46e5)] underline"
        >
            {{ __('livewire-datatable::livewire-datatable.select_all_filtered', ['count' => $this->rows->total()]) }}
        </button>
    </div>
@elseif ($this->isAllFilteredSelected() && $this->rows->total() > $this->rows->count())
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-[var(--dt-primary-light, #eef2ff)] px-4 py-2 text-sm text-slate-700">
        <span>{{ __('livewire-datatable::livewire-datatable.all_filtered_selected', ['count' => count($this->selected)]) }}</span>
        <button
            type="button"
            wire:click="clearSelection"
            class="font-medium text-[var(--dt-primary, #4f46e5)] underline"
        >
            {{ __('livewire-datatable::livewire-datatable.clear_selection') }}
        </button>
    </div>
@endif
