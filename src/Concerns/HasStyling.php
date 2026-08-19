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
    /**
     * Classes for the single outer card wrapping the toolbar, table, and
     * pagination — one unified soft-shadowed container rather than each
     * piece carrying its own border/radius.
     */
    public function rootClasses(): string
    {
        return (string) config('livewire-datatable.classes.root', '');
    }

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

    public function toolbarClasses(): string
    {
        return (string) config('livewire-datatable.classes.toolbar', '');
    }

    public function filtersPanelClasses(): string
    {
        return (string) config('livewire-datatable.classes.filters_panel', '');
    }

    public function bulkActionsBarClasses(): string
    {
        return (string) config('livewire-datatable.classes.bulk_actions_bar', '');
    }

    public function selectionBannerClasses(): string
    {
        return (string) config('livewire-datatable.classes.selection_banner', '');
    }

    public function emptyStateClasses(): string
    {
        return (string) config('livewire-datatable.classes.empty_state', '');
    }

    /**
     * A view name to render instead of the built-in "No results found"
     * message — receives $columns and $colspan, the same as the default
     * partial, so a custom one can still colspan the row correctly. Useful
     * for a call-to-action ("No products yet — create one") rather than a
     * flat empty message. Returning null (the default) keeps the built-in
     * message.
     */
    public function emptyStateView(): ?string
    {
        return null;
    }

    public function columnsDropdownClasses(): string
    {
        return (string) config('livewire-datatable.classes.columns_dropdown', '');
    }

    public function errorStateClasses(): string
    {
        return (string) config('livewire-datatable.classes.error_state', '');
    }

    /**
     * Default classes for a standalone ToolbarAction button — overridden
     * per-action by ToolbarAction::cssClass() when set.
     */
    public function toolbarActionClasses(): string
    {
        return (string) config('livewire-datatable.classes.toolbar_action', '');
    }

    /**
     * Wrapper classes for a ToolbarActionGroup's segmented control —
     * overridden per-group by ToolbarActionGroup::cssClass() when set.
     */
    public function toolbarActionGroupClasses(): string
    {
        return (string) config('livewire-datatable.classes.toolbar_action_group', '');
    }

    /**
     * The open menu panel for a ToolbarActionGroup::dropdown() — the
     * trigger button itself uses toolbarActionClasses()/cssClass() like
     * any other standalone action.
     */
    public function toolbarActionDropdownClasses(): string
    {
        return (string) config('livewire-datatable.classes.toolbar_action_dropdown', '');
    }
}
