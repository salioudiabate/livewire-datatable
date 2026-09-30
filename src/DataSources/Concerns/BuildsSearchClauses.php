<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Builds the global-search conditions so they behave the same on every database:
 * case-insensitive (ILIKE on PostgreSQL, where LIKE is case-sensitive), and with
 * plain column names qualified by the main table when they belong to it, so that
 * a join (e.g. payments ⋈ students, both having `identifier`) doesn't make them ambiguous.
 */
trait BuildsSearchClauses
{
    /** @var array<string, array<int, string>> */
    private static array $searchColumnListings = [];

    /**
     * @param  EloquentBuilder<Model>|QueryBuilder  $query
     */
    protected function likeOperator(EloquentBuilder|QueryBuilder $query): string
    {
        $connection = $query->getConnection();

        return $connection instanceof Connection && $connection->getDriverName() === 'pgsql' ? 'ilike' : 'like';
    }

    /**
     * Adds "OR field LIKE %term%" for a searchable field. For Eloquent, "relation.field"
     * searches a relation; "table.column" (not a relation) is used as a qualified column.
     *
     * @param  EloquentBuilder<Model>|QueryBuilder  $query
     */
    protected function orWhereSearch(EloquentBuilder|QueryBuilder $query, string $field, string $escaped): void
    {
        $operator = $this->likeOperator($query);

        if ($query instanceof EloquentBuilder && str_contains($field, '.')) {
            [$relation, $relationField] = explode('.', $field, 2);

            if (method_exists($query->getModel(), $relation)) {
                $query->orWhereHas($relation, fn (EloquentBuilder $relationQuery) => $relationQuery->where($relationField, $operator, "%{$escaped}%"));

                return;
            }
        }

        $query->orWhere($this->qualifySearchField($query, $field), $operator, "%{$escaped}%");
    }

    /**
     * @param  EloquentBuilder<Model>|QueryBuilder  $query
     */
    private function qualifySearchField(EloquentBuilder|QueryBuilder $query, string $field): string
    {
        if (str_contains($field, '.')) {
            return $field;
        }

        $base = $query instanceof EloquentBuilder ? $query->getQuery() : $query;
        $table = $base->from;
        $connection = $base->getConnection();

        if (! is_string($table) || str_contains($table, ' ') || ! $connection instanceof Connection) {
            return $field;
        }

        $key = $connection->getName().'|'.$table;
        self::$searchColumnListings[$key] ??= $connection->getSchemaBuilder()->getColumnListing($table);

        return in_array($field, self::$searchColumnListings[$key], true) ? $table.'.'.$field : $field;
    }
}
