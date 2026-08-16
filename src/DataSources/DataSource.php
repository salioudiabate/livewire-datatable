<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;

/**
 * Strategy/Adapter contract that decouples the table component from any
 * particular query engine. Implementations are used as immutable-per-call:
 * every apply* method returns a new (or `$this`, when nothing changed)
 * instance, and callers must always reassign — `$source = $source->applySort(...)`,
 * never rely on in-place mutation. This lets both mutable adapters (Eloquent,
 * Query Builder) and naturally-immutable ones (Collection) satisfy the same
 * contract without special-casing either.
 */
interface DataSource
{
    /**
     * @param  array<int, string>  $searchableFields  Field names, already allow-listed by the caller.
     */
    public function applySearch(string $term, array $searchableFields): static;

    /**
     * @param  'asc'|'desc'  $direction
     */
    public function applySort(?string $field, string $direction): static;

    /**
     * Generic comparison filter, the portable building block behind every
     * built-in Filter type's default (closure-free) behavior: equality for
     * Select/Boolean/Date, and >= / <= pairs for range filters.
     *
     * @param  '='|'!='|'>'|'>='|'<'|'<='  $operator
     */
    public function applyWhere(string $field, string $operator, mixed $value): static;

    /**
     * @param  array<int, mixed>  $values
     */
    public function applyWhereIn(string $field, array $values): static;

    public function paginate(int $perPage, int $page): PaginationResult;

    public function count(): int;

    /**
     * @return array<int, mixed>
     */
    public function pluckKeys(string|Closure $keyResolver): array;

    /**
     * @param  'sum'|'avg'|'min'|'max'|'count'  $function
     */
    public function aggregate(string $function, string $column): mixed;

    /**
     * Escape hatch to the underlying query/collection object, for custom
     * closures supplied via Column::searchable()/sortUsing() or Filter::using().
     */
    public function raw(): mixed;
}
