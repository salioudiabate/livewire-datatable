<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataSources\Concerns\EscapesLikeTerms;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;

trait HasSearch
{
    use EscapesLikeTerms;

    public string $search = '';

    /**
     * @return array<int, Column>
     */
    abstract public function columns(): array;

    abstract protected function onTableStateChanged(): void;

    public function updatedSearch(): void
    {
        $this->onTableStateChanged();
    }

    public function showSearch(): bool
    {
        return true;
    }

    public function searchPlaceholder(): string
    {
        return __('livewire-datatable::livewire-datatable.search');
    }

    /**
     * @return array<int, Column>
     */
    protected function searchableColumns(): array
    {
        return array_values(array_filter($this->columns(), fn (Column $column) => $column->isSearchable()));
    }

    protected function applySearchTo(DataSource $dataSource): DataSource
    {
        $term = trim($this->search);
        $columns = $this->searchableColumns();

        if ($term === '' || $columns === []) {
            return $dataSource;
        }

        $hasCustomSearch = collect($columns)->contains(fn (Column $column) => $column->getSearchUsing() !== null);

        if (! $hasCustomSearch) {
            return $dataSource->applySearch($term, array_map(fn (Column $column) => $column->getField(), $columns));
        }

        return $this->applyCombinedSearch($dataSource, $columns, $term);
    }

    /**
     * At least one searchable column supplies a custom search closure, so
     * the whole global-search clause (generic fields included) is built as
     * one combined OR-clause directly against the raw query — otherwise the
     * generic and custom conditions would AND together instead of ORing,
     * which would silently break global search.
     *
     * @param  array<int, Column>  $columns
     */
    private function applyCombinedSearch(DataSource $dataSource, array $columns, string $term): DataSource
    {
        $raw = $dataSource->raw();

        if (! is_object($raw) || ! method_exists($raw, 'where')) {
            $genericFields = array_map(
                fn (Column $column) => $column->getField(),
                array_filter($columns, fn (Column $column) => $column->getSearchUsing() === null)
            );

            return $genericFields === [] ? $dataSource : $dataSource->applySearch($term, $genericFields);
        }

        $escaped = $this->escapeLikeTerm($term);
        $isEloquent = $raw instanceof EloquentBuilder;

        $raw->where(function (mixed $query) use ($columns, $term, $escaped, $isEloquent) {
            foreach ($columns as $column) {
                $using = $column->getSearchUsing();

                if ($using !== null) {
                    $using($query, $term);

                    continue;
                }

                $field = $column->getField();

                if ($isEloquent && str_contains($field, '.')) {
                    [$relation, $relationField] = explode('.', $field, 2);
                    $query->orWhereHas($relation, fn (mixed $relationQuery) => $relationQuery->where($relationField, 'like', "%{$escaped}%"));

                    continue;
                }

                $query->orWhere($field, 'like', "%{$escaped}%");
            }
        });

        return DataSourceFactory::make($raw);
    }
}
