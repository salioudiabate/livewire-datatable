<div class="dt-root {{ $this->rootClasses() }}" x-data="{ filtersOpen: @js($this->filtersDefaultOpen()) }">
    @include('livewire-datatable::components.theme-style')

    @include('livewire-datatable::components.toolbar', ['filters' => $filters])

    @if (count($filters) > 0)
        <div
            x-show="filtersOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mx-4 mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4"
        >
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

    @if ($this->rows->total() > 0)
        <div class="{{ $this->paginationWrapperClasses() }}">
            <span class="text-center text-xs text-slate-400 sm:text-left">
                {{ __('livewire-datatable::livewire-datatable.showing') }}
                <span class="font-semibold text-slate-500">{{ $this->rows->firstItem() }}</span>
                {{ __('livewire-datatable::livewire-datatable.to') }}
                <span class="font-semibold text-slate-500">{{ $this->rows->lastItem() }}</span>
                {{ __('livewire-datatable::livewire-datatable.of') }}
                <span class="font-semibold text-slate-500">{{ $this->rows->total() }}</span>
                {{ __('livewire-datatable::livewire-datatable.results') }}
            </span>

            <div class="flex justify-center sm:justify-end">
                {{ $this->rows->links() }}
            </div>
        </div>
    @endif
</div>
