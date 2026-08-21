<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use RuntimeException;
use Salioudiabate\LivewireDatatable\DataSources\Concerns\EscapesLikeTerms;

final class QueryBuilderDataSource implements DataSource, Deletable
{
    use EscapesLikeTerms;

    public function __construct(private Builder $query) {}

    public function applySearch(string $term, array $searchableFields): static
    {
        if ($term === '' || $searchableFields === []) {
            return $this;
        }

        $escaped = $this->escapeLikeTerm($term);

        $clone = clone $this;
        $clone->query = (clone $this->query)->where(function (Builder $query) use ($escaped, $searchableFields) {
            foreach ($searchableFields as $field) {
                $query->orWhere($field, 'like', "%{$escaped}%");
            }
        });

        return $clone;
    }

    public function applySort(?string $field, string $direction): static
    {
        if ($field === null) {
            return $this;
        }

        $clone = clone $this;
        $clone->query = (clone $this->query)->orderBy($field, $direction);

        return $clone;
    }

    public function applyWhere(string $field, string $operator, mixed $value): static
    {
        $clone = clone $this;
        $clone->query = (clone $this->query)->where($field, $operator, $value);

        return $clone;
    }

    public function applyWhereIn(string $field, array $values): static
    {
        $clone = clone $this;
        $clone->query = (clone $this->query)->whereIn($field, $values);

        return $clone;
    }

    public function paginate(int $perPage, int $page): PaginationResult
    {
        $total = $this->count();

        $items = (clone $this->query)->forPage($page, $perPage)->get();

        return new PaginationResult(
            items: $items,
            total: $total,
            perPage: $perPage,
            currentPage: $page,
        );
    }

    public function count(): int
    {
        return (clone $this->query)->getCountForPagination();
    }

    public function pluckKeys(string|Closure $keyResolver): array
    {
        if (is_string($keyResolver)) {
            return (clone $this->query)->pluck($keyResolver)->all();
        }

        return (clone $this->query)->get()->map($keyResolver)->all();
    }

    /**
     * @param  non-empty-string  $function
     */
    public function aggregate(string $function, string $column): mixed
    {
        if (! in_array($function, ['sum', 'avg', 'min', 'max', 'count'], true)) {
            throw new InvalidArgumentException("Unsupported aggregate function [{$function}].");
        }

        return (clone $this->query)->{$function}($column);
    }

    public function raw(): Builder
    {
        return $this->query;
    }

    public function deleteByKeys(string $keyName, array $keys, ?Closure $beforeDelete = null, ?Closure $afterDelete = null): DeletionSummary
    {
        if (! is_string($this->query->from) || $this->query->from === '') {
            throw new RuntimeException(
                'Cannot delete rows: the underlying query is not backed by a plain table name '.
                '(e.g. it wraps a derived table or subquery).'
            );
        }

        $table = $this->query->from;

        $rows = (clone $this->query)->whereIn($keyName, $keys)->get();

        $deleted = [];
        $failures = [];

        foreach ($rows as $row) {
            if ($beforeDelete !== null && $beforeDelete($row) === false) {
                continue;
            }

            $key = $row->{$keyName};

            try {
                $this->query->getConnection()->table($table)->where($keyName, $key)->delete();
                $deleted[] = $key;

                if ($afterDelete !== null) {
                    $afterDelete($row);
                }
            } catch (QueryException $e) {
                $failures[$key] = $e;
            }
        }

        return new DeletionSummary($deleted, $failures);
    }
}
