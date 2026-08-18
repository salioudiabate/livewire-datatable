@if ($this->selectAll && ! $this->isAllFilteredSelected())
    <div class="{{ $this->selectionBannerClasses() }}">
        <span>{{ __('livewire-datatable::livewire-datatable.page_selected', ['count' => count($this->selected)]) }}</span>
        <button
            type="button"
            wire:click="selectAllFiltered"
            class="font-semibold text-[var(--dt-primary,#4f46e5)] transition-colors duration-150 hover:underline"
        >
            {{ __('livewire-datatable::livewire-datatable.select_all_filtered', ['count' => $this->rows->total()]) }}
        </button>
    </div>
@elseif ($this->isAllFilteredSelected() && $this->rows->total() > $this->rows->count())
    <div class="{{ $this->selectionBannerClasses() }}">
        <span>{{ __('livewire-datatable::livewire-datatable.all_filtered_selected', ['count' => count($this->selected)]) }}</span>
        <button
            type="button"
            wire:click="clearSelection"
            class="font-semibold text-[var(--dt-primary,#4f46e5)] transition-colors duration-150 hover:underline"
        >
            {{ __('livewire-datatable::livewire-datatable.clear_selection') }}
        </button>
    </div>
@endif
