@php
    $toolbarActionMode = $mode ?? 'standalone';
    $toolbarActionDefaultClass = match ($toolbarActionMode) {
        'segmented' => 'flex h-9 items-center gap-1.5 px-3 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50',
        'dropdown-item' => 'flex w-full items-center gap-1.5 px-3 py-1.5 text-left text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50',
        default => $action->isPrimary()
            ? ($this->toolbarActionPrimaryClasses() ?? 'flex items-center gap-1.5 rounded-lg border border-[var(--dt-primary,#4f46e5)] bg-[var(--dt-primary,#4f46e5)] px-3 py-2 text-sm font-medium text-[var(--dt-primary-text,#ffffff)] transition-colors duration-150 hover:border-[var(--dt-primary-hover,#4338ca)] hover:bg-[var(--dt-primary-hover,#4338ca)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)] focus:ring-offset-1')
            : $this->toolbarActionClasses(),
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
@elseif ($action->getTrigger() === 'submit')
    @include('livewire-datatable::components.action-submit-form', [
        'formAction' => $action->getSubmitAction(),
        'formMethod' => $action->getSubmitMethod(),
        'formData' => $action->getSubmitData(),
        'formTarget' => $action->getTarget(),
        'formConfirm' => $action->getConfirmMessage(),
        'formLabel' => $action->getLabel(),
        'formIcon' => $action->getIcon(),
        'formClass' => $toolbarActionClass,
        'formCloseDropdown' => $toolbarActionMode === 'dropdown-item',
        'formWrapperClass' => $toolbarActionMode === 'dropdown-item' ? 'block w-full' : 'inline-flex',
    ])
@else
    <button
        type="button"
        @if ($action->needsConfirmation())
            x-on:click="{{ $toolbarActionMode === 'dropdown-item' ? 'open = false; ' : '' }}{!! \Salioudiabate\LivewireDatatable\Support\ConfirmScript::make($action->getConfirmMessage(), '$wire.runToolbarAction('.\Illuminate\Support\Js::from($action->getMethod()).')') !!}"
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
