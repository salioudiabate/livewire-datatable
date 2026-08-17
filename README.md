# Livewire DataTable

[![Tests](https://github.com/salioudiabate/livewire-datatable/actions/workflows/tests.yml/badge.svg)](https://github.com/salioudiabate/livewire-datatable/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/salioudiabate/livewire-datatable.svg)](https://packagist.org/packages/salioudiabate/livewire-datatable)
[![License](https://img.shields.io/packagist/l/salioudiabate/livewire-datatable.svg)](LICENSE.md)

A professional, multi-datasource Livewire DataTable for Laravel. Search, sortable columns, a real filter system, per-instance URL binding, bulk selection and delete, column visibility, CSV export and row actions — all built on a `DataSource` abstraction so the same table works against **Eloquent, the Query Builder, raw SQL, or a plain PHP array/Collection**, not just Eloquent.

```php
class UsersTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable(),
            Column::make('Joined', 'created_at')->sortable()
                ->format(fn ($value) => $value->format('d/m/Y')),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Role', 'role')->options(['admin' => 'Admin', 'member' => 'Member']),
        ];
    }
}
```

```blade
<livewire:users-table />
```

## Table of contents

- [Why this package](#why-this-package)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [Data sources](#data-sources)
- [Columns](#columns)
- [Filters](#filters)
- [Sorting](#sorting)
- [URL binding](#url-binding)
- [Selection & bulk actions](#selection--bulk-actions)
- [Bulk delete](#bulk-delete)
- [Column visibility](#column-visibility)
- [Row density](#row-density)
- [Frozen columns](#frozen-columns)
- [Export](#export)
- [Row actions](#row-actions)
- [Theming](#theming)
- [Styling hooks](#styling-hooks)
- [Translations](#translations)
- [Extending to a custom data source](#extending-to-a-custom-data-source)
- [Testing](#testing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

## Why this package

Every one of these features fixes a specific, real gap:

- **Multi-source by design.** A `DataSource` contract (Strategy pattern) sits between the table and the query engine. `EloquentDataSource`, `QueryBuilderDataSource`, `CollectionDataSource` (plain arrays too) and `RawSqlDataSource` all implement it; `DataTableComponent::builder()` can return any of them and the rest of the table doesn't know or care which one it got. Bring your own adapter for anything else via `DataSourceFactory::extend()`.
- **Server-side re-validated sorting.** `sortBy()` re-checks the field against `columns()` on every call — a UI-only restriction (only rendering sort buttons for sortable columns) is not real protection, since Livewire actions are callable by name regardless of what's rendered.
- **Filters that actually do something out of the box.** Every filter type has a working, portable default behavior against the `DataSource` abstraction — no silent no-op if you forget to attach a closure.
- **Per-instance URL binding.** Two tables on the same page never collide in the query string; each gets its own key-prefixed set of query parameters.
- **Fully translatable.** Every user-facing string goes through Laravel's translator — no hardcoded UI text. Ships with `en` (canonical) and `fr` (full parity), publishable so you can add your own locale.
- **Theming without global collisions.** Colors are CSS custom properties scoped to a `.dt-root` wrapper, never `:root` — a table package should never clobber your app's own theme variables.

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- Livewire 4

## Installation

```bash
composer require salioudiabate/livewire-datatable
```

Publish the config (optional — sensible defaults are used otherwise):

```bash
php artisan vendor:publish --tag=livewire-datatable-config
```

Publish the views if you want to customize the markup:

```bash
php artisan vendor:publish --tag=livewire-datatable-views
```

**Tailwind content scanning.** The package's Blade views ship inside `vendor/`, and Tailwind does not scan `vendor/` by default. Add the package's views to your `tailwind.config.js` (or `@source` in a CSS-based Tailwind v4 config) so the classes it uses aren't purged:

```js
// tailwind.config.js
export default {
  content: [
    // ...your own paths
    './vendor/salioudiabate/livewire-datatable/resources/views/**/*.blade.php',
  ],
};
```

```css
/* Tailwind v4, in your main CSS file */
@source '../../vendor/salioudiabate/livewire-datatable/resources/views/**/*.blade.php';
```

Do this **before** anything looks unstyled — it's the most common "installed but the table looks broken" cause for any package that ships Blade views.

## Quick start

```php
use Illuminate\Database\Eloquent\Builder;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataTableComponent;

class UsersTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return User::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable(),
        ];
    }
}
```

That's it — `<livewire:users-table />` renders a searchable, sortable, paginated table. Everything else in this README is opt-in.

Or scaffold the class above with `make:datatable`:

```bash
php artisan make:datatable UsersTable --model=User
```

Omit `--model` for a generic `builder(): mixed` stub instead of a typed Eloquent one — useful when starting from a Query Builder, raw SQL, or array/Collection table. `--force` overwrites an existing file; nested namespaces work via slashes (`make:datatable Admin/UsersTable`).

## Data sources

`builder()` may return any of the following; the right `DataSource` adapter is picked automatically.

**Eloquent:**

```php
public function builder(): Builder
{
    return User::query()->where('active', true);
}
```

**Query Builder:**

```php
use Illuminate\Support\Facades\DB;

public function builder(): \Illuminate\Database\Query\Builder
{
    return DB::table('users')->where('active', true);
}
```

**A plain array or Collection:**

```php
public function builder(): array
{
    return [
        ['id' => 1, 'name' => 'Ada Lovelace'],
        ['id' => 2, 'name' => 'Grace Hopper'],
    ];
}
```

**Raw SQL** — wrapped explicitly via `RawSql::query()` so a bare string is never ambiguous, and bindings stay parameterized (never string-interpolated):

```php
use Salioudiabate\LivewireDatatable\DataSources\RawSql;

public function builder(): RawSql
{
    return RawSql::query(
        'select u.*, count(o.id) as orders_count from users u left join orders o on o.user_id = u.id group by u.id',
    );
}
```

> The wrapped SQL must be a bare `SELECT` with no own `ORDER BY`/`LIMIT` — sorting and pagination are applied by the adapter on top of it as a derived table.

Search, sort, and filters work the same way across all four — with one caveat: bulk delete and custom `Column::searchable(Closure)` / `Filter::using(Closure)` closures are inherently engine-specific (they receive the raw query object), so they only make full sense against Eloquent/Query Builder. Sticking to the closure-free defaults keeps a table fully portable across all four sources.

## Columns

```php
Column::make('Status', 'status')
    ->format(fn ($value, $row) => ucfirst($value))   // custom display
    ->view('partials.status-badge')                   // or a full Blade partial instead of format()
    ->thView('partials.status-header')                 // custom header cell
    ->thClass('text-right')                             // extra <th> classes
    ->searchable()                                       // include in the global search box
    ->searchable(fn ($query, $term) => $query->orWhere('legacy_status', 'like', "%{$term}%"))
    ->sortable()                                         // click-to-sort
    ->sortable('status_rank')                            // sort by a different underlying field
    ->sortUsing(fn ($query, $direction) => $query->orderByRaw('field_order' /* ... */))
    ->exportUsing(fn ($value, $row) => strtoupper($value)) // distinct from format(): CSV cells shouldn't get HTML
    ->toggleable()                                       // user can hide/show this column
    ->toggleable(visibleByDefault: false);                // ...hidden by default
```

`format()` and `exportUsing()` are intentionally separate: a `format()` closure is free to return markup for on-screen display, which would be meaningless (or wrong) written into a CSV cell. Exports use the raw value unless `exportUsing()` is set.

Dot-notation fields (`'author.name'`) work for display and for the default Eloquent search (one level of relation depth via `whereHas`); for anything deeper, use `searchable(Closure)`.

## Filters

Eight built-in types, all sharing the same contract — each owns its own tiny Blade partial and its own portable default behavior (no `getType()` switch anywhere in the core views, so adding a filter type never requires touching the package itself):

```php
use Salioudiabate\LivewireDatatable\Filters\{
    TextFilter, DateFilter, DateRangeFilter, SelectFilter,
    MultiSelectFilter, NumberFilter, NumberRangeFilter, BooleanFilter,
};

public function filters(): array
{
    return [
        TextFilter::make('Search tag', 'tag'),
        SelectFilter::make('Status', 'status')->options(['draft' => 'Draft', 'published' => 'Published']),
        MultiSelectFilter::make('Category', 'category')->options($categories),
        BooleanFilter::make('Active', 'active'),           // tri-state: Any / Yes / No
        DateFilter::make('Created on', 'created_at'),
        DateRangeFilter::make('Created between', 'created_at'),
        NumberFilter::make('Exact price', 'price'),
        NumberRangeFilter::make('Price range', 'price'),
    ];
}
```

Every filter works with no further configuration — `SelectFilter`/`DateFilter`/`NumberFilter` apply an exact match, the range filters apply `>=`/`<=` on whichever bounds are present, `MultiSelectFilter` applies a `whereIn`, `TextFilter` searches its field. For full control, `->using(Closure)` hands you the raw query (or Collection) directly:

```php
SelectFilter::make('Status', 'status')->using(
    fn ($query, $value) => $query->where('status', $value)->orWhere('legacy_status', $value)
);
```

## Sorting

```php
Column::make('Name', 'name')->sortable();
```

Click the header to sort ascending, click again for descending, click a different sortable column to reset to ascending. The field is re-validated against `columns()` on every `sortBy()` call server-side.

## URL binding

Search, filters, sort, and pagination state are bound to the query string by default, with a key prefix derived from the component's class name (`Str::kebab(class_basename(...))`) — so state survives a refresh and is shareable via URL.

Rendering **two instances of the same table class** on one page? Override `urlKey()` on one of them (or both) so they don't share a prefix:

```php
class UsersTableForTeamA extends UsersTable
{
    protected function urlKey(): string
    {
        return 'team-a-users';
    }
}
```

Don't want URL binding for an embedded/modal table at all?

```php
protected function withoutUrlBinding(): bool
{
    return true;
}
```

## Selection & bulk actions

```php
use Salioudiabate\LivewireDatatable\BulkAction;

public function bulkActions(): array
{
    return [
        BulkAction::make('exportSelected', 'Export selected')->icon('download'),
        BulkAction::make('archiveSelected', 'Archive')
            ->confirm('Archive the selected rows?')
            ->cssClass('text-amber-600')
            ->permission('archive-users'),
    ];
}

public function archiveSelected(): void
{
    User::query()->whereIn('id', $this->getSelected())->update(['archived' => true]);
    $this->clearSelection();
}
```

The header checkbox selects the current page only; once every row on the page is checked, a banner offers to expand the selection to every row matching the current search/filters across all pages (`selectAllFiltered()`) — without eagerly fetching every key on a single accidental click.

Every bulk action is dispatched through `runBulkAction($method)`, which re-checks `permission()` before invoking it — the button not being rendered is not the same as the action being protected, since Livewire actions are directly callable by name.

By default, selection is keyed on each row's `id` field. Override `recordKey()` for anything else:

```php
public function recordKey(): string
{
    return 'uuid';
}
```

## Bulk delete

A generic, permission-gated bulk delete is built in — opt in by overriding `deletePermission()`:

```php
public function bulkActions(): array
{
    return [
        BulkAction::make('destroySelected', 'Delete')->permission('delete-users'),
    ];
}

protected function deletePermission(): ?string
{
    return 'delete-users';
}

protected function beforeDelete(mixed $row): bool
{
    return $row->id !== auth()->id(); // veto: never let a user delete themself
}

protected function afterDelete(mixed $row): void
{
    Log::info("Deleted user {$row->id}");
}
```

Rows are deleted one at a time rather than a single mass `DELETE` — on Eloquent this preserves model events/observers, and on both Eloquent and Query Builder it means one row blocked by a foreign key constraint doesn't abort the rest of the batch (the failure is reported back via `reportDeletionSummary(DeletionSummary $summary)`, which you can override to flash a message through your app's own alert system). Bulk delete is only available when the resolved data source is Eloquent or Query Builder — it's not meaningful for a raw-SQL or array-backed table.

> **Two separate checks, set them to the same permission.** `deletePermission()` gates the actual deletion (re-checked server-side on every call, so it's safe on its own) — but the `BulkAction` itself needs its *own* matching `->permission()` too, otherwise the selection checkboxes and delete button still render for everyone, even users who would get a 403 the moment they click. The two are deliberately independent (a `BulkAction` can be permission-gated without going through `destroySelected()` at all), so nothing wires them together automatically.

## Column visibility

```php
Column::make('Internal notes', 'notes')->toggleable(visibleByDefault: false);
```

A "Columns" dropdown appears in the toolbar automatically once at least one column is `toggleable()`. Persist the choice across requests (session-backed) by returning a key from `persistColumnVisibility()`:

```php
protected function persistColumnVisibility(): ?string
{
    return 'users-table';
}
```

## Row density

A compact/comfortable/spacious toggle appears in the toolbar automatically, controlling only the vertical padding of `th`/`td` cells — everything else about the table stays the same. Comfortable is the default:

```php
protected function defaultDensity(): string // or config('livewire-datatable.density.default')
{
    return 'compact';
}
```

Hide the toggle with `showDensityToggle(): false`, or persist the choice across requests (session-backed, same pattern as column visibility) with `persistDensity()`:

```php
protected function persistDensity(): ?string
{
    return 'users-table';
}
```

Padding per mode is configurable globally via `config('livewire-datatable.density')`, or per-table by overriding `densityThClasses()`/`densityTdClasses()`.

## Frozen columns

Pin a leading run of columns so they stay visible while the rest of a wide table scrolls horizontally — useful for an identifying column (name, SKU) on a table with many data columns. Each frozen column needs an explicit pixel width, since there's no way to measure a rendered column's width from PHP and a later frozen column's position depends on the widths of the ones before it:

```php
public function columns(): array
{
    return [
        Column::make('Name', 'name')->frozen(200),
        Column::make('Email', 'email'),
        // ...many more columns that scroll under "Name"
    ];
}
```

Frozen columns must be a **leading, contiguous run** — `Column::make('Email', 'email')->frozen(160)` coming *after* a non-frozen column throws (surfaced as the same friendly error view as any other `columns()` misconfiguration). If bulk actions/selection are enabled, the selection checkbox column is automatically pinned alongside the frozen columns too, so nothing scrolls out from under it.

The reserved width for that checkbox column (`config('livewire-datatable.frozen_checkbox_width')`) and the frozen-cell background/edge-shadow classes (`config('livewire-datatable.classes.frozen_thead_bg')` etc.) are configurable if you've changed the table's overall padding or colors.

## Export

A CSV export button appears in the toolbar automatically (`showExport()`, default `true`). It streams the **current filtered view** (search + filters applied, not just the current page) in chunks — never materializing the whole result set in memory:

```php
protected function exportFilename(): string
{
    return 'users-'.now()->format('Y-m-d').'.csv';
}
```

Want Excel instead of CSV? `Salioudiabate\LivewireDatatable\Export\ExcelExporter` ships with the package (built on [maatwebsite/excel](https://github.com/SpartnerNL/Laravel-Excel), an optional dependency — `composer require maatwebsite/excel` first). Return it from `exporter()`, and give the file an Excel-recognized extension in `exportFilename()`:

```php
use Salioudiabate\LivewireDatatable\Export\ExcelExporter;
use Salioudiabate\LivewireDatatable\Export\Exporter;

protected function exporter(): Exporter
{
    return new ExcelExporter();
}

protected function exportFilename(): string
{
    return 'users-'.now()->format('Y-m-d').'.xlsx';
}
```

It reads the DataSource in the same chunked fashion as `CsvExporter` (never a single `get()`-everything call), and honors `Column::exportUsing()`/`exportValue()` identically. Note this bounds *read* memory only — the XLSX format itself isn't row-streamable, so PhpSpreadsheet still holds the workbook in memory while writing it. For very large exports, prefer CSV.

Need a different format entirely? Implement `Exporter` yourself and return it from `exporter()` — its `export()` method returns a Symfony `Response`, so anything from a streamed CSV to a full file download works.

## Row actions

Purely additive sugar alongside the existing `Column::make('Actions', ...)->view(...)` pattern — use whichever fits:

```php
use Salioudiabate\LivewireDatatable\RowAction;

public function rowActions(): array
{
    return [
        RowAction::make('Edit')->url(fn ($user) => route('users.edit', $user)),
        RowAction::make('Delete')
            ->action('deleteUser')
            ->confirm('Delete this user?')
            ->visible(fn ($user) => $user->id !== auth()->id()),
    ];
}

public function deleteUser(string $id): void
{
    User::query()->whereKey($id)->delete();
}
```

## Theming

Colors are CSS custom properties (`--dt-primary`, `--dt-primary-hover`, `--dt-primary-dark`, `--dt-primary-light`, `--dt-primary-text`) scoped to a `.dt-root` wrapper — never the global `:root` — so the package can never silently override your application's own theme variables.

Set them in `config/livewire-datatable.php`:

```php
'theme' => [
    'primary' => '#4f46e5',
    'primary_hover' => '#4338ca',
    'primary_dark' => '#3730a3',
    'primary_light' => '#eef2ff',
    'primary_text' => '#ffffff',
],
```

If your app already has its own brand color variables, alias them instead of duplicating a palette — either approach works:

```css
/* CSS-level, one line in your own stylesheet */
.dt-root {
    --dt-primary: var(--brand-primary);
    --dt-primary-hover: var(--brand-primary-hover);
}
```

```php
// Config-level — the value is emitted verbatim, so it can itself be a var() reference
'theme' => [
    'primary' => 'var(--brand-primary)',
],
```

Set `'inject_theme_style' => false` in the config if you'd rather define the `--dt-*` variables once in your own compiled CSS instead of having every table instance emit its own `<style>` block.

## Styling hooks

Structural classes are configurable globally (`config('livewire-datatable.classes')`) or per-table by overriding the matching method:

```php
protected function tableWrapperClasses(): string
{
    return 'overflow-x-auto rounded-2xl border border-slate-300';
}
```

Available hooks: `tableWrapperClasses()`, `tableClasses()`, `theadTrClasses()`, `thClasses()`, `tbodyTrClasses()`, `tdClasses()`, `paginationWrapperClasses()`.

## Translations

Every user-facing string is translated — `en` (canonical) and `fr` (full parity) ship with the package. Publish and add your own locale:

```bash
php artisan vendor:publish --tag=livewire-datatable-translations
```

Then edit `lang/vendor/livewire-datatable/{locale}/livewire-datatable.php`.

## Extending to a custom data source

Implement `Salioudiabate\LivewireDatatable\DataSources\DataSource` and register it:

```php
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;

DataSourceFactory::extend(YourQueryType::class, fn ($query) => new YourDataSource($query));
```

Or return an instance implementing `DataSource` directly from `builder()` — it passes straight through the factory untouched, no registration needed.

## Testing

```bash
composer test        # Pest (Unit, Feature, Arch)
composer analyse      # Larastan (level 8)
composer format        # Pint
```

Architecture invariants documented throughout this README (final adapters, traits-not-classes, etc.) are enforced by Pest `arch()` tests, run as part of `composer test`.

A real-browser smoke test suite also exists under `tests/Browser`, driven by [Pest 4's browser testing](https://pestphp.com/docs/browser-testing) (Playwright) against the shipped Blade views and a real `DataTableComponent`: typing into search, clicking to sort, selecting a filter, paginating, and checking a row all get exercised in an actual Chromium page. It's opt-in — not part of `composer test` — since it needs Node and a downloaded Chromium build:

```bash
npm install
npx playwright install chromium

composer test-browser
```

## Security

If you discover a security vulnerability, please don't open a public issue — see [SECURITY.md](SECURITY.md) for how to report it privately.

## Credits

The `Column`/`Filter` naming and fluent chaining style (`Column::make()->sortable()->searchable()`, `TextFilter`/`SelectFilter`/`DateRangeFilter`/etc.) follows conventions established by [rappasoft/laravel-livewire-tables](https://github.com/rappasoft/laravel-livewire-tables), one of the most widely used Livewire table packages in the Laravel ecosystem. No code is shared between the two — this package's `DataSource` abstraction, adapters, traits, and every implementation detail are original — but the API vocabulary owes a clear debt to it, and it's worth a look if you only ever need Eloquent.

## License

MIT. See [LICENSE.md](LICENSE.md).
