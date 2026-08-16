<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;

/**
 * Tri-state (Yes / No / Any) — isActive() must check `!== null`, not a
 * generic emptiness/falsy check, since `false` is itself a legitimate value.
 */
final class BooleanFilter extends Filter
{
    public function isActive(array $filterValues): bool
    {
        $value = $filterValues[$this->key] ?? null;

        return $value !== null && $value !== '';
    }

    public function view(): string
    {
        return 'livewire-datatable::filters.boolean';
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

        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($bool === null) {
            return $dataSource;
        }

        return $dataSource->applyWhere($this->key, '=', $bool);
    }
}
