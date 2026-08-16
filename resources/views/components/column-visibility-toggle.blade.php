@if (count($this->toggleableColumns()) > 0)
    <div x-data="{ columnsOpen: false }" class="relative">
        <button
            type="button"
            x-on:click="columnsOpen = ! columnsOpen"
            class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-[var(--dt-primary-light, #eef2ff)]"
        >
            {{ __('livewire-datatable::livewire-datatable.columns') }}
        </button>

        <div
            x-show="columnsOpen"
            x-cloak
            x-on:click.outside="columnsOpen = false"
            class="absolute right-0 z-10 mt-2 w-48 rounded-lg border border-slate-200 bg-white p-2 shadow-lg"
        >
            @foreach ($this->toggleableColumns() as $column)
                <label class="flex items-center gap-2 px-2 py-1 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        wire:click="toggleColumnVisibility('{{ $column->getField() }}')"
                        @checked(! $this->isColumnHidden($column->getField()))
                        class="rounded border-slate-300 text-[var(--dt-primary, #4f46e5)] focus:ring-[var(--dt-primary, #4f46e5)]"
                    />
                    {{ $column->getLabel() }}
                </label>
            @endforeach
        </div>
    </div>
@endif
