<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Filters;

use Closure;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;

abstract class Filter implements FilterContract
{
    protected ?Closure $using = null;

    final public function __construct(
        protected readonly string $label,
        protected readonly string $key,
    ) {}

    public static function make(string $label, string $key): static
    {
        return new static($label, $key);
    }

    /**
     * Full escape hatch: receives the underlying raw query/collection object
     * (whatever DataSource::raw() exposes) and the filter's current value.
     * May mutate it in place (mutable engines: Eloquent, Query Builder) or
     * return a new state (immutable engines: Collection) — both are
     * detected and handled correctly. Opting in trades multi-source
     * portability for full control, since the closure has to know which
     * concrete query engine it's receiving.
     */
    public function using(Closure $callback): static
    {
        $this->using = $callback;

        return $this;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function stateKeys(): array
    {
        return [$this->key];
    }

    public function isActive(array $filterValues): bool
    {
        $value = $filterValues[$this->key] ?? null;

        return $value !== null && $value !== '';
    }

    public function apply(DataSource $dataSource, array $filterValues): DataSource
    {
        $value = $filterValues[$this->key] ?? null;

        return $this->resolveUsing($dataSource, $value) ?? $this->applyDefault($dataSource, $value);
    }

    public function defaultValue(): mixed
    {
        return null;
    }

    abstract public function view(): string;

    /**
     * The closure-free default behavior, portable across every DataSource
     * adapter. Only reached when using() was not supplied.
     */
    abstract protected function applyDefault(DataSource $dataSource, mixed $value): DataSource;

    protected function resolveUsing(DataSource $dataSource, mixed $value): ?DataSource
    {
        if ($this->using === null) {
            return null;
        }

        $result = ($this->using)($dataSource->raw(), $value);

        return $result instanceof DataSource
            ? $result
            : DataSourceFactory::make($result ?? $dataSource->raw());
    }
}
