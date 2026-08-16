<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Closure;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\DataSources\Deletable;
use Salioudiabate\LivewireDatatable\DataSources\DeletionSummary;
use Salioudiabate\LivewireDatatable\Exceptions\NonDeletableDataSourceException;

/**
 * Opt-in behaviorally (activates only once deletePermission() is
 * overridden to return non-null), gated on the resolved DataSource
 * implementing Deletable (only Eloquent/Query Builder support it).
 * Deletes row-by-row rather than a single mass DELETE: on Eloquent this
 * preserves model events/observers, and on both adapters it means one row
 * blocked by a foreign key constraint doesn't abort the rest of the batch.
 */
trait HasBulkDelete
{
    use DetectsForeignKeyViolations;

    abstract protected function filteredDataSource(): DataSource;

    abstract public function recordKey(): string|Closure;

    /**
     * @return array<int, string>
     */
    abstract public function getSelected(): array;

    protected function deletePermission(): ?string
    {
        return null;
    }

    protected function beforeDelete(mixed $row): bool
    {
        return true;
    }

    protected function afterDelete(mixed $row): void
    {
        //
    }

    public function canBulkDelete(): bool
    {
        $permission = $this->deletePermission();

        return $permission !== null && Gate::allows($permission);
    }

    public function destroySelected(): void
    {
        if (! $this->canBulkDelete()) {
            abort(403);
        }

        $source = $this->filteredDataSource();

        if (! $source instanceof Deletable) {
            throw NonDeletableDataSourceException::forSource($source);
        }

        $keyName = $this->recordKey();

        if ($keyName instanceof Closure) {
            throw new RuntimeException(
                'destroySelected() requires recordKey() to return a plain column name, not a Closure, '.
                'since the underlying delete query needs a real database column.'
            );
        }

        $summary = $source->deleteByKeys(
            $keyName,
            $this->getSelected(),
            beforeDelete: fn (mixed $row) => $this->beforeDelete($row),
            afterDelete: fn (mixed $row) => $this->afterDelete($row),
        );

        $this->clearSelected();

        $this->reportDeletionSummary($summary);
    }

    /**
     * Hook point: override to flash a message via your app's own alert
     * system. FK-blocked failures are already separated from deletedKeys —
     * use isForeignKeyViolation() on each to explain *why* a row survived.
     */
    protected function reportDeletionSummary(DeletionSummary $summary): void
    {
        //
    }

    abstract protected function clearSelected(): void;
}
