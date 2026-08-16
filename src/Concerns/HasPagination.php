<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;

/**
 * Deliberately does not use Livewire\WithPagination: that trait is shaped
 * around Eloquent's own paginate() and can't be reconciled with per-instance
 * URL keys (see HasUrlBinding). $page/$perPage are owned here as plain
 * properties instead, and a real LengthAwarePaginator is built from the
 * DataSource-agnostic PaginationResult DTO.
 *
 * gotoPage()/previousPage()/nextPage() intentionally match the method
 * signatures Livewire's own WithPagination trait exposes (including the
 * unused $pageName parameter) so the ported pagination Blade view — which
 * this package registers as the *global* default Tailwind pagination view
 * — keeps working for any other Livewire component in the host app that
 * still uses WithPagination normally.
 */
trait HasPagination
{
    public int $page = 1;

    public int $perPage = 10;

    abstract protected function filteredDataSource(): DataSource;

    abstract protected function urlKey(): string;

    abstract protected function withoutUrlBinding(): bool;

    public function showPerPage(): bool
    {
        return true;
    }

    /**
     * @return array<int, int>
     */
    public function perPageOptions(): array
    {
        return [10, 25, 50, 100];
    }

    public function updatedPerPage(): void
    {
        $this->onTableStateChanged();
    }

    public function gotoPage(int $page, ?string $pageName = null): void
    {
        $this->page = max(1, $page);
    }

    public function previousPage(?string $pageName = null): void
    {
        $this->gotoPage($this->page - 1);
    }

    public function nextPage(?string $pageName = null): void
    {
        $this->gotoPage($this->page + 1);
    }

    protected function resetPage(): void
    {
        $this->page = 1;
    }

    protected function pageName(): string
    {
        return $this->withoutUrlBinding() ? 'page' : "{$this->urlKey()}-page";
    }

    /**
     * @return LengthAwarePaginator<array-key, mixed>
     */
    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return $this->filteredDataSource()
            ->paginate($this->perPage, $this->page)
            ->toLengthAwarePaginator(pageName: $this->pageName());
    }
}
