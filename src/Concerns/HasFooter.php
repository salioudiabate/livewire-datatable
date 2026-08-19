<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Optional summary blocks rendered below the table, above pagination — a
 * total, a count, an average, whatever the table wants to surface. Free-form
 * on purpose: unlike a per-column footer row, these aren't tied to a
 * specific Column, so a table can show fewer, more, or differently-computed
 * blocks than it has columns.
 *
 * Compute values against filteredDataSource() (ResolvesDataSource), not
 * rows() — filteredDataSource() reflects search/filters but not pagination,
 * so a sum here covers every matching row, not just the current page.
 * DataSource::aggregate('sum'|'avg'|'min'|'max'|'count', $column) does this
 * without materializing the full result set on Eloquent/Query Builder/RawSql.
 */
trait HasFooter
{
    /**
     * @return array<int, array{label: string, value: string, align?: 'left'|'right'}>
     */
    public function footer(): array
    {
        return [];
    }
}
