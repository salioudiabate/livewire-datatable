<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Closure;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;

/**
 * The async counterpart to SelectFilter, for option sets too large to load
 * eagerly into a single ->options() array — a "Customer" filter over 50k
 * rows, for example, instead of a status enum with 3 values. Same exact-
 * match apply() behavior as SelectFilter; only how the option list is
 * produced differs: on demand, from a search term, instead of upfront.
 */
final class AsyncSelectFilter extends Filter
{
    private ?Closure $optionsResolver = null;

    private ?Closure $labelResolver = null;

    /**
     * @param  Closure(string): array<int|string, string>  $resolver  Receives the current search term (empty string when the box hasn't been typed in yet), returns [value => label, ...].
     */
    public function optionsUsing(Closure $resolver): static
    {
        $this->optionsResolver = $resolver;

        return $this;
    }

    /**
     * Resolves the label shown for the currently selected value, once the
     * search term that produced it in the dropdown is long gone — the
     * dropdown's own result list can't be relied on for this, since it only
     * ever reflects the *current* search term. Falls back to the raw stored
     * value when not set; override this whenever the value itself isn't a
     * presentable label (a database id, for instance).
     *
     * @param  Closure(mixed): ?string  $resolver
     */
    public function labelUsing(Closure $resolver): static
    {
        $this->labelResolver = $resolver;

        return $this;
    }

    /**
     * @return array<int|string, string>
     */
    public function searchOptions(string $term): array
    {
        if ($this->optionsResolver === null) {
            return [];
        }

        return ($this->optionsResolver)($term);
    }

    public function resolveLabel(mixed $value): string
    {
        if ($this->labelResolver !== null) {
            return ($this->labelResolver)($value) ?? (string) $value;
        }

        return (string) $value;
    }

    public function view(): string
    {
        return 'livewire-datatable::filters.async-select';
    }

    public function defaultValue(): mixed
    {
        return '';
    }

    protected function applyDefault(DataSource $dataSource, mixed $value): DataSource
    {
        if ($value === null || $value === '') {
            return $dataSource;
        }

        return $dataSource->applyWhere($this->key, '=', $value);
    }
}
