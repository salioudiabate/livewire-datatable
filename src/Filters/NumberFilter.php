<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;

final class NumberFilter extends Filter
{
    public function view(): string
    {
        return 'livewire-datatable::filters.number';
    }

    public function defaultValue(): mixed
    {
        return '';
    }

    protected function applyDefault(DataSource $dataSource, mixed $value): DataSource
    {
        if ($value === null || $value === '') {
            return $dataSource;
        }

        return $dataSource->applyWhere($this->key, '=', is_numeric($value) ? $value + 0 : $value);
    }
}
