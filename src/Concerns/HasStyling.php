<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

/**
 * Structural CSS classes for every part of the table, sourced from the
 * publishable config by default and overridable per-table by redeclaring
 * any of these methods on a concrete DataTableComponent subclass.
 */
trait HasStyling
{
    public function tableWrapperClasses(): string
    {
        return (string) config('livewire-datatable.classes.table_wrapper', '');
    }

    public function tableClasses(): string
    {
        return (string) config('livewire-datatable.classes.table', '');
    }

    public function theadTrClasses(): string
    {
        return (string) config('livewire-datatable.classes.thead_tr', '');
    }

    public function thClasses(): string
    {
        return (string) config('livewire-datatable.classes.th', '');
    }

    public function tbodyTrClasses(): string
    {
        return (string) config('livewire-datatable.classes.tbody_tr', '');
    }

    public function tdClasses(): string
    {
        return (string) config('livewire-datatable.classes.td', '');
    }

    public function paginationWrapperClasses(): string
    {
        return (string) config('livewire-datatable.classes.pagination_wrapper', '');
    }
}
