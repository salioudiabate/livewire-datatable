@php
    $toolbarActionMode = $mode ?? 'standalone';
    $toolbarActionDefaultClass = match ($toolbarActionMode) {
        'segmented' => 'flex h-9 items-center gap-1.5 px-3 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50',
        'dropdown-item' => 'flex w-full items-center gap-1.5 px-3 py-1.5 text-left text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50',
        default => $this->toolbarActionClasses(),
    };
    $toolbarActionClass = $action->getCssClass() !== '' ? $action->getCssClass() : $toolbarActionDefaultClass;
@endphp

@if ($action->getTrigger() === 'url')
    <a
        href="{{ $action->getUrl() }}"
        @if ($action->getTarget())
            target="{{ $action->getTarget() }}"
        @endif
        class="{{ $toolbarActionClass }}"
    >
        @if ($action->getIcon())
            {!! $action->getIcon() !!}
        @endif
        {{ $action->getLabel() }}
    </a>
@elseif ($action->getTrigger() === 'dispatch')
    <button
        type="button"
        @if ($toolbarActionMode === 'dropdown-item')
            x-on:click="open = false"
        @endif
        wire:click="$dispatch('{{ $action->getDispatchEvent() }}', @js($action->getDispatchParams()))"
        class="{{ $toolbarActionClass }}"
    >
        @if ($action->getIcon())
            {!! $action->getIcon() !!}
        @endif
        {{ $action->getLabel() }}
    </button>
@else
    <button
        type="button"
        @if ($action->needsConfirmation())
            x-on:click="{{ $toolbarActionMode === 'dropdown-item' ? 'open = false; ' : '' }}confirm('{{ $action->getConfirmMessage() }}') && $wire.runToolbarAction('{{ $action->getMethod() }}')"
        @elseif ($toolbarActionMode === 'dropdown-item')
            x-on:click="open = false"
            wire:click="runToolbarAction('{{ $action->getMethod() }}')"
        @else
            wire:click="runToolbarAction('{{ $action->getMethod() }}')"
        @endif
        class="{{ $toolbarActionClass }}"
    >
        @if ($action->getIcon())
            {!! $action->getIcon() !!}
        @endif
        {{ $action->getLabel() }}
    </button>
@endif
