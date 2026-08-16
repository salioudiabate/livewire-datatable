<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Salioudiabate\LivewireDatatable\DataSources\Concerns\EscapesLikeTerms;

final class EloquentDataSource implements DataSource, Deletable
{
    use EscapesLikeTerms;

    /**
     * @param  Builder<Model>  $query
     */
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
                if (str_contains($field, '.')) {
                    [$relation, $relationField] = explode('.', $field, 2);
                    $query->orWhereHas(
                        $relation,
                        fn (Builder $relationQuery) => $relationQuery->where($relationField, 'like', "%{$escaped}%")
                    );

                    continue;
                }

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
        $paginator = (clone $this->query)->paginate(perPage: $perPage, page: $page);

        return new PaginationResult(
            items: $paginator->getCollection(),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
        );
    }

    public function count(): int
    {
        return (clone $this->query)->toBase()->getCountForPagination();
    }

    public function pluckKeys(string|Closure $keyResolver): array
    {
        if (is_string($keyResolver)) {
            return (clone $this->query)->pluck($keyResolver)->all();
        }

        return (clone $this->query)->get()->map($keyResolver)->all();
    }

    public function aggregate(string $function, string $column): mixed
    {
        return (clone $this->query)->{$function}($column);
    }

    /**
     * @return Builder<Model>
     */
    public function raw(): Builder
    {
        return $this->query;
    }

    public function deleteByKeys(string $keyName, array $keys, ?Closure $beforeDelete = null, ?Closure $afterDelete = null): DeletionSummary
    {
        $rows = (clone $this->query)->whereIn($keyName, $keys)->get();

        $deleted = [];
        $failures = [];

        foreach ($rows as $row) {
            if ($beforeDelete !== null && $beforeDelete($row) === false) {
                continue;
            }

            $key = $row->{$keyName};

            try {
                $row->delete();
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
