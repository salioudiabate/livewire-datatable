<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;

/**
 * Strategy contract: each filter type owns both its apply-logic and its own
 * Blade partial (view()), mirroring Column::view(). The shared table view
 * never branches on a filter "type" string — it just renders view() and
 * lets isActive()/apply() do the rest, so adding a new filter type never
 * requires touching the core component or its Blade template.
 */
interface FilterContract
{
    public function key(): string;

    public function label(): string;

    /**
     * The filterValues keys this filter reads/writes. A single-value filter
     * returns [key()]; a range filter returns two derived keys (e.g.
     * "{key}_from"/"{key}_to") instead of the base component special-casing
     * range filters by name.
     *
     * @return array<int, string>
     */
    public function stateKeys(): array;

    /**
     * @param  array<string, mixed>  $filterValues
     */
    public function isActive(array $filterValues): bool;

    /**
     * @param  array<string, mixed>  $filterValues
     */
    public function apply(DataSource $dataSource, array $filterValues): DataSource;

    /**
     * The Blade view name used to render this filter's input(s).
     */
    public function view(): string;

    public function defaultValue(): mixed;
}
