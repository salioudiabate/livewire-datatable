@php
    $colspan = count($columns)
        + (count($this->authorizedBulkActions()) > 0 ? 1 : 0)
        + (count($this->rowActions()) > 0 ? 1 : 0);
@endphp
@if ($this->emptyStateView())
    @include($this->emptyStateView(), ['columns' => $columns, 'colspan' => $colspan])
@else
    <tr>
        <td colspan="{{ $colspan }}" class="{{ $this->emptyStateClasses() }}">
            <p class="text-sm font-medium text-slate-500">{{ __('livewire-datatable::livewire-datatable.no_results') }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ __('livewire-datatable::livewire-datatable.no_results_hint') }}</p>
        </td>
    </tr>
@endif
