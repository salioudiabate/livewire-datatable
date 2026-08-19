@php
    $stickyHeaderStyle = $this->stickyHeaderStyle();
    $stickyHeaderActive = $stickyHeaderStyle !== '';
@endphp
<tr>
    @if (count($this->authorizedBulkActions()) > 0)
        @php
            $checkboxStyle = trim(($this->frozenCheckboxStyle($columns) ?? '').' '.$stickyHeaderStyle);
        @endphp
        <th
            class="{{ trim($this->thClasses().' '.$this->densityThClasses().' w-px '.(($this->hasFrozenColumns($columns) || $stickyHeaderActive) ? $this->frozenTheadBackgroundClass() : '')) }}"
            @if ($checkboxStyle !== '') style="{{ $checkboxStyle }}" @endif
        >
            <input
                type="checkbox"
                wire:model.live="selectAll"
                aria-label="{{ __('livewire-datatable::livewire-datatable.select_all_filtered', ['count' => $this->rows->total()]) }}"
                class="rounded border-slate-300 text-[var(--dt-primary,#4f46e5)] focus:ring-[var(--dt-primary,#4f46e5)]"
            />
        </th>
    @endif

    @foreach ($columns as $column)
        @php
            $thStyle = trim(($this->frozenColumnStyle($column, $columns) ?? '').' '.$stickyHeaderStyle);
            $thBgClass = $column->isFrozen()
                ? $this->frozenTheadBackgroundClass().' '.$this->frozenRightEdgeClass($column, $columns)
                : ($stickyHeaderActive ? $this->frozenTheadBackgroundClass() : '');
        @endphp
        <th
            class="{{ trim($this->thClasses().' '.$this->densityThClasses().' '.$column->getThClass().' '.$thBgClass) }}"
            @if ($thStyle !== '') style="{{ $thStyle }}" @endif
        >
            @if ($column->getThView())
                @include($column->getThView(), ['column' => $column])
            @elseif ($column->isSortable())
                <button
                    type="button"
                    wire:click="sortBy('{{ $column->getField() }}')"
                    class="group flex items-center gap-1 uppercase tracking-wide transition-colors duration-150 hover:text-slate-700"
                >
                    {{ $column->getLabel() }}
                    @if ($this->sortField === $column->getField())
                        <svg class="h-3.5 w-3.5 text-[var(--dt-primary,#4f46e5)]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            @if ($this->sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            @endif
                        </svg>
                    @else
                        <span class="flex flex-col -space-y-1 text-slate-400 transition-colors duration-150 group-hover:text-slate-500" aria-hidden="true">
                            <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                            <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </button>
            @else
                {{ $column->getLabel() }}
            @endif
        </th>
    @endforeach

    @if (count($this->rowActions()) > 0)
        <th
            class="{{ trim($this->thClasses().' '.$this->densityThClasses().' w-px '.($stickyHeaderActive ? $this->frozenTheadBackgroundClass() : '')) }}"
            @if ($stickyHeaderActive) style="{{ $stickyHeaderStyle }}" @endif
        ></th>
    @endif
</tr>
