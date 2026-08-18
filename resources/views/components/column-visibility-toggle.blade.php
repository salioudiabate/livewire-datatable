@if (count($this->toggleableColumns()) > 0)
    <div x-data="{ columnsOpen: false }" class="relative">
        <button
            type="button"
            x-on:click="columnsOpen = ! columnsOpen"
            class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
        >
            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            {{ __('livewire-datatable::livewire-datatable.columns') }}
        </button>

        <div
            x-show="columnsOpen"
            x-cloak
            x-on:click.outside="columnsOpen = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="{{ $this->columnsDropdownClasses() }}"
        >
            @foreach ($this->toggleableColumns() as $column)
                <label class="flex items-center gap-2 px-3 py-1.5 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50">
                    <input
                        type="checkbox"
                        wire:click="toggleColumnVisibility('{{ $column->getField() }}')"
                        @checked(! $this->isColumnHidden($column->getField()))
                        class="rounded border-slate-300 text-[var(--dt-primary,#4f46e5)] focus:ring-[var(--dt-primary,#4f46e5)]"
                    />
                    {{ $column->getLabel() }}
                </label>
            @endforeach
        </div>
    </div>
@endif
