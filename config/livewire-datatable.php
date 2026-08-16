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
        'table_wrapper' => 'overflow-x-auto rounded-xl border border-slate-200 bg-white',
        'table' => 'min-w-full divide-y divide-slate-200 text-sm',
        'thead_tr' => 'bg-slate-50',
        'th' => 'px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500',
        'tbody_tr' => 'divide-y divide-slate-100 bg-white',
        'td' => 'px-4 py-3 text-slate-700',
        'pagination_wrapper' => 'flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between',
    ],

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
