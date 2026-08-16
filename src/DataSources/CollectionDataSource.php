<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Backs both plain PHP arrays and Collections. Everything happens in
 * memory, so search/sort/pagination are plain Collection operations rather
 * than query-building — the price of supporting arbitrary in-memory data.
 */
final class CollectionDataSource implements DataSource
{
    /**
     * @param  Collection<array-key, mixed>  $items
     */
    public function __construct(private Collection $items) {}

    public function applySearch(string $term, array $searchableFields): static
    {
        if ($term === '' || $searchableFields === []) {
            return $this;
        }

        $clone = clone $this;
        $clone->items = $this->items->filter(function (mixed $row) use ($term, $searchableFields): bool {
            foreach ($searchableFields as $field) {
                $value = data_get($row, $field);

                if ($value !== null && Str::contains((string) $value, $term, ignoreCase: true)) {
                    return true;
                }
            }

            return false;
        })->values();

        return $clone;
    }

    public function applySort(?string $field, string $direction): static
    {
        if ($field === null) {
            return $this;
        }

        $clone = clone $this;
        $clone->items = $direction === 'desc'
            ? $this->items->sortByDesc(fn (mixed $row) => data_get($row, $field))->values()
            : $this->items->sortBy(fn (mixed $row) => data_get($row, $field))->values();

        return $clone;
    }

    /**
     * @param  non-empty-string  $operator  See the note on aggregate() below about why this isn't just `string`.
     */
    public function applyWhere(string $field, string $operator, mixed $value): static
    {
        if (! in_array($operator, ['=', '!=', '>', '>=', '<', '<='], true)) {
            throw new InvalidArgumentException("Unsupported comparison operator [{$operator}].");
        }

        $clone = clone $this;
        $clone->items = $this->items->filter(function (mixed $row) use ($field, $operator, $value): bool {
            $actual = data_get($row, $field);

            return match ($operator) {
                '=' => $actual == $value,
                '!=' => $actual != $value,
                '>' => $actual > $value,
                '>=' => $actual >= $value,
                '<' => $actual < $value,
                default => $actual <= $value,
            };
        })->values();

        return $clone;
    }

    public function applyWhereIn(string $field, array $values): static
    {
        $clone = clone $this;
        $clone->items = $this->items
            ->filter(fn (mixed $row) => in_array(data_get($row, $field), $values, false))
            ->values();

        return $clone;
    }

    public function paginate(int $perPage, int $page): PaginationResult
    {
        return new PaginationResult(
            items: $this->items->forPage($page, $perPage)->values(),
            total: $this->items->count(),
            perPage: $perPage,
            currentPage: $page,
        );
    }

    public function count(): int
    {
        return $this->items->count();
    }

    public function pluckKeys(string|Closure $keyResolver): array
    {
        if (is_string($keyResolver)) {
            return $this->items->map(fn (mixed $row) => data_get($row, $keyResolver))->all();
        }

        return $this->items->map($keyResolver)->all();
    }

    /**
     * The interface declares $function's docblock type as the literal union
     * 'sum'|'avg'|'min'|'max'|'count' for IDE/static-analysis convenience,
     * but that is not a runtime-enforced constraint — a caller can still
     * pass an arbitrary string. Re-declaring it here as non-empty-string
     * (rather than a plain `string` Pint would strip as a superfluous
     * duplicate of the native type) keeps the explicit validation below
     * meaningful instead of PHPStan treating it as unreachable dead code.
     *
     * @param  non-empty-string  $function
     */
    public function aggregate(string $function, string $column): mixed
    {
        if ($function === 'count') {
            return $this->items->count();
        }

        if (! in_array($function, ['sum', 'avg', 'min', 'max'], true)) {
            throw new InvalidArgumentException("Unsupported aggregate function [{$function}].");
        }

        $values = fn (mixed $row) => data_get($row, $column);

        return $this->items->{$function}($values);
    }

    /**
     * @return Collection<array-key, mixed>
     */
    public function raw(): Collection
    {
        return $this->items;
    }
}
