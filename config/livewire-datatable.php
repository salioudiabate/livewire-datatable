<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | These values are emitted as CSS custom properties (--dt-primary, ...)
    | scoped to a ".dt-root" wrapper class around every table, so they never
    | collide with a host application's own :root theme variables.
    |
    | Each value may also be a CSS var() reference, which lets you alias the
    | table's palette to your own design system in one line, e.g.:
    |
    |   'primary' => 'var(--brand-primary)',
    |
    */
    'theme' => [
        'primary' => '#4f46e5',
        'primary_hover' => '#4338ca',
        'primary_dark' => '#3730a3',
        'primary_light' => '#eef2ff',
        'primary_text' => '#ffffff',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inject theme style
    |--------------------------------------------------------------------------
    |
    | Whether each table should emit its own <style> block defining the
    | --dt-* variables above. Disable if you'd rather define them once in
    | your own compiled CSS (see the theming docs for both approaches).
    |
    */
    'inject_theme_style' => true,

    /*
    |--------------------------------------------------------------------------
    | Default styling hooks
    |--------------------------------------------------------------------------
    |
    | Structural Tailwind classes used by the default views. Overridable
    | globally here, or per-table by overriding the matching method from
    | Concerns\HasStyling on your DataTableComponent subclass.
    |
    */
    'classes' => [
        // No card/border/shadow by default — a flat, borderless root, the
        // toolbar and table define their own dividers. Available as a hook
        // if you *want* a card look; empty means "get out of the way".
        'root' => '',
        // border-y only (no left/right, no radius, no shadow) is deliberate:
        // a flat divider rather than a boxed-in table.
        'table_wrapper' => 'overflow-x-auto border-y border-slate-200/80 bg-white',
        'table' => 'min-w-full text-sm',
        'thead_tr' => 'border-b border-slate-200 bg-slate-50',
        // Vertical padding is deliberately absent here — it's density-driven
        // (see 'density' below) and composed onto these in the header/body
        // row views, the same way column-specific classes are composed.
        'th' => 'text-left text-xs font-semibold uppercase tracking-wide text-slate-500',
        'tbody_tr' => 'border-b border-slate-100 bg-white transition-colors duration-100 last:border-0 hover:bg-slate-50/60',
        'td' => 'text-slate-700',
        // No horizontal padding of its own — deliberately, so it aligns with
        // table_wrapper (also unpadded) rather than double-padding on top of
        // whatever container the host page already wraps this table in.
        'pagination_wrapper' => 'mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between',

        // Used only by frozen (Column::frozen()) columns and the selection
        // checkbox column when at least one column is frozen: an opaque
        // background so scrolled-under content doesn't bleed through a
        // sticky cell, and a right-edge shadow on the last frozen column.
        'frozen_thead_bg' => 'bg-slate-50',
        'frozen_tbody_bg' => 'bg-white',
        'frozen_edge' => 'shadow-[2px_0_4px_-2px_rgba(0,0,0,0.15)]',

        // The remaining hooks below are full-replace, same as the ones
        // above: whatever you set here (or return from the matching
        // Concerns\HasStyling method override) becomes the *entire* class
        // list for that compartment — structural utilities (flex,
        // overflow-hidden, etc.) that make it render/behave correctly are
        // your responsibility to keep if you touch these.
        'toolbar' => 'mb-5 flex flex-wrap items-start justify-between gap-3',
        'filters_panel' => 'mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4',
        // Filter inputs — three shapes shared across all 8 filter types
        // (see Concerns\HasStyling for which partial uses which), plus one
        // label class common to all of them. Overridable per-filter via
        // Filter::cssClass(), which takes precedence over these.
        'filter_label' => 'text-xs font-medium text-slate-600',
        'filter_input' => 'w-full min-w-0 rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 transition-colors duration-150 placeholder:text-slate-400 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]',
        'filter_select' => 'w-full appearance-none bg-none rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm text-slate-700 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]',
        'filter_multiselect' => 'w-full min-w-0 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 transition-colors duration-150 hover:border-slate-300 focus:border-[var(--dt-primary,#4f46e5)] focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]',
        'bulk_actions_bar' => 'mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-[var(--dt-primary,#4f46e5)] bg-[var(--dt-primary-light,#eef2ff)] px-4 py-2.5 text-sm',
        'selection_banner' => 'mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-[var(--dt-primary,#4f46e5)] bg-[var(--dt-primary-light,#eef2ff)] px-4 py-2.5 text-sm text-slate-700',
        'empty_state' => 'px-4 py-10 text-center',
        // footer() summary blocks — one row of label/value pills between the
        // table and pagination. mt-4 keeps it off the table's bottom edge
        // (table_wrapper carries no bottom margin of its own); justify-between
        // so items with align: 'left' and align: 'right' split to opposite
        // ends, like the bulk actions bar / selection banner above them.
        'footer_wrapper' => 'mt-4 mb-4 flex flex-wrap items-center justify-between gap-2',
        'footer_item' => 'inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 shadow-sm shadow-slate-100',
        // z-20: must outrank the sticky header's z-index: 10 (HasStickyHeader)
        // — equal values would let the header win on DOM order and paint
        // over this panel, since <thead> renders after the toolbar.
        'columns_dropdown' => 'absolute right-0 z-20 mt-1 w-48 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg shadow-slate-100',
        'error_state' => 'rounded-xl border border-red-200 bg-red-50 p-6 text-center',

        // Custom toolbar buttons/groups declared via toolbarActions().
        // Overridable per-item via ToolbarAction::cssClass() /
        // ToolbarActionGroup::cssClass(), same precedence as bulk actions.
        // No h-9 here: this button carries its own border, so a fixed
        // height would be the *total* box height (border-box), landing
        // 2px shorter than Filters/Export/Columns — those resolve their
        // height from py-2 + text-sm's line-height plus their border, so
        // matching that exact recipe (not a fixed height) is what keeps
        // them pixel-identical.
        'toolbar_action' => 'flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition-colors duration-150 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[var(--dt-primary,#4f46e5)]',
        // h-9 lives on each segmented item instead (see toolbar-action.blade.php),
        // not on this wrapper — same two-tier pattern as the density
        // toggle: an auto-height wrapper lets its own border add on top
        // of the items' fixed height, landing at the same 38px total.
        'toolbar_action_group' => 'flex items-center divide-x divide-slate-200 overflow-hidden rounded-lg border border-slate-200 bg-white',
        // The open menu panel for ToolbarActionGroup::dropdown() — same
        // look as the built-in Columns dropdown panel. z-20 for the same
        // reason as columns_dropdown above: must outrank the sticky
        // header's z-index: 10.
        'toolbar_action_dropdown' => 'absolute right-0 z-20 mt-1 w-48 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg shadow-slate-100',

        // Global only — Laravel renders the paginator's view (tailwind.blade.php
        // / simple-tailwind.blade.php) in its own context, outside the
        // component's Blade scope, so there's no per-table HasStyling method
        // for this one. Applies to every table in the app. Deliberately
        // excludes display utilities (inline-flex/hidden) — each usage site
        // appends its own, since the desktop bar is responsively hidden on
        // mobile and the simple (Previous/Next only) bar isn't.
        'pagination_bar' => 'relative z-0 overflow-hidden rounded-lg border border-slate-200',
    ],

    /*
    |--------------------------------------------------------------------------
    | Row density
    |--------------------------------------------------------------------------
    |
    | Vertical (and, for compact, horizontal) padding per density mode,
    | selectable at runtime via the toolbar's density toggle. Overridable
    | globally here, or per-table via Concerns\HasDensity's
    | densityThClasses()/densityTdClasses().
    |
    */
    'density' => [
        'default' => 'comfortable',
        'th' => [
            'compact' => 'px-3 py-1.5',
            'comfortable' => 'px-4 py-3',
            'spacious' => 'px-5 py-4',
        ],
        'td' => [
            'compact' => 'px-3 py-1.5',
            'comfortable' => 'px-4 py-3',
            'spacious' => 'px-5 py-4',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Frozen columns
    |--------------------------------------------------------------------------
    |
    | Reserved pixel width for the selection checkbox column, used to offset
    | frozen (Column::frozen()) data columns so they don't render underneath
    | it. Only relevant when bulk actions/selection are enabled.
    |
    */
    'frozen_checkbox_width' => 44,

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Whether the package should register its themed pagination view as the
    | default Tailwind pagination view for the whole application (via
    | Paginator::useTailwind()). Disable if your app already ships its own
    | vendor/livewire/tailwind.blade.php override.
    |
    */
    'register_pagination_view' => true,

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
    'export' => [
        'chunk_size' => 1000,
    ],

];
