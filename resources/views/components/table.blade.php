<div class="dt-root" x-data="{ filtersOpen: @js($this->filtersDefaultOpen()) }">
    @include('livewire-datatable::components.theme-style')

    @include('livewire-datatable::components.toolbar', ['filters' => $filters])

    @if (count($filters) > 0)
        <div x-show="filtersOpen" x-cloak class="border-b border-slate-200 bg-slate-50 p-4">
            @include('livewire-datatable::components.filters-panel', ['filters' => $filters])
        </div>
    @endif

    @include('livewire-datatable::components.bulk-actions-bar')
    @include('livewire-datatable::components.selection-banner')

    <div class="{{ $this->tableWrapperClasses() }}">
        <table class="{{ $this->tableClasses() }}">
            <thead class="{{ $this->theadTrClasses() }}">
                @include('livewire-datatable::components.header-row', ['columns' => $columns])
            </thead>
            <tbody>
                @forelse ($this->rows as $row)
                    @include('livewire-datatable::components.body-row', ['columns' => $columns, 'row' => $row])
                @empty
                    @include('livewire-datatable::components.empty-state', ['columns' => $columns])
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->rows->hasPages())
        <div class="{{ $this->paginationWrapperClasses() }}">
            {{ $this->rows->links() }}
        </div>
    @endif
</div>
