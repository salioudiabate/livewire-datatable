<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use InvalidArgumentException;
use Salioudiabate\LivewireDatatable\BulkAction;
use Salioudiabate\LivewireDatatable\Column;

/**
 * Freezes a leading, contiguous run of columns so they stay visible while
 * the rest of a wide table scrolls horizontally. Each frozen column carries
 * its own explicit pixel width() (see Column::frozen()), which is all this
 * trait needs to compute a "left" sticky offset — the cumulative width of
 * every frozen column before it, plus the selection checkbox column's
 * reserved width when bulk actions are in play (that column is made sticky
 * too whenever any column is frozen, otherwise it would scroll away and
 * leave a gap in front of the frozen columns).
 *
 * Position/left/width are dynamic per render and go through inline style();
 * background is a static, themeable Tailwind class (frozenBackgroundClass())
 * composed alongside thClasses()/tdClasses() — an opaque background is
 * required so scrolled-under content doesn't bleed through the sticky cell.
 */
trait HasFrozenColumns
{
    /**
     * @return array<int, BulkAction>
     */
    abstract public function authorizedBulkActions(): array;

    /**
     * @param  array<int, Column>  $columns
     */
    public function assertValidFrozenColumns(array $columns): void
    {
        $seenUnfrozen = false;

        foreach ($columns as $column) {
            if (! $column->isFrozen()) {
                $seenUnfrozen = true;

                continue;
            }

            if ($seenUnfrozen) {
                throw new InvalidArgumentException(
                    "Frozen columns must be a leading, contiguous run of columns(): '{$column->getLabel()}' is frozen but a non-frozen column comes before it."
                );
            }
        }
    }

    /**
     * @param  array<int, Column>  $columns
     */
    public function hasFrozenColumns(array $columns): bool
    {
        foreach ($columns as $column) {
            if ($column->isFrozen()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, Column>  $columns
     */
    public function frozenCheckboxStyle(array $columns): ?string
    {
        if (! $this->hasFrozenColumns($columns)) {
            return null;
        }

        return 'position: sticky; left: 0; z-index: 1;';
    }

    /**
     * @param  array<int, Column>  $columns
     */
    public function frozenColumnStyle(Column $column, array $columns): ?string
    {
        if (! $column->isFrozen()) {
            return null;
        }

        $offset = count($this->authorizedBulkActions()) > 0
            ? (int) config('livewire-datatable.frozen_checkbox_width', 44)
            : 0;

        foreach ($columns as $c) {
            if ($c === $column) {
                break;
            }

            if ($c->isFrozen()) {
                $offset += (int) $c->getWidth();
            }
        }

        return "position: sticky; left: {$offset}px; width: {$column->getWidth()}px; min-width: {$column->getWidth()}px; z-index: 1;";
    }

    /**
     * @param  array<int, Column>  $columns
     */
    public function isLastFrozenColumn(Column $column, array $columns): bool
    {
        if (! $column->isFrozen()) {
            return false;
        }

        $frozenColumns = array_values(array_filter($columns, fn (Column $c): bool => $c->isFrozen()));

        return end($frozenColumns) === $column;
    }

    /**
     * @param  array<int, Column>  $columns
     */
    public function frozenRightEdgeClass(Column $column, array $columns): string
    {
        return $this->isLastFrozenColumn($column, $columns)
            ? (string) config('livewire-datatable.classes.frozen_edge', 'shadow-[2px_0_4px_-2px_rgba(0,0,0,0.15)]')
            : '';
    }

    public function frozenTheadBackgroundClass(): string
    {
        return (string) config('livewire-datatable.classes.frozen_thead_bg', 'bg-slate-50');
    }

    public function frozenTbodyBackgroundClass(): string
    {
        return (string) config('livewire-datatable.classes.frozen_tbody_bg', 'bg-white');
    }
}
