<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Salioudiabate\LivewireDatatable\DataSources\DataSource;

/**
 * Shared base for two-bound filters (Date/Number range): "either bound
 * present ⇒ active, cast both bounds the same way" is the same logic for
 * both, only castBound() differs. Replaces the hardcoded "{key}_from"/
 * "{key}_to" special-casing the audited source projects had baked into
 * their shared component instead of the filter class itself.
 */
abstract class RangeFilter extends Filter
{
    public function fromKey(): string
    {
        return "{$this->key}_from";
    }

    public function toKey(): string
    {
        return "{$this->key}_to";
    }

    public function stateKeys(): array
    {
        return [$this->fromKey(), $this->toKey()];
    }

    public function isActive(array $filterValues): bool
    {
        $from = $filterValues[$this->fromKey()] ?? null;
        $to = $filterValues[$this->toKey()] ?? null;

        return ($from !== null && $from !== '') || ($to !== null && $to !== '');
    }

    public function apply(DataSource $dataSource, array $filterValues): DataSource
    {
        $from = $filterValues[$this->fromKey()] ?? null;
        $to = $filterValues[$this->toKey()] ?? null;

        $resolved = $this->resolveUsing($dataSource, ['from' => $from, 'to' => $to]);

        if ($resolved !== null) {
            return $resolved;
        }

        if ($from !== null && $from !== '') {
            $dataSource = $dataSource->applyWhere($this->key, '>=', $this->castBound($from));
        }

        if ($to !== null && $to !== '') {
            $dataSource = $dataSource->applyWhere($this->key, '<=', $this->castBound($to));
        }

        return $dataSource;
    }

    public function defaultValue(): mixed
    {
        return ['from' => '', 'to' => ''];
    }

    protected function castBound(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Never reached: apply() is overridden above since a range needs two
     * values at once, not the single value applyDefault() receives.
     */
    protected function applyDefault(DataSource $dataSource, mixed $value): DataSource
    {
        return $dataSource;
    }
}
