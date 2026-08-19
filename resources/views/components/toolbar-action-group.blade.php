@php
    $authorizedGroupActions = array_values(array_filter(
        $group->getActions(),
        fn ($groupAction) => $groupAction->isAuthorized()
    ));
@endphp

@if (count($authorizedGroupActions) > 0)
    <div class="{{ $group->getCssClass() !== '' ? $group->getCssClass() : $this->toolbarActionGroupClasses() }}">
        @foreach ($authorizedGroupActions as $groupAction)
            @include('livewire-datatable::components.toolbar-action', ['action' => $groupAction, 'grouped' => true])
        @endforeach
    </div>
@endif
