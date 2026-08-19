@if (count($actions) > 0)
    <div x-data="{ open: false }" class="relative inline-block text-left">
        <button
            type="button"
            x-on:click="open = ! open"
            class="rounded-lg p-1.5 text-slate-400 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]"
        >
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
            </svg>
        </button>

        <div
            x-show="open"
            x-cloak
            x-on:click.outside="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute right-0 z-10 mt-1 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg"
        >
            @foreach ($actions as $action)
                @if ($action->resolveUrl($row) !== null)
                    <a
                        href="{{ $action->resolveUrl($row) }}"
                        @if ($action->getTarget())
                            target="{{ $action->getTarget() }}"
                        @endif
                        class="block px-3 py-1.5 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 {{ $action->getCssClass() }}"
                    >
                        {{ $action->getLabel() }}
                    </a>
                @elseif ($action->resolveSubmitUrl($row) !== null)
                    @include('livewire-datatable::components.action-submit-form', [
                        'formAction' => $action->resolveSubmitUrl($row),
                        'formMethod' => $action->getSubmitMethod(),
                        'formData' => $action->resolveSubmitData($row),
                        'formTarget' => $action->getTarget(),
                        'formConfirm' => $action->getConfirmMessage(),
                        'formLabel' => $action->getLabel(),
                        'formIcon' => $action->getIcon(),
                        'formClass' => 'block w-full px-3 py-1.5 text-left text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 '.$action->getCssClass(),
                        'formCloseDropdown' => true,
                        'formWrapperClass' => 'block w-full',
                    ])
                @else
                    <button
                        type="button"
                        @if ($action->needsConfirmation())
                            x-on:click="open = false; confirm('{{ $action->getConfirmMessage() }}') && $wire.{{ $action->getMethod() }}('{{ $this->resolveRowKey($row) }}')"
                        @else
                            wire:click="{{ $action->getMethod() }}('{{ $this->resolveRowKey($row) }}')"
                        @endif
                        class="block w-full px-3 py-1.5 text-left text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 {{ $action->getCssClass() }}"
                    >
                        {{ $action->getLabel() }}
                    </button>
                @endif
            @endforeach
        </div>
    </div>
@endif
