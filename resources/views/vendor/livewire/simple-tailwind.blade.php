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
        <nav role="navigation" aria-label="{{ __('livewire-datatable::livewire-datatable.pagination') }}">
            <span class="{{ config('livewire-datatable.classes.pagination_bar', '') }} inline-flex">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-hidden="true" class="relative inline-flex items-center border-r border-slate-200 bg-white px-4 py-1.5 text-xs font-medium leading-5 text-slate-300">
                        {{ __('livewire-datatable::livewire-datatable.previous') }}
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        class="relative inline-flex items-center border-r border-slate-200 bg-white px-4 py-1.5 text-xs font-medium leading-5 text-slate-600 transition ease-in-out duration-150 hover:bg-slate-50 hover:text-slate-800 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] active:bg-slate-100"
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
                        class="relative -ml-px inline-flex items-center bg-white px-4 py-1.5 text-xs font-medium leading-5 text-slate-600 transition ease-in-out duration-150 hover:bg-slate-50 hover:text-slate-800 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] active:bg-slate-100"
                    >
                        {{ __('livewire-datatable::livewire-datatable.next') }}
                    </button>
                @else
                    <span aria-disabled="true" aria-hidden="true" class="relative -ml-px inline-flex items-center bg-white px-4 py-1.5 text-xs font-medium leading-5 text-slate-300">
                        {{ __('livewire-datatable::livewire-datatable.next') }}
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>
