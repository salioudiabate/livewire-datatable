<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Illuminate\Database\QueryException;

/**
 * Outcome of a row-by-row bulk deletion. Deliberately does not classify
 * failures itself (e.g. as "foreign key violation") — that is driver-specific
 * and left to the caller (see Concerns\DetectsForeignKeyViolations), so this
 * class stays free of any particular database engine's error conventions.
 */
final class DeletionSummary
{
    /**
     * @param  array<int, mixed>  $deletedKeys
     * @param  array<int|string, QueryException>  $failures  Keyed by the record key that failed to delete.
     */
    public function __construct(
        public readonly array $deletedKeys,
        public readonly array $failures,
    ) {}

    public function deletedCount(): int
    {
        return count($this->deletedKeys);
    }

    public function hasFailures(): bool
    {
        return $this->failures !== [];
    }

    public function failureCount(): int
    {
        return count($this->failures);
    }
}
