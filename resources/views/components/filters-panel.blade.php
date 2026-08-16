<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($filters as $filter)
        @include($filter->view(), ['filter' => $filter])
    @endforeach
</div>
