# Changelog

All notable changes to `livewire-datatable` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2026-08-18

### Added

- `Concerns\HasStyling::rootClasses()` — a styling hook for the single outer wrapper around the toolbar, table, and pagination (default: empty, flat, no card).

### Fixed

- The per-page/boolean/select filter `<select>` elements' custom chevron icon overlapped with Tailwind Forms' own auto-injected background-image arrow when both were present. `bg-none` now cancels the latter.
- Sortable column headers render as `<button>`, and Tailwind's preflight resets `button { text-transform: none }`, silently cancelling the `uppercase` inherited from the header cell — only non-sortable headers actually appeared uppercase. Applied directly on the button now.

### Changed

- Toolbar controls (search, Filters, Columns, Export, density toggle, per-page select) now share one consistent flat recipe (`rounded-lg`, `border-slate-200`, `py-2`, no shadow) instead of drifting sizes and padding.
- Pagination, dropdowns, the filters panel, and selection/bulk-action banners follow the same flat philosophy: no shadow on inline controls, `shadow-lg shadow-slate-100` reserved for floating popovers, `rounded-xl` for panels, `rounded-lg` for inline controls.

## [1.1.0] - 2026-08-17

### Added

- `make:datatable` Artisan command to scaffold a `DataTableComponent` subclass, with `--model` for a typed Eloquent `builder()`, `--force` to overwrite, and nested namespaces via slashes.
- Pest `arch()` test suite enforcing the package's own architectural invariants (strict types, final adapters/exceptions/filters, traits-not-classes, no debug statements).
- Opt-in Pest 4 browser test suite (`composer test-browser`), real end-to-end smoke coverage in a headless Chromium page.
- `SECURITY.md` with private vulnerability disclosure instructions.
- Official `ExcelExporter` (`Salioudiabate\LivewireDatatable\Export\ExcelExporter`), built on `maatwebsite/excel` (optional dependency), reading the `DataSource` in the same chunked fashion as `CsvExporter`.
- Row density mode (compact/comfortable/spacious), a toolbar toggle controlling `th`/`td` padding, session-persistable via `persistDensity()`.
- Frozen (sticky) columns via `Column::frozen(int $width)` — pins a leading, contiguous run of columns while the rest of a wide table scrolls horizontally.

### Changed

- `Export\Exporter::export()` now returns the common Symfony `Response` instead of `StreamedResponse` specifically, to also allow a `BinaryFileResponse` (what `ExcelExporter` returns). A custom `Exporter` implementation declaring `StreamedResponse` as its own return type is unaffected — that's still a valid covariant override of the widened interface.

## [1.0.0] - 2026-08-16

Initial release.

### Added

- `DataSource` contract (Strategy/Adapter pattern) with four built-in adapters — `EloquentDataSource`, `QueryBuilderDataSource`, `CollectionDataSource` (arrays and Collections), `RawSqlDataSource` — plus `DataSourceFactory::extend()` for custom adapters.
- `Column` fluent builder: `format()`, `view()`, `thView()`, `thClass()`, `searchable()`, `sortable()`, `sortUsing()`, `exportUsing()`, `toggleable()`.
- Eight built-in `Filter` types (`TextFilter`, `DateFilter`, `DateRangeFilter`, `SelectFilter`, `MultiSelectFilter`, `NumberFilter`, `NumberRangeFilter`, `BooleanFilter`), each with a portable closure-free default and a `using()` escape hatch, each owning its own Blade partial.
- Global search, server-side re-validated sorting, per-instance URL binding via `queryString()`.
- Row selection (current-page + "select all filtered across pages"), declarative `BulkAction`, permission-gated generic bulk delete with driver-aware foreign key violation detection (Postgres, MySQL, SQLite).
- Column visibility toggling with optional session persistence.
- Streamed, chunked CSV export; `Exporter` interface for a custom (e.g. Excel) implementation.
- Declarative `RowAction` for per-row dropdowns, additive alongside custom `Column::view()` actions columns.
- Theming via `--dt-*` CSS custom properties scoped to `.dt-root`, configurable and alias-able to a host app's own design system.
- Full English and French translations, no hardcoded UI strings.
- Ported, brand-themeable segmented-pill pagination view registered as the app-wide default Tailwind pagination view.
