<div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-4">
    <div class="flex flex-1 flex-wrap items-center gap-3">
        @if ($this->showSearch())
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('livewire-datatable::livewire-datatable.search') }}"
                class="w-full max-w-xs rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-700 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary, #4f46e5)]"
            />
        @endif

        @if (count($filters) > 0)
            <button
                type="button"
                x-on:click="filtersOpen = ! filtersOpen"
                class="flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-[var(--dt-primary-light, #eef2ff)]"
            >
                {{ __('livewire-datatable::livewire-datatable.filters') }}
                @if ($this->activeFilterCount() > 0)
                    <span class="rounded-full bg-[var(--dt-primary, #4f46e5)] px-1.5 py-0.5 text-xs text-[var(--dt-primary-text, #ffffff)]">
                        {{ $this->activeFilterCount() }}
                    </span>
                @endif
            </button>

            @if ($this->hasActiveFilters())
                <button type="button" wire:click="resetFilters" class="text-sm text-slate-400 hover:text-slate-600">
                    {{ __('livewire-datatable::livewire-datatable.reset_filters') }}
                </button>
            @endif
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-3">
        @include('livewire-datatable::components.column-visibility-toggle')

        @if ($this->showExport())
            <button
                type="button"
                wire:click="export"
                wire:loading.attr="disabled"
                class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-[var(--dt-primary-light, #eef2ff)]"
            >
                {{ __('livewire-datatable::livewire-datatable.export') }}
            </button>
        @endif

        @if ($this->showDensityToggle())
            <div class="flex items-center overflow-hidden rounded-lg border border-slate-200" role="group" aria-label="{{ __('livewire-datatable::livewire-datatable.density') }}">
                @foreach ($this->densityOptions() as $option)
                    <button
                        type="button"
                        wire:click="setDensity('{{ $option }}')"
                        aria-pressed="{{ $this->density === $option ? 'true' : 'false' }}"
                        title="{{ __('livewire-datatable::livewire-datatable.density_'.$option) }}"
                        class="px-2 py-1.5 text-xs {{ $this->density === $option ? 'bg-[var(--dt-primary, #4f46e5)] text-[var(--dt-primary-text, #ffffff)]' : 'text-slate-500 hover:bg-[var(--dt-primary-light, #eef2ff)]' }}"
                    >
                        {{ __('livewire-datatable::livewire-datatable.density_'.$option) }}
                    </button>
                @endforeach
            </div>
        @endif

        @if ($this->showPerPage())
            <select
                wire:model.live="perPage"
                aria-label="{{ __('livewire-datatable::livewire-datatable.per_page') }}"
                class="rounded-lg border border-slate-200 px-2 py-1.5 text-sm text-slate-600 focus:border-[var(--dt-primary, #4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary, #4f46e5)]"
            >
                @foreach ($this->perPageOptions() as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        @endif
    </div>
</div>
