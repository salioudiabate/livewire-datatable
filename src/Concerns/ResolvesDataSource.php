<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;

/**
 * Owns turning whatever builder() returns into a DataSource, memoized for
 * the lifetime of a single request/render so builder() only ever executes
 * once per request no matter how many collaborators need the data (rows(),
 * selection, footer aggregates, export).
 */
trait ResolvesDataSource
{
    private ?DataSource $resolvedDataSource = null;

    /**
     * @return \Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Database\Query\Builder|\Salioudiabate\LivewireDatatable\DataSources\RawSql|\Illuminate\Support\Collection<array-key, mixed>|array<int, mixed>|DataSource
     */
    abstract public function builder(): mixed;

    abstract protected function applySearchTo(DataSource $dataSource): DataSource;

    abstract protected function applyFiltersTo(DataSource $dataSource): DataSource;

    abstract protected function applySortTo(DataSource $dataSource): DataSource;

    protected function dataSource(): DataSource
    {
        return $this->resolvedDataSource ??= DataSourceFactory::make($this->builder());
    }

    /**
     * The base data source with search, filters and sort applied, but not
     * yet paginated — used both for the paginated rows() and for
     * cross-page operations (select-all, footer aggregates, export).
     */
    protected function filteredDataSource(): DataSource
    {
        $source = $this->dataSource();
        $source = $this->applySearchTo($source);
        $source = $this->applyFiltersTo($source);

        return $this->applySortTo($source);
    }
}
