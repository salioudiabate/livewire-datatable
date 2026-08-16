@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = $scrollTo !== false
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav
            role="navigation"
            aria-label="{{ __('livewire-datatable::livewire-datatable.pagination') }}"
            class="flex items-center justify-between"
        >
            <span class="relative z-0 inline-flex overflow-hidden rounded-lg border border-slate-200">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-hidden="true" class="px-4 py-2 text-sm text-slate-300">
                        {{ __('livewire-datatable::livewire-datatable.previous') }}
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="border-r border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-[var(--dt-primary-light, #eef2ff)]"
                    >
                        {{ __('livewire-datatable::livewire-datatable.previous') }}
                    </button>
                @endif

                @if ($paginator->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm text-slate-600 hover:bg-[var(--dt-primary-light, #eef2ff)]"
                    >
                        {{ __('livewire-datatable::livewire-datatable.next') }}
                    </button>
                @else
                    <span aria-disabled="true" aria-hidden="true" class="px-4 py-2 text-sm text-slate-300">
                        {{ __('livewire-datatable::livewire-datatable.next') }}
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>
