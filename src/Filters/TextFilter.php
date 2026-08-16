<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;

final class TextFilter extends Filter
{
    public function view(): string
    {
        return 'livewire-datatable::filters.text';
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

        return $dataSource->applySearch((string) $value, [$this->key]);
    }
}
