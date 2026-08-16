<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Salioudiabate\LivewireDatatable\Concerns\HandlesRenderErrors;
use Salioudiabate\LivewireDatatable\Concerns\HasBulkActions;
use Salioudiabate\LivewireDatatable\Concerns\HasBulkDelete;
use Salioudiabate\LivewireDatatable\Concerns\HasColumnVisibility;
use Salioudiabate\LivewireDatatable\Concerns\HasExport;
use Salioudiabate\LivewireDatatable\Concerns\HasFilters;
use Salioudiabate\LivewireDatatable\Concerns\HasPagination;
use Salioudiabate\LivewireDatatable\Concerns\HasRowActions;
use Salioudiabate\LivewireDatatable\Concerns\HasSearch;
use Salioudiabate\LivewireDatatable\Concerns\HasSelection;
use Salioudiabate\LivewireDatatable\Concerns\HasSorting;
use Salioudiabate\LivewireDatatable\Concerns\HasStyling;
use Salioudiabate\LivewireDatatable\Concerns\HasUrlBinding;
use Salioudiabate\LivewireDatatable\Concerns\ResolvesDataSource;

/**
 * Template Method skeleton: two required hooks (builder(), columns()), a
 * handful of no-op/sane-default hooks, and a thin render(). All real
 * behavior lives in the Concerns\* traits, each independently reasoned
 * about and tested — see Concerns\ResolvesDataSource for how they combine
 * to build the data ultimately shown in rows().
 */
abstract class DataTableComponent extends Component
{
    use HandlesRenderErrors;
    use HasBulkActions;
    use HasBulkDelete;
    use HasColumnVisibility;
    use HasExport;
    use HasFilters;
    use HasPagination;
    use HasRowActions;
    use HasSearch;
    use HasSelection;
    use HasSorting;
    use HasStyling;
    use HasUrlBinding;
    use ResolvesDataSource;

    /**
     * The data to display. Supported return types: an Eloquent Builder, a
     * Query Builder, DataSources\RawSql::query(...), a Collection, a plain
     * array, or any class implementing DataSources\DataSource directly.
     *
     * @return \Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Database\Query\Builder|\Salioudiabate\LivewireDatatable\DataSources\RawSql|\Illuminate\Support\Collection<array-key, mixed>|array<int, mixed>|\Salioudiabate\LivewireDatatable\DataSources\DataSource
     */
    abstract public function builder(): mixed;

    /**
     * @return array<int, Column>
     */
    abstract public function columns(): array;

    /**
     * The single coordination point every state-mutating trait calls
     * through: HasPagination provides resetPage(), HasSelection provides
     * clearSelected() — each concern only needs to own the piece it's
     * responsible for.
     */
    protected function onTableStateChanged(): void
    {
        $this->resetPage();
        $this->clearSelected();
    }

    public function render(): View
    {
        return $this->renderTable();
    }
}
