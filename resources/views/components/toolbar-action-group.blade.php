@php
    $authorizedGroupActions = array_values(array_filter(
        $group->getActions(),
        fn ($groupAction) => $groupAction->isAuthorized()
    ));
@endphp

@if (count($authorizedGroupActions) > 0)
    @if ($group->isDropdown())
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                x-on:click="open = ! open"
                class="{{ $group->getCssClass() !== '' ? $group->getCssClass() : $this->toolbarActionClasses() }}"
            >
                @if ($group->getDropdownIcon())
                    {!! $group->getDropdownIcon() !!}
                @endif
                {{ $group->getDropdownLabel() }}
            </button>

            <div
                x-show="open"
                x-cloak
                x-on:click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="{{ $this->toolbarActionDropdownClasses() }}"
            >
                @foreach ($authorizedGroupActions as $groupAction)
                    @include('livewire-datatable::components.toolbar-action', ['action' => $groupAction, 'mode' => 'dropdown-item'])
                @endforeach
            </div>
        </div>
    @else
        <div class="{{ $group->getCssClass() !== '' ? $group->getCssClass() : $this->toolbarActionGroupClasses() }}">
            @foreach ($authorizedGroupActions as $groupAction)
                @include('livewire-datatable::components.toolbar-action', ['action' => $groupAction, 'mode' => 'segmented'])
            @endforeach
        </div>
    @endif
@endif
