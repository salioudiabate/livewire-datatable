<tr>
    @if (count($this->authorizedBulkActions()) > 0)
        <th
            class="{{ trim($this->thClasses().' '.$this->densityThClasses().' w-px '.($this->hasFrozenColumns($columns) ? $this->frozenTheadBackgroundClass() : '')) }}"
            @if ($style = $this->frozenCheckboxStyle($columns)) style="{{ $style }}" @endif
        >
            <input
                type="checkbox"
                wire:model.live="selectAll"
                aria-label="{{ __('livewire-datatable::livewire-datatable.select_all_filtered', ['count' => $this->rows->total()]) }}"
                class="rounded border-slate-300 text-[var(--dt-primary, #4f46e5)] focus:ring-[var(--dt-primary, #4f46e5)]"
            />
        </th>
    @endif

    @foreach ($columns as $column)
        <th
            class="{{ trim($this->thClasses().' '.$this->densityThClasses().' '.$column->getThClass().' '.($column->isFrozen() ? $this->frozenTheadBackgroundClass().' '.$this->frozenRightEdgeClass($column, $columns) : '')) }}"
            @if ($style = $this->frozenColumnStyle($column, $columns)) style="{{ $style }}" @endif
        >
            @if ($column->getThView())
                @include($column->getThView(), ['column' => $column])
            @elseif ($column->isSortable())
                <button
                    type="button"
                    wire:click="sortBy('{{ $column->getField() }}')"
                    class="flex items-center gap-1 uppercase tracking-wide transition-colors duration-150 hover:text-slate-700"
                >
                    {{ $column->getLabel() }}
                    @if ($this->sortField === $column->getField())
                        <span class="text-[var(--dt-primary, #4f46e5)]">{{ $this->sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
            @else
                {{ $column->getLabel() }}
            @endif
        </th>
    @endforeach

    @if (count($this->rowActions()) > 0)
        <th class="{{ trim($this->thClasses().' '.$this->densityThClasses()) }} w-px"></th>
    @endif
</tr>
