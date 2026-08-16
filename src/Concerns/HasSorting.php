<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;
use Salioudiabate\LivewireDatatable\Exceptions\InvalidSortColumnException;

trait HasSorting
{
    public ?string $sortField = null;

    public string $sortDirection = 'asc';

    /**
     * @return array<int, Column>
     */
    abstract public function columns(): array;

    abstract protected function onTableStateChanged(): void;

    /**
     * Re-validates the field against sortable columns on every call, since
     * Livewire actions are directly callable by name regardless of what the
     * UI renders — relying solely on "the button only exists for sortable
     * columns" would not be real protection.
     */
    public function sortBy(string $field): void
    {
        if (! array_key_exists($field, $this->sortableFieldMap())) {
            throw InvalidSortColumnException::forField($field);
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->onTableStateChanged();
    }

    /**
     * @return array<string, string> display field => underlying sort field
     */
    protected function sortableFieldMap(): array
    {
        $map = [];

        foreach ($this->columns() as $column) {
            if ($column->isSortable()) {
                $map[$column->getField()] = $column->getSortField();
            }
        }

        return $map;
    }

    protected function applySortTo(DataSource $dataSource): DataSource
    {
        if ($this->sortField === null) {
            return $dataSource;
        }

        $map = $this->sortableFieldMap();

        // Ignore rather than throw: columns() may have legitimately changed
        // (e.g. a permission-gated column) since this state was set, and a
        // stale sort field shouldn't crash rendering.
        if (! array_key_exists($this->sortField, $map)) {
            return $dataSource;
        }

        $column = $this->columnByField($this->sortField);
        $sortUsing = $column?->getSortUsing();

        // $sortDirection is a plain `string` (it must stay a Livewire-bindable
        // scalar) even though only 'asc'/'desc' are ever assigned by sortBy();
        // normalize defensively rather than trusting that invariant blindly.
        $direction = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        if ($sortUsing !== null) {
            $result = $sortUsing($dataSource->raw(), $direction);

            return $result instanceof DataSource
                ? $result
                : DataSourceFactory::make($result ?? $dataSource->raw());
        }

        return $dataSource->applySort($map[$this->sortField], $direction);
    }

    private function columnByField(string $field): ?Column
    {
        foreach ($this->columns() as $column) {
            if ($column->getField() === $field) {
                return $column;
            }
        }

        return null;
    }
}
