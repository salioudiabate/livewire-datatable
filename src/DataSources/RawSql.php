<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

/**
 * Explicit wrapper for a raw SQL query, so a bare string returned from
 * builder() is never ambiguously mistaken for something else. The SQL must
 * be a bare SELECT with no own ORDER BY/LIMIT — those are owned exclusively
 * by RawSqlDataSource, which wraps this query as a derived table.
 */
final class RawSql
{
    /**
     * @param  array<int|string, mixed>  $bindings
     */
    private function __construct(
        public readonly string $sql,
        public readonly array $bindings,
        public readonly ?string $connection,
    ) {}

    /**
     * @param  array<int|string, mixed>  $bindings
     */
    public static function query(string $sql, array $bindings = [], ?string $connection = null): self
    {
        return new self($sql, $bindings, $connection);
    }
}
