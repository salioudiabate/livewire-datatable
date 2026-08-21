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

    /**
     * Wrapper around the footer() summary blocks, rendered below the table
     * and above pagination.
     */
    public function footerWrapperClasses(): string
    {
        return (string) config('livewire-datatable.classes.footer_wrapper', '');
    }

    /**
     * Each individual footer() block ({label, value} pill).
     */
    public function footerItemClasses(): string
    {
        return (string) config('livewire-datatable.classes.footer_item', '');
    }

    /**
     * The wire:offline connection-lost banner — see Concerns\HasOfflineIndicator.
     */
    public function offlineBannerClasses(): string
    {
        return (string) config('livewire-datatable.classes.offline_banner', '');
    }

    /**
     * The <label> above a filter's input(s) — shared by every filter type.
     */
    public function filterLabelClasses(): string
    {
        return (string) config('livewire-datatable.classes.filter_label', '');
    }

    /**
     * Default classes for a plain text-like filter input: TextFilter,
     * NumberFilter, DateFilter, and both halves of NumberRangeFilter/
     * DateRangeFilter. Overridden per-filter by Filter::cssClass() when set.
     */
    public function filterInputClasses(): string
    {
        return (string) config('livewire-datatable.classes.filter_input', '');
    }

    /**
     * Default classes for a single-value dropdown filter: SelectFilter and
     * BooleanFilter — both render a <select> with a custom chevron overlay.
     * Overridden per-filter by Filter::cssClass() when set.
     */
    public function filterSelectClasses(): string
    {
        return (string) config('livewire-datatable.classes.filter_select', '');
    }

    /**
     * Default classes for MultiSelectFilter's native <select multiple> —
     * kept distinct from filterSelectClasses() since it has no chevron
     * overlay and different vertical padding. Overridden per-filter by
     * Filter::cssClass() when set.
     */
    public function filterMultiSelectClasses(): string
    {
        return (string) config('livewire-datatable.classes.filter_multiselect', '');
    }
}
