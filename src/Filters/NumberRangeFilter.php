<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

final class NumberRangeFilter extends RangeFilter
{
    public function view(): string
    {
        return 'livewire-datatable::filters.number-range';
    }

    protected function castBound(mixed $value): mixed
    {
        return is_numeric($value) ? $value + 0 : $value;
    }
}
