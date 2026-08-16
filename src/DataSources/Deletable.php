<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;

/**
 * Opt-in capability implemented only by adapters where "delete" is well
 * defined against a single underlying table (Eloquent, Query Builder).
 * Collection and RawSql sources deliberately do not implement this.
 */
interface Deletable
{
    /**
     * Delete the rows matching the given key values, one at a time (never a
     * single mass DELETE), so that:
     *   - Eloquent model events/observers fire per row.
     *   - A single row blocked by a foreign key constraint doesn't abort
     *     the rest of the batch — it is recorded as a failure instead.
     *
     * @param  array<int, mixed>  $keys
     * @param  (Closure(mixed $row): bool)|null  $beforeDelete  Return false to skip a row.
     * @param  (Closure(mixed $row): void)|null  $afterDelete
     */
    public function deleteByKeys(
        string $keyName,
        array $keys,
        ?Closure $beforeDelete = null,
        ?Closure $afterDelete = null
    ): DeletionSummary;
}
