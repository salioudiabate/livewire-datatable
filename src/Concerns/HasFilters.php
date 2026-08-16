<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\Filters\FilterContract;

trait HasFilters
{
    /**
     * @var array<string, mixed>
     */
    public array $filterValues = [];

    abstract protected function onTableStateChanged(): void;

    /**
     * @return array<int, FilterContract>
     */
    public function filters(): array
    {
        return [];
    }

    public function updatedFilterValues(): void
    {
        $this->onTableStateChanged();
    }

    public function resetFilters(): void
    {
        $this->filterValues = [];
        $this->onTableStateChanged();
    }

    public function hasActiveFilters(): bool
    {
        return $this->activeFilterCount() > 0;
    }

    public function activeFilterCount(): int
    {
        return count(array_filter(
            $this->filters(),
            fn (FilterContract $filter) => $filter->isActive($this->filterValues)
        ));
    }

    public function filtersDefaultOpen(): bool
    {
        return $this->hasActiveFilters();
    }

    protected function applyFiltersTo(DataSource $dataSource): DataSource
    {
        foreach ($this->filters() as $filter) {
            if ($filter->isActive($this->filterValues)) {
                $dataSource = $filter->apply($dataSource, $this->filterValues);
            }
        }

        return $dataSource;
    }
}
