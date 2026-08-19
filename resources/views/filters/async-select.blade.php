@php
    $term = $this->filterSearchTerms[$filter->key()] ?? '';
    $value = $this->filterValues[$filter->key()] ?? null;
    $hasValue = $value !== null && $value !== '';
    $options = $filter->searchOptions($term);
    $selectedLabel = $hasValue ? $filter->resolveLabel($value) : null;
@endphp

<div class="flex flex-col gap-1.5" x-data="{ open: false }">
    <label class="{{ $this->filterLabelClasses() }}">{{ $filter->label() }}</label>
    <div class="relative">
        <button
            type="button"
            x-on:click="open = ! open"
            class="{{ trim(($filter->getCssClass() ?? $this->filterSelectClasses()).' flex items-center justify-between text-left') }}"
        >
            <span class="truncate {{ $selectedLabel ? '' : 'text-slate-400' }}">
                {{ $selectedLabel ?? __('livewire-datatable::livewire-datatable.select_option') }}
            </span>
        </button>
        <svg class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>

        <div
            x-show="open"
            x-cloak
            x-on:click.outside="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute z-20 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg shadow-slate-100"
        >
            <div class="p-2">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="filterSearchTerms.{{ $filter->key() }}"
                    placeholder="{{ __('livewire-datatable::livewire-datatable.search') }}"
                    class="w-full rounded-md border border-slate-200 px-2.5 py-1.5 text-sm text-slate-700 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-1 focus:ring-[var(--dt-primary,#4f46e5)]"
                />
            </div>
            <ul class="max-h-56 overflow-y-auto py-1">
                @if ($hasValue)
                    <li>
                        <button
                            type="button"
                            wire:click="$set('filterValues.{{ $filter->key() }}', null)"
                            x-on:click="open = false"
                            class="block w-full px-3 py-1.5 text-left text-sm text-slate-400 hover:bg-slate-50"
                        >
                            {{ __('livewire-datatable::livewire-datatable.clear_selection') }}
                        </button>
                    </li>
                @endif
                @forelse ($options as $optionValue => $optionLabel)
                    <li wire:key="async-filter-{{ $filter->key() }}-{{ $optionValue }}">
                        <button
                            type="button"
                            wire:click="$set('filterValues.{{ $filter->key() }}', '{{ $optionValue }}')"
                            x-on:click="open = false"
                            class="block w-full px-3 py-1.5 text-left text-sm text-slate-700 hover:bg-slate-50 {{ (string) $value === (string) $optionValue ? 'bg-[var(--dt-primary-light,#eef2ff)] font-medium' : '' }}"
                        >
                            {{ $optionLabel }}
                        </button>
                    </li>
                @empty
                    <li class="px-3 py-1.5 text-sm text-slate-400">
                        {{ __('livewire-datatable::livewire-datatable.no_options') }}
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
