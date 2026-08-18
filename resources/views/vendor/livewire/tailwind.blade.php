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
            <span class="relative z-0 inline-flex overflow-hidden rounded-lg border border-slate-200">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-hidden="true" class="relative inline-flex items-center bg-white px-2 py-1.5 text-xs font-medium leading-5 text-slate-300">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        aria-label="{{ __('livewire-datatable::livewire-datatable.previous') }}"
                        class="relative inline-flex items-center border-r border-slate-200 bg-white px-2 py-1.5 text-xs font-medium leading-5 text-slate-500 transition ease-in-out duration-150 hover:bg-slate-50 hover:text-slate-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] active:bg-slate-100"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </button>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-hidden="true" class="relative -ml-px inline-flex items-center border-r border-slate-200 bg-white px-3 py-1.5 text-xs font-medium leading-5 text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <span wire:key="dt-paginator-{{ $paginator->getPageName() }}-page-{{ $page }}">
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative -ml-px inline-flex items-center border-r border-slate-200 bg-[var(--dt-primary,#4f46e5)] px-3 py-1.5 text-xs font-semibold leading-5 text-[var(--dt-primary-text,#ffffff)]">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                        aria-label="{{ __('livewire-datatable::livewire-datatable.goto_page', ['page' => $page]) }}"
                                        class="relative -ml-px inline-flex items-center border-r border-slate-200 bg-white px-3 py-1.5 text-xs font-medium leading-5 text-slate-600 transition ease-in-out duration-150 hover:bg-[var(--dt-primary-light,#eef2ff)] hover:text-slate-800 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] active:bg-slate-100"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif
                            </span>
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                        wire:loading.attr="disabled"
                        aria-label="{{ __('livewire-datatable::livewire-datatable.next') }}"
                        class="relative -ml-px inline-flex items-center bg-white px-2 py-1.5 text-xs font-medium leading-5 text-slate-500 transition ease-in-out duration-150 hover:bg-slate-50 hover:text-slate-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] active:bg-slate-100"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </button>
                @else
                    <span aria-disabled="true" aria-hidden="true" class="relative -ml-px inline-flex items-center bg-white px-2 py-1.5 text-xs font-medium leading-5 text-slate-300">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>
