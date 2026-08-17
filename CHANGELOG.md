# Changelog

All notable changes to `livewire-datatable` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
