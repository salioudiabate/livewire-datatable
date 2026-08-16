<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * A decorator around QueryBuilderDataSource, not a fifth reimplementation:
 * the raw SQL is wrapped as a derived table via fromRaw() (bindings stay
 * parameterized, never string-interpolated), and every operation then
 * reuses QueryBuilderDataSource's search/sort/paginate/count/pluck/aggregate
 * logic against that derived table.
 *
 * The wrapped SQL must be a bare SELECT with no own ORDER BY/LIMIT — those
 * are owned exclusively by this class.
 *
 * Deliberately does not implement Deletable: there is no single well-defined
 * table to delete from once the source is an arbitrary raw query.
 */
final class RawSqlDataSource implements DataSource
{
    private QueryBuilderDataSource $inner;

    public function __construct(RawSql $rawSql)
    {
        // fromSub() (rather than fromRaw()) is used deliberately: the wrapped
        // SQL is only known at runtime, and fromSub() accepts a plain string
        // subquery without requiring it to be a compile-time literal. Its
        // bindings are then attached manually since Builder::parseSub()
        // does not extract bindings out of a bare SQL string.
        $query = DB::connection($rawSql->connection)
            ->query()
            ->fromSub($rawSql->sql, 'dt_sub')
            ->addBinding($rawSql->bindings, 'from');

        $this->inner = new QueryBuilderDataSource($query);
    }

    private function withInner(QueryBuilderDataSource $inner): static
    {
        $clone = clone $this;
        $clone->inner = $inner;

        return $clone;
    }

    public function applySearch(string $term, array $searchableFields): static
    {
        return $this->withInner($this->inner->applySearch($term, $searchableFields));
    }

    public function applySort(?string $field, string $direction): static
    {
        return $this->withInner($this->inner->applySort($field, $direction));
    }

    public function applyWhere(string $field, string $operator, mixed $value): static
    {
        return $this->withInner($this->inner->applyWhere($field, $operator, $value));
    }

    public function applyWhereIn(string $field, array $values): static
    {
        return $this->withInner($this->inner->applyWhereIn($field, $values));
    }

    public function paginate(int $perPage, int $page): PaginationResult
    {
        return $this->inner->paginate($perPage, $page);
    }

    public function count(): int
    {
        return $this->inner->count();
    }

    public function pluckKeys(string|Closure $keyResolver): array
    {
        return $this->inner->pluckKeys($keyResolver);
    }

    public function aggregate(string $function, string $column): mixed
    {
        return $this->inner->aggregate($function, $column);
    }

    public function raw(): Builder
    {
        return $this->inner->raw();
    }
}
