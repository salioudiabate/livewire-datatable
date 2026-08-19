<div class="{{ $this->toolbarClasses() }}">
    <div class="flex flex-1 flex-wrap items-start gap-2.5">
        @if ($this->showSearch())
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('livewire-datatable::livewire-datatable.search') }}"
                    class="w-full max-w-xs rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-700 transition-colors duration-150 placeholder:text-slate-400 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
                />
            </div>
        @endif

        @if (count($filters) > 0)
            <button
                type="button"
                x-on:click="filtersOpen = ! filtersOpen"
                class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
            >
                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                {{ __('livewire-datatable::livewire-datatable.filters') }}
                @if ($this->activeFilterCount() > 0)
                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-[var(--dt-primary,#4f46e5)] px-1 text-xs font-medium text-[var(--dt-primary-text,#ffffff)]">
                        {{ $this->activeFilterCount() }}
                    </span>
                @endif
            </button>

            @if ($this->hasActiveFilters())
                <button type="button" wire:click="resetFilters" class="text-sm text-slate-400 transition-colors duration-150 hover:text-slate-600">
                    {{ __('livewire-datatable::livewire-datatable.reset_filters') }}
                </button>
            @endif
        @endif

        @foreach ($this->toolbarActions() as $item)
            @if ($item instanceof \Salioudiabate\LivewireDatatable\ToolbarActionGroup && $item->getAlign() === 'left')
                @include('livewire-datatable::components.toolbar-action-group', ['group' => $item])
            @elseif ($item instanceof \Salioudiabate\LivewireDatatable\ToolbarAction && $item->getAlign() === 'left' && $item->isAuthorized())
                @include('livewire-datatable::components.toolbar-action', ['action' => $item, 'grouped' => false])
            @endif
        @endforeach
    </div>

    <div class="flex flex-wrap items-start gap-2.5">
        @include('livewire-datatable::components.column-visibility-toggle')

        @if ($this->showExport())
            <button
                type="button"
                wire:click="export"
                wire:loading.attr="disabled"
                class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] disabled:opacity-50"
            >
                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5 5-5M12 15V3" />
                </svg>
                {{ __('livewire-datatable::livewire-datatable.export') }}
            </button>
        @endif

        @if ($this->showDensityToggle())
            @php
                $densityIconPaths = [
                    'compact' => 'M4 9h16M4 12h16M4 15h16',
                    'comfortable' => 'M4 6h16M4 12h16M4 18h16',
                    'spacious' => 'M4 4h16M4 12h16M4 20h16',
                ];
            @endphp
            <div class="flex items-center overflow-hidden rounded-lg border border-slate-200 bg-white" role="group" aria-label="{{ __('livewire-datatable::livewire-datatable.density') }}">
                @foreach ($this->densityOptions() as $option)
                    <button
                        type="button"
                        wire:click="setDensity('{{ $option }}')"
                        aria-pressed="{{ $this->density === $option ? 'true' : 'false' }}"
                        aria-label="{{ __('livewire-datatable::livewire-datatable.density_'.$option) }}"
                        title="{{ __('livewire-datatable::livewire-datatable.density_'.$option) }}"
                        class="flex h-9 w-9 items-center justify-center transition-colors duration-150 {{ $this->density === $option ? 'bg-[var(--dt-primary,#4f46e5)] text-[var(--dt-primary-text,#ffffff)]' : 'text-slate-500 hover:bg-slate-50' }}"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-width="2" d="{{ $densityIconPaths[$option] ?? '' }}" />
                        </svg>
                    </button>
                @endforeach
            </div>
        @endif

        @if ($this->showPerPage())
            <div class="relative">
                <select
                    wire:model.live="perPage"
                    aria-label="{{ __('livewire-datatable::livewire-datatable.per_page') }}"
                    class="appearance-none bg-none rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm text-slate-600 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
                >
                    @foreach ($this->perPageOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        @endif

        @foreach ($this->toolbarActions() as $item)
            @if ($item instanceof \Salioudiabate\LivewireDatatable\ToolbarActionGroup && $item->getAlign() === 'right')
                @include('livewire-datatable::components.toolbar-action-group', ['group' => $item])
            @elseif ($item instanceof \Salioudiabate\LivewireDatatable\ToolbarAction && $item->getAlign() === 'right' && $item->isAuthorized())
                @include('livewire-datatable::components.toolbar-action', ['action' => $item, 'grouped' => false])
            @endif
        @endforeach
    </div>
</div>
