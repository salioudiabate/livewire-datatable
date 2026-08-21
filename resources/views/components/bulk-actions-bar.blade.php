@if (count($this->authorizedBulkActions()) > 0 && count($this->selected) > 0)
    <div class="{{ $this->bulkActionsBarClasses() }}">
        <span class="font-medium text-slate-700">
            {{ __('livewire-datatable::livewire-datatable.selected_count', ['count' => count($this->selected)]) }}
        </span>

        <div class="flex flex-wrap items-center gap-2">
            @foreach ($this->authorizedBulkActions() as $action)
                @if ($action->getSubmitAction() !== null)
                    @include('livewire-datatable::components.action-submit-form', [
                        'formAction' => $action->getSubmitAction(),
                        'formMethod' => $action->getSubmitMethod(),
                        'formData' => array_merge(['selected' => $this->selected], $action->getSubmitData()),
                        'formTarget' => $action->getTarget(),
                        'formConfirm' => $action->getConfirmMessage(),
                        'formLabel' => $action->getLabel(),
                        'formIcon' => $action->getIcon(),
                        'formClass' => $action->getCssClass() !== '' ? $action->getCssClass() : 'rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50',
                    ])
                @else
                    <button
                        type="button"
                        @if ($action->needsConfirmation())
                            x-on:click="confirm(@js($action->getConfirmMessage())) && $wire.runBulkAction('{{ $action->getMethod() }}')"
                        @else
                            wire:click="runBulkAction('{{ $action->getMethod() }}')"
                        @endif
                        class="{{ $action->getCssClass() !== '' ? $action->getCssClass() : 'flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50' }}"
                    >
                        @if ($action->getIcon())
                            {!! $action->getIcon() !!}
                        @endif
                        {{ $action->getLabel() }}
                    </button>
                @endif
            @endforeach
        </div>

        <button
            type="button"
            wire:click="clearSelection"
            class="ml-auto text-sm font-medium text-slate-500 transition-colors duration-150 hover:text-slate-700"
        >
            {{ __('livewire-datatable::livewire-datatable.clear_selection') }}
        </button>
    </div>
@endif
