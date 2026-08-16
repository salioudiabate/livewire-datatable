<tr>
    @if (count($this->authorizedBulkActions()) > 0)
        <th class="{{ $this->thClasses() }} w-px">
            <input
                type="checkbox"
                wire:model.live="selectAll"
                aria-label="{{ __('livewire-datatable::livewire-datatable.select_all_filtered', ['count' => $this->rows->total()]) }}"
                class="rounded border-slate-300 text-[var(--dt-primary, #4f46e5)] focus:ring-[var(--dt-primary, #4f46e5)]"
            />
        </th>
    @endif

    @foreach ($columns as $column)
        <th class="{{ trim($this->thClasses().' '.$column->getThClass()) }}">
            @if ($column->getThView())
                @include($column->getThView(), ['column' => $column])
            @elseif ($column->isSortable())
                <button
                    type="button"
                    wire:click="sortBy('{{ $column->getField() }}')"
                    class="flex items-center gap-1 hover:text-slate-700"
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
        <th class="{{ $this->thClasses() }} w-px"></th>
    @endif
</tr>
