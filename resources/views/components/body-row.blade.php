<tr class="{{ $this->tbodyTrClasses() }}" wire:key="dt-row-{{ data_get($row, 'id') ?? $loop->index }}">
    @if (count($this->authorizedBulkActions()) > 0)
        @php($rowKey = $this->resolveRowKey($row))
        <td
            class="{{ trim($this->tdClasses().' '.$this->densityTdClasses().' '.($this->hasFrozenColumns($columns) ? $this->frozenTbodyBackgroundClass() : '')) }}"
            @if ($style = $this->frozenCheckboxStyle($columns)) style="{{ $style }}" @endif
        >
            <input
                type="checkbox"
                wire:model.live="selected"
                value="{{ $rowKey }}"
                wire:key="dt-checkbox-{{ $rowKey }}"
                class="rounded border-slate-300 text-[var(--dt-primary, #4f46e5)] focus:ring-[var(--dt-primary, #4f46e5)]"
            />
        </td>
    @endif

    @foreach ($columns as $column)
        <td
            class="{{ trim($this->tdClasses().' '.$this->densityTdClasses().' '.($column->isFrozen() ? $this->frozenTbodyBackgroundClass().' '.$this->frozenRightEdgeClass($column, $columns) : '')) }}"
            @if ($style = $this->frozenColumnStyle($column, $columns)) style="{{ $style }}" @endif
        >
            @if ($column->getView())
                @include($column->getView(), ['row' => $row, 'value' => data_get($row, $column->getField())])
            @else
                {{ $column->renderValue(data_get($row, $column->getField()), $row) }}
            @endif
        </td>
    @endforeach

    @if (count($this->rowActions()) > 0)
        <td class="{{ trim($this->tdClasses().' '.$this->densityTdClasses()) }}">
            @include('livewire-datatable::row-actions.dropdown', [
                'actions' => $this->visibleRowActions($row),
                'row' => $row,
            ])
        </td>
    @endif
</tr>
