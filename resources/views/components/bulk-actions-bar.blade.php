@if (count($this->authorizedBulkActions()) > 0 && count($this->selected) > 0)
    <div class="flex flex-wrap items-center gap-3 border-b border-slate-200 bg-[var(--dt-primary-light, #eef2ff)] px-4 py-2">
        <span class="text-sm font-medium text-slate-700">
            {{ __('livewire-datatable::livewire-datatable.selected_count', ['count' => count($this->selected)]) }}
        </span>

        <div class="flex flex-wrap items-center gap-2">
            @foreach ($this->authorizedBulkActions() as $action)
                <button
                    type="button"
                    @if ($action->needsConfirmation())
                        x-on:click="confirm('{{ $action->getConfirmMessage() }}') && $wire.runBulkAction('{{ $action->getMethod() }}')"
                    @else
                        wire:click="runBulkAction('{{ $action->getMethod() }}')"
                    @endif
                    class="{{ $action->getCssClass() !== '' ? $action->getCssClass() : 'rounded-lg border border-slate-200 bg-white px-3 py-1 text-sm text-slate-600 hover:bg-slate-50' }}"
                >
                    {{ $action->getLabel() }}
                </button>
            @endforeach
        </div>

        <button
            type="button"
            wire:click="clearSelection"
            class="ml-auto text-sm text-slate-400 hover:text-slate-600"
        >
            {{ __('livewire-datatable::livewire-datatable.clear_selection') }}
        </button>
    </div>
@endif
