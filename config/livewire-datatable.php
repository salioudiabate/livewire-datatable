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
        'pagination_wrapper' => 'flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between',

        // Used only by frozen (Column::frozen()) columns and the selection
        // checkbox column when at least one column is frozen: an opaque
        // background so scrolled-under content doesn't bleed through a
        // sticky cell, and a right-edge shadow on the last frozen column.
        'frozen_thead_bg' => 'bg-slate-50',
        'frozen_tbody_bg' => 'bg-white',
        'frozen_edge' => 'shadow-[2px_0_4px_-2px_rgba(0,0,0,0.15)]',
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
