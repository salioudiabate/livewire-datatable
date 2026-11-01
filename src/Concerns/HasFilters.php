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

    /**
     * Per-AsyncSelectFilter search box text, keyed by filter key — separate
     * from filterValues since it drives what the dropdown *shows*, not what
     * the table is actually filtered by. Deliberately excluded from
     * queryString() (HasUrlBinding): it's transient typing state, not
     * something worth a shareable URL.
     *
     * @var array<string, string>
     */
    public array $filterSearchTerms = [];

    /**
     * filters() for the current request only (never dehydrated: private).
     *
     * @var array<int, FilterContract>|null
     */
    private ?array $resolvedFilters = null;

    abstract protected function onTableStateChanged(): void;

    /**
     * filters() built once per request: a filter's options often come from
     * the database (a list of classes, of users…), and filters() is needed by
     * the toolbar badge, the panel and applyFiltersTo() within one render.
     * Built lazily, after the request's updates, so dependent options still
     * see the new filter values.
     *
     * @return array<int, FilterContract>
     */
    protected function resolvedFilters(): array
    {
        return $this->resolvedFilters ??= $this->filters();
    }

    /**
     * @return array<int, FilterContract>
     */
    public function filters(): array
    {
        return [];
    }

    public function filtersLabel(): string
    {
        return __('livewire-datatable::livewire-datatable.filters');
    }

    public function updatedFilterValues(): void
    {
        $this->onTableStateChanged();
    }

    public function resetFilters(): void
    {
        $this->filterValues = [];
        $this->filterSearchTerms = [];
        $this->onTableStateChanged();
    }

    public function hasActiveFilters(): bool
    {
        return $this->activeFilterCount() > 0;
    }

    public function activeFilterCount(): int
    {
        return count(array_filter(
            $this->resolvedFilters(),
            fn (FilterContract $filter) => $filter->isActive($this->filterValues)
        ));
    }

    public function filtersDefaultOpen(): bool
    {
        return $this->hasActiveFilters();
    }

    protected function applyFiltersTo(DataSource $dataSource): DataSource
    {
        foreach ($this->resolvedFilters() as $filter) {
            if ($filter->isActive($this->filterValues)) {
                $dataSource = $filter->apply($dataSource, $this->filterValues);
            }
        }

        return $dataSource;
    }
}
