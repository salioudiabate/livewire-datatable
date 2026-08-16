<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * A pagination result decoupled from Livewire/HTTP concerns (page name, path),
 * so DataSource implementations stay unit-testable without an HTTP context.
 */
final class PaginationResult
{
    /**
     * @param  Collection<array-key, mixed>  $items
     */
    public function __construct(
        public readonly Collection $items,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
    ) {}

    public function lastPage(): int
    {
        return (int) max(1, (int) ceil($this->total / max(1, $this->perPage)));
    }

    /**
     * @return LengthAwarePaginatorContract<array-key, mixed>
     */
    public function toLengthAwarePaginator(string $pageName = 'page', ?string $path = null): LengthAwarePaginatorContract
    {
        return new LengthAwarePaginator(
            items: $this->items,
            total: $this->total,
            perPage: max(1, $this->perPage),
            currentPage: $this->currentPage,
            options: [
                'pageName' => $pageName,
                'path' => $path ?? LengthAwarePaginator::resolveCurrentPath(),
            ],
        );
    }
}
