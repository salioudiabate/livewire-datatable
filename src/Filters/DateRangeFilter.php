<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

final class DateRangeFilter extends RangeFilter
{
    public function view(): string
    {
        return 'livewire-datatable::filters.date-range';
    }
}
