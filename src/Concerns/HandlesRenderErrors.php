<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFactory;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\Filters\FilterContract;
use Throwable;

/**
 * Wraps rendering in a try/catch so a bug in a consumer's builder()/
 * columns()/filters() (bad relation name, missing column, etc.) degrades to
 * a friendly inline error instead of a hard 500 for the whole page — kept
 * from every one of the audited predecessor implementations, all of which
 * independently arrived at this same defensive pattern.
 */
trait HandlesRenderErrors
{
    /**
     * @return array<int, Column>
     */
    abstract public function visibleColumns(): array;

    /**
     * @return array<int, FilterContract>
     */
    abstract public function filters(): array;

    abstract public function rows(): mixed;

    /**
     * @param  array<int, Column>  $columns
     */
    abstract public function assertValidFrozenColumns(array $columns): void;

    protected function renderTable(): View
    {
        try {
            $columns = $this->visibleColumns();
            $filters = $this->filters();

            $this->assertValidFrozenColumns($columns);

            // rows() is #[Computed] (memoized): forcing it here means a
            // failure in builder()/columns() surfaces inside this try block
            // instead of later, when Blade lazily evaluates $this->rows
            // during Livewire's own rendering pass — outside this method
            // entirely, and therefore outside any try/catch it could have.
            // The memoized result is simply reused when the view accesses
            // $this->rows below.
            $this->rows();

            return ViewFactory::make('livewire-datatable::components.table', [
                'columns' => $columns,
                'filters' => $filters,
            ]);
        } catch (Throwable $e) {
            report($e);

            return ViewFactory::make('livewire-datatable::components.error', [
                'exceptionClass' => $e::class,
                'message' => $e->getMessage(),
                'debug' => (bool) config('app.debug'),
            ]);
        }
    }
}
