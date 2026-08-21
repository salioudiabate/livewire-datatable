<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\Exceptions\MissingRecordKeyException;

/**
 * The header checkbox selects the *current page* only (cheap, immediate) —
 * "select all across every filtered page" is a separate, explicit opt-in
 * action (selectAllFiltered()) surfaced via a banner once the current page
 * is fully selected, rather than eagerly plucking every matching key on a
 * single click.
 */
trait HasSelection
{
    /**
     * @var array<int, string>
     */
    public array $selected = [];

    public bool $selectAll = false;

    abstract protected function filteredDataSource(): DataSource;

    abstract public function rows(): mixed;

    /**
     * The field (or a resolver Closure) that uniquely identifies each row.
     * A Closure receives the row and must return a scalar; it works for
     * selection, but bulk delete specifically requires a plain column name
     * (see Concerns\HasBulkDelete).
     */
    public function recordKey(): string|Closure
    {
        return 'id';
    }

    /**
     * @return array<int, string>
     */
    public function getSelected(): array
    {
        return $this->selected;
    }

    public function isSelected(mixed $key): bool
    {
        return in_array((string) $key, $this->selected, true);
    }

    public function resolveRowKey(mixed $row): string
    {
        $resolver = $this->recordKey();
        $value = $resolver instanceof Closure ? $resolver($row) : data_get($row, $resolver);

        if ($value === null) {
            throw MissingRecordKeyException::forRow($row, 'row selection');
        }

        return (string) $value;
    }

    /**
     * Public wrapper around clearSelected() so the Blade view can clear the
     * selection via wire:click regardless of the current selectAll value —
     * `$set('selectAll', false)` would silently no-op whenever selectAll
     * was already false (e.g. a partial manual selection), since Livewire
     * only fires updated hooks on an actual value change.
     */
    public function clearSelection(): void
    {
        $this->clearSelected();
    }

    public function isCurrentPageFullySelected(): bool
    {
        $pageKeys = $this->visibleRowKeys();

        return $pageKeys !== [] && array_diff($pageKeys, $this->selected) === [];
    }

    public function isAllFilteredSelected(): bool
    {
        $total = $this->filteredDataSource()->count();

        return $total > 0 && count($this->selected) === $total;
    }

    public function updatedSelected(): void
    {
        $this->selectAll = $this->isCurrentPageFullySelected();
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value ? $this->visibleRowKeys() : [];
    }

    public function selectAllFiltered(): void
    {
        $this->selected = $this->allFilteredKeys();
        $this->selectAll = true;
    }

    protected function clearSelected(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    /**
     * The resolved key of every row on the current page — for a custom
     * action of your own (toolbarActions()/bulkActions()/rowActions()
     * methods) that needs to act on exactly what's on screen, the same
     * scope rows() itself uses.
     *
     * @return array<int, string>
     */
    public function visibleRowKeys(): array
    {
        return $this->resolveKeysOf(collect($this->rows()->items()));
    }

    /**
     * The resolved key of every row matching the current search/filters —
     * not just the current page — the same building block
     * selectAllFiltered() uses internally, exposed for a custom action of
     * your own that needs every matching key rather than just a page's
     * worth. See ResolvesDataSource::allFilteredRows() for the full row
     * data instead of just keys.
     *
     * @return array<int, string>
     */
    public function allFilteredKeys(): array
    {
        $keys = $this->filteredDataSource()->pluckKeys($this->recordKey());

        return array_map(function (mixed $value): string {
            if ($value === null) {
                throw MissingRecordKeyException::forRow(null, 'select all');
            }

            return (string) $value;
        }, $keys);
    }

    /**
     * @param  Collection<array-key, mixed>  $rows
     * @return array<int, string>
     */
    private function resolveKeysOf($rows): array
    {
        return $rows->map(fn (mixed $row) => $this->resolveRowKey($row))->all();
    }
}
