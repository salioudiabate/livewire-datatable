# Changelog

All notable changes to `livewire-datatable` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.0] - 2026-08-21

### Added

- `ToolbarActionGroup::dropdown()` — a segmented group can now render as a dropdown menu instead of a segmented pill bar, with a chevron indicator and its own `ToolbarActionGroup::icon()`.
- `ToolbarAction`/`RowAction`/`BulkAction::submit()` — a real, non-AJAX HTML form POST for server work that must return a full HTTP response (a generated PDF opened in a new tab, a file download), with automatic CSRF/`_method` spoofing, `confirm()` support, and a loading-state spinner.
- `stickyHeader()` / `stickyHeaderMaxHeight()` — keeps the header row visible while a tall table scrolls internally.
- `Export\PdfExporter` (`barryvdh/laravel-dompdf`, optional) — a print-friendly PDF alternative to CSV/Excel.
- `emptyStateView()` — replace the built-in "No results" message with a custom Blade partial.
- `pollInterval()` — `wire:poll`-based auto-refresh.
- `rowUrl()` — click-anywhere-on-the-row navigation, skipping interactive elements.
- `footer()` — free-form label/value summary blocks below the table, computed against the full filtered result via `DataSource::aggregate()`.
- `AsyncSelectFilter` — a `SelectFilter` whose options are resolved on demand from a search term (`optionsUsing()`/`labelUsing()`) instead of a fixed array, for option sets too large to load eagerly.
- Filter input styling: `filterLabelClasses()`, `filterInputClasses()`, `filterSelectClasses()`, `filterMultiSelectClasses()`, plus a per-filter `Filter::cssClass()` override — every filter input's classes were previously hardcoded per Blade partial.
- Documented and permanently tested the dependent/cascading filters pattern (country → town): no new API, `filters()` already re-reads `$this->filterValues` on every render and Livewire's own `updatedFilterValuesX()` nested hook clears a stale child value.
- `visibleRowKeys()`, `allFilteredKeys()` (now public), `allFilteredRows(int $chunkSize = 1000)` — public access to the current page's or every filtered row's keys/data, for building a custom `toolbarActions()`/`bulkActions()`/`rowActions()` method without reimplementing `DataSource` pagination by hand.
- A soft, flat, `wire:offline`-based connection-lost banner, on by default (`showOfflineIndicator()`, `offlineBannerClasses()`).
- `filtersLabel()`, `exportLabel()`, `searchPlaceholder()` — previously fixed toolbar strings, now overridable per table.

### Fixed

- **Sticky header never actually worked.** The original `stickyHeaderOffset()` design assumed `position: sticky` relative to the page, but `table_wrapper`'s existing `overflow-x-auto` forces the computed `overflow-y` to `auto` too per the CSS Overflow spec, silently making the wrapper its own scroll container — a page-relative sticky offset never engaged. Redesigned around `stickyHeaderMaxHeight()`: a bounded-height wrapper that scrolls internally on both axes, with the header pinned to that scrollport's own top.
- The toolbar and Columns dropdown panels shared the sticky header's exact `z-index: 10` — ties go to later DOM order, so an open dropdown menu painted *under* the sticky header instead of over it. Both bumped to `z-20`.
- `RowAction`/`BulkAction::icon()` was stored but never rendered outside a `submit()` form — the regular `wire:click`/`url()` branches silently dropped it.
- Custom toolbar buttons and the density toggle rendered 2px shorter than the built-in Filters/Export/Columns buttons (a fixed `h-9` computes as the *total* border-box height on an element that also carries its own border, landing short of the `py-2`-based recipe the other buttons use).

### Security

- `AsyncSelectFilter` option buttons and `RowAction`'s `confirm()`/`action()` triggers built `wire:click` attributes via raw Blade interpolation instead of `@js()`. Blade's `{{ }}` only escapes for the HTML-attribute context; the browser decodes those entities back before Livewire's own action-call parser ever sees the value, so a developer-supplied option key or a `recordKey()` resolving to free text (a name, a slug) could break out of the intended single argument. Fixed everywhere in the package via `@js()`, which is immune regardless of quoting style.
- `EloquentDataSource`/`QueryBuilderDataSource::aggregate()` now validate `$function` against `sum`/`avg`/`min`/`max`/`count` before the dynamic `$query->{$function}(...)` dispatch, matching `CollectionDataSource`'s existing guard — closes an arbitrary-method-call gadget on the underlying query builder that was unreachable by user input today, but had no defense if that ever changed.
- `RowAction::visible()` was previously enforced at render time only — the underlying method stayed directly callable via Livewire regardless. Added `runRowAction()`, symmetric with the existing `runToolbarAction()`/`runBulkAction()`, which re-resolves the real row from the current page and re-checks `visible()` before invoking the method.

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
