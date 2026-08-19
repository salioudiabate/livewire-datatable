@php
    $toolbarActionDefaultClass = ($grouped ?? false)
        ? 'flex h-9 items-center gap-1.5 px-3 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50'
        : $this->toolbarActionClasses();
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
            x-on:click="confirm('{{ $action->getConfirmMessage() }}') && $wire.runToolbarAction('{{ $action->getMethod() }}')"
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
