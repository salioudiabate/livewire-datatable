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
- [Search & pagination](#search--pagination)
- [Auto-refresh](#auto-refresh)
- [Connection status](#connection-status)
- [URL binding](#url-binding)
- [Selection & bulk actions](#selection--bulk-actions)
- [Bulk delete](#bulk-delete)
- [Column visibility](#column-visibility)
- [Row density](#row-density)
- [Frozen columns](#frozen-columns)
- [Sticky header](#sticky-header)
- [Footer](#footer)
- [Export](#export)
- [Row actions](#row-actions)
- [Clickable rows](#clickable-rows)
- [Toolbar actions](#toolbar-actions)
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
    ->tdClass('text-right tabular-nums')                 // extra <td> classes, for this column only
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

Nine built-in types, all sharing the same contract — each owns its own tiny Blade partial and its own portable default behavior (no `getType()` switch anywhere in the core views, so adding a filter type never requires touching the package itself):

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

The "Filters" toolbar button's own label comes from the `filters` translation by default — override `filtersLabel(): string` per-table for anything else.

### Async select (searchable, remote options)

`SelectFilter`/`MultiSelectFilter` need every option loaded upfront — fine for a status enum, unusable for a "Customer" filter over 50k rows. `AsyncSelectFilter` resolves its options on demand from whatever's currently typed in its search box, instead of a fixed array:

```php
use Salioudiabate\LivewireDatatable\Filters\AsyncSelectFilter;

AsyncSelectFilter::make('Client', 'customer_id')
    ->optionsUsing(fn (string $term) => Customer::query()
        ->when($term !== '', fn ($query) => $query->where('name', 'like', "%{$term}%"))
        ->limit(20)
        ->pluck('name', 'id')
        ->all())
    ->labelUsing(fn (mixed $value) => Customer::find($value)?->name),
```

`optionsUsing()` receives the current search term (empty string until the box has been typed in) and returns `[value => label, ...]`, same shape as `SelectFilter::options()`. It re-runs on every render while the box is focused (300ms debounce, same as `TextFilter`) — the same DataTableComponent render cycle as everything else, no separate route or controller to wire up.

The tricky part any async select has to solve: once a value is selected, the option list that produced it is gone — a fresh search for `''` (or whatever's currently typed) may not include it at all. `labelUsing()` resolves *just that one value* back to a label for display, independently of the current search. Skip it and the raw stored value is shown instead — fine for something already legible (a slug), not for a database id.

`apply()` behavior is identical to `SelectFilter`: an exact match, `null`/`''` treated as inactive.

### Dependent (cascading) filters

Country → town → district, category → subcategory: there's no dedicated cascading API, but nothing needs to exist for one — `filters()` is a plain method re-invoked on every render, with full access to `$this->filterValues`, so a child filter's options can already be computed from whatever the parent is currently set to:

```php
public function filters(): array
{
    $country = $this->filterValues['country'] ?? null;

    return [
        SelectFilter::make('Country', 'country')->options(Country::pluck('name', 'code')->all()),
        SelectFilter::make('Town', 'town')->options(
            $country ? Town::where('country_code', $country)->pluck('name', 'id')->all() : []
        ),
    ];
}
```

The one thing a real cascade needs beyond that — clearing the child's now-stale value once the parent changes — isn't a package concern either: updating a nested property like `filterValues.country` fires `updatedFilterValuesCountry()` natively, a built-in Livewire hook for any dotted property path:

```php
public function updatedFilterValuesCountry(): void
{
    unset($this->filterValues['town']);
}
```

Both pieces compose with `AsyncSelectFilter` too — `optionsUsing()`'s closure has the same `$this` access, so a search box can filter by both the current term *and* a parent value at once.

### Styling filter inputs

Every filter input is styled from config by default (`filterLabelClasses()`, `filterInputClasses()`, `filterSelectClasses()`, `filterMultiSelectClasses()` — see [Styling hooks](#styling-hooks)), with a per-filter escape hatch on top:

```php
SelectFilter::make('Statut', 'status')
    ->options(['draft' => 'Brouillon', 'published' => 'Publié'])
    ->cssClass('rounded-lg border border-amber-300 bg-amber-50 py-2 pl-3 pr-8 text-sm text-amber-900'),
```

`cssClass()` full-replaces that one filter's input classes (both halves, for a range filter) — it doesn't merge with the default, same as every other `cssClass()` hook in the package. Which default it replaces depends on the filter's shape: `filterInputClasses()` for text/number/date/range inputs, `filterSelectClasses()` for `SelectFilter`/`BooleanFilter`'s single dropdown, `filterMultiSelectClasses()` for `MultiSelectFilter`'s native multi-select (no chevron overlay, so it needs different padding than a single select), and `filterSelectClasses()` again for `AsyncSelectFilter`'s trigger button.

## Sorting

```php
Column::make('Name', 'name')->sortable();
```

Click the header to sort ascending, click again for descending, click a different sortable column to reset to ascending. The field is re-validated against `columns()` on every `sortBy()` call server-side.

## Search & pagination

Both are on by default and both hide their toolbar control the same way:

```php
public function showSearch(): bool   // default true
{
    return false;
}

public function showPerPage(): bool  // default true
{
    return false;
}
```

The search input's placeholder comes from the `search` translation by default — override `searchPlaceholder(): string` per-table for anything else.

The per-page dropdown's own choices (`[10, 25, 50, 100]` by default) are overridable too:

```php
public function perPageOptions(): array
{
    return [15, 30, 60];
}
```

## Auto-refresh

Off by default — opt in for dashboard-style tables that should refresh themselves:

```php
public function pollInterval(): ?int
{
    return 5000; // milliseconds
}
```

Wires Livewire's own `wire:poll` onto the table rather than reinventing polling, so all of Livewire's usual polling behavior applies (it pauses in a background browser tab, for instance).

## Connection status

A soft, flat amber banner appears automatically when the browser goes offline, and disappears once back online — on by default:

```php
public function showOfflineIndicator(): bool
{
    return false; // opt out per-table
}
```

Pure Livewire under the hood — `wire:offline` is hidden via a `display: none` CSS rule Livewire itself injects globally, then shown/hidden by listening to the native browser `offline`/`online` events. No custom JS, no polling to detect it. Style it like any other compartment via `offlineBannerClasses()` (see [Styling hooks](#styling-hooks)).

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
        BulkAction::make('exportSelected', 'Export selected')
            ->icon('<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M7 10l5 5 5-5M12 15V3" /></svg>'),
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

`icon()` (also on `RowAction` and `ToolbarAction`) takes raw inline SVG/HTML, not an icon name — there's no icon-name registry anywhere in the package, so you bring your own markup, the same as every built-in button.

The header checkbox selects the current page only; once every row on the page is checked, a banner offers to expand the selection to every row matching the current search/filters across all pages (`selectAllFiltered()`) — without eagerly fetching every key on a single accidental click.

Every bulk action is dispatched through `runBulkAction($method)`, which re-checks `permission()` before invoking it — the button not being rendered is not the same as the action being protected, since Livewire actions are directly callable by name.

Need to hand the current selection to a real server-side export instead — a PDF, a large CSV, anything that has to return a full HTTP response? `BulkAction::submit($action)` renders a real form post (the selected keys go along automatically as `selected[]`) instead of a `wire:click` — see [Toolbar actions](#toolbar-actions) for how `submit()` works and why it bypasses `runBulkAction()`'s re-check.

By default, selection is keyed on each row's `id` field. Override `recordKey()` for anything else:

```php
public function recordKey(): string
{
    return 'uuid';
}
```

### Accessing row and filter data for your own custom actions

Everything a `toolbarActions()`/`bulkActions()`/`rowActions()` method needs to build a custom action of its own — beyond `getSelected()` above — is already public:

| Method / property | Scope | Returns |
|---|---|---|
| `rows()` | current page | the `LengthAwarePaginator` the table itself renders — `->items()` for the row data |
| `visibleRowKeys()` | current page | resolved keys only, same scope as `rows()` |
| `allFilteredKeys()` | every matching row, all pages | resolved keys only — the same building block `selectAllFiltered()` uses internally |
| `allFilteredRows(int $chunkSize = 1000)` | every matching row, all pages | full row data, as a `Collection` — fetched in bounded chunks (same technique `Export\CsvExporter` uses), but still materializes the whole result at the end. Fine for a custom action over a realistically-sized filtered result; for exporting a genuinely large table, use `Export\Exporter` instead, which streams rather than materializing |
| `$this->filterValues` | — | the raw current state of every filter, keyed by `Filter::key()`/`RangeFilter`'s derived `_from`/`_to` keys |
| `$this->filters()` | — | the `Filter` objects themselves — `->label()`, `->key()`, `->isActive($this->filterValues)` |

```php
public function bulkActions(): array
{
    return [
        BulkAction::make('emailAllFiltered', 'Email everyone matching this search'),
    ];
}

public function emailAllFiltered(): void
{
    foreach ($this->allFilteredRows() as $user) {
        Mail::to($user)->queue(new WeeklyDigest);
    }
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

Padding per mode is configurable globally via `config('livewire-datatable.density')`, or per-table by overriding `densityThClasses()`/`densityTdClasses()`. Want fewer than the three built-in modes (e.g. no "spacious")? Override `densityOptions(): array` to return a subset of `['compact', 'comfortable', 'spacious']`.

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

## Sticky header

The vertical counterpart to frozen columns — keeps the header row visible while a *tall* table scrolls, instead of the header scrolling away with the rest of the content. Off by default:

```php
public function stickyHeader(): bool
{
    return true;
}
```

This can't stick relative to the page the way a naive `position: sticky` might suggest — `table_wrapper` already carries `overflow-x-auto` for horizontal scroll (frozen columns, wide tables), and per the CSS Overflow spec, setting `overflow-x` to anything but `visible` forces the computed `overflow-y` to `auto` too. The wrapper silently becomes its own scroll container regardless, so a sticky cell inside it sticks relative to *that* container, not the page. Rather than fight this, `stickyHeader()` embraces it: the wrapper gets a bounded height and scrolls internally on both axes, with the header pinned to the top of that same scrollport:

```php
protected function stickyHeaderMaxHeight(): string
{
    return '600px'; // any valid CSS length — default '70vh'
}
```

Combines correctly with frozen columns — a column that's both frozen and in a sticky header gets both `position: sticky` offsets (`left` and `top`) at once, not one replacing the other.

## Footer

Optional summary blocks — a count, a total, an average — rendered as a row of label/value pills below the table, above pagination. Empty by default, so nothing renders unless you override it:

```php
public function footer(): array
{
    $count = $this->filteredDataSource()->count();

    if ($count === 0) {
        return [];
    }

    return [
        ['label' => 'Products', 'value' => (string) $count, 'align' => 'left'],
        ['label' => 'Total stock value', 'value' => number_format(
            (float) $this->filteredDataSource()->aggregate('sum', 'price'), 2
        ).' €'],
    ];
}
```

Each item is `['label' => string, 'value' => string, 'align' => 'left'|'right']` — `align` defaults to `'right'` when omitted, so items split into two groups at opposite ends of the row, the same way the bulk actions bar does.

This is deliberately free-form rather than tied to a specific `Column` — a table can show fewer, more, or differently-computed blocks than it has columns (e.g. one combined "Revenue: X · Expenses: Y · Margin: Z" block instead of one per column). Compute values against `filteredDataSource()` (protected, inherited from `ResolvesDataSource`), not `rows()` — it reflects search and filters but not pagination, so a sum here covers every matching row, not just the current page. `DataSource::aggregate('sum'|'avg'|'min'|'max'|'count', $column)` does this without loading the full result set into memory on the Eloquent/Query Builder/RawSql adapters. For anything the four built-in aggregate functions can't express, `filteredDataSource()->raw()` hands you the underlying query/collection directly.

## Export

A CSV export button appears in the toolbar automatically (`showExport()`, default `true`). Its label comes from the `export` translation by default — override `exportLabel(): string` per-table for anything else. It streams the **current filtered view** (search + filters applied, not just the current page) in chunks — never materializing the whole result set in memory:

```php
protected function exportFilename(): string
{
    return 'users-'.now()->format('Y-m-d').'.csv';
}
```

By default the export includes whatever columns are currently visible (respecting column visibility toggles) — override `exportColumns(): array` to export a different set entirely, e.g. including a column the user has hidden on screen:

```php
protected function exportColumns(): array
{
    return $this->columns(); // ignore visibility toggles, always export everything
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

Want a printable PDF instead? `Salioudiabate\LivewireDatatable\Export\PdfExporter` ships with the package (built on [barryvdh/laravel-dompdf](https://github.com/barryvdh/laravel-dompdf), an optional dependency — `composer require barryvdh/laravel-dompdf` first):

```php
use Salioudiabate\LivewireDatatable\Export\PdfExporter;

protected function exporter(): Exporter
{
    return new PdfExporter();
}

protected function exportFilename(): string
{
    return 'users-'.now()->format('Y-m-d').'.pdf';
}
```

Unlike CSV or Excel, dompdf has no streaming render API at all — the full row set is held in memory and the whole document rendered before a single byte is sent, bounding neither read nor write memory the way the other two exporters do. This is meant for a print-friendly report on a reasonably sized (typically already-filtered) result set, not bulk data export — prefer `CsvExporter` for that. It's also the natural pairing for `submit()` (see [Toolbar actions](#toolbar-actions)) when you want the PDF to open in a new tab from a real form post rather than a Livewire-driven download.

Need a different format entirely? Implement `Exporter` yourself and return it from `exporter()` — its `export()` method returns a Symfony `Response`, so anything from a streamed CSV to a full file download works.

The chunk size both built-in exporters read at a time is `config('livewire-datatable.export.chunk_size')` (default `1000`) — lower it if individual rows are unusually large.

Need the export to hand back a real file response from your *own* route instead — server-side PDF generation, say — rather than a Livewire-driven download? See `submit()` under [Toolbar actions](#toolbar-actions).

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

Need a per-row action that hands back a real HTTP response instead of an AJAX diff (a per-row generated PDF, say)? `RowAction::submit()` works the same way as `ToolbarAction::submit()` — see [Toolbar actions](#toolbar-actions).

`->action($method)` is dispatched through `runRowAction($method, $key)`, which re-resolves the actual row from the current page and re-checks `visible()` against it before invoking the method — the same defensive pattern as `runToolbarAction()`/`runBulkAction()`. `visible()` isn't just a rendering filter: a "Delete" button hidden for a given row via `visible()` genuinely can't be triggered for that row either, even by a client calling the underlying method directly.

## Clickable rows

The whole "click anywhere on the row to open it" pattern, independent of `RowAction` — off by default:

```php
public function rowUrl(mixed $row): ?string
{
    return route('users.edit', $row);
}
```

A click anywhere on the row navigates, *except* when it lands on an interactive element inside it (a checkbox, a row action button, a link in a formatted cell) — those still work normally, the row-level navigation only fires for clicks that don't land on one. Return `null` for a specific row (an archived one, say) to leave just that row non-clickable while the rest of the table still navigates.

## Toolbar actions

Custom buttons in the toolbar itself — independent of `RowAction` (per row) and `BulkAction` (per selection) — for things like "New record", "Import", or "Refresh" that aren't tied to any particular row:

```php
use Salioudiabate\LivewireDatatable\ToolbarAction;
use Salioudiabate\LivewireDatatable\ToolbarActionGroup;

public function toolbarActions(): array
{
    return [
        ToolbarAction::make('New product')
            ->dispatch('openModal', ['component' => 'product.create-modal'])  // fires Livewire's $dispatch()
            ->cssClass('bg-[var(--dt-primary,#4f46e5)] text-white hover:opacity-90')
            ->permission('products.create'),

        ToolbarAction::make('Docs')->url('https://example.com/docs', target: '_blank')->align('left'),

        ToolbarActionGroup::make([
            ToolbarAction::make('Sort by price')->action('sortByPrice'),
            ToolbarAction::make('Sort by stock')->action('sortByStock'),
        ]),
    ];
}

public function sortByPrice(): void
{
    $this->sortBy('price');
}
```

Exactly one trigger is expected per action:

- `->url($href, target: null)` — a plain link.
- `->dispatch($event, $params = [])` — fires Livewire's `$dispatch()`, the same mechanism used to open a modal system, trigger a listener on another component, etc.
- `->action($method)` — a `wire:click` call on this component, dispatched through `runToolbarAction($method)`, which re-checks `permission()` before invoking it (the same defensive pattern as `runBulkAction()` — a button not being rendered isn't the same as the action being protected).
- `->submit($action, $method = 'POST', $data = [], $target = null)` — a real, non-AJAX HTML form post. `action()`/`dispatch()` both stay inside Livewire's AJAX request cycle, which can't hand back a full HTTP response — no streaming a generated file, no opening a new tab on the result. `submit()` renders an actual `<form>` instead, so server work that needs to do that (build a PDF and render it in `target: '_blank'`, trigger a real file download, etc.) has a normal request/response round trip to work with:

    ```php
    ToolbarAction::make('Export as PDF')
        ->submit(
            action: route('products.export-pdf'),
            data: fn () => ['status' => $this->filterValues['status'] ?? null, 'search' => $this->search],
            target: '_blank',
        )
        ->confirm('Generate a PDF of the current results?'),
    ```

    `$data` may be a closure so it's resolved against the component's *current* state (filters, search...) at render time rather than once when the action is declared. CSRF is added automatically for anything but `GET`; `PUT`/`PATCH`/`DELETE` are spoofed via a `_method` field the same way Blade's `@method()` does, since HTML forms only support `GET`/`POST` natively. The button shows a small spinner and disables itself while the browser is submitting (there's no `wire:loading` for a real form post, so this is the package's own stand-in — it auto-resets after 8s since there's no way to know when a new tab has finished loading).

    **`submit()` bypasses `runToolbarAction()`/`runBulkAction()` entirely** — the form posts straight to `$action`, never touching this component. `->permission()` still hides the button from unauthorized users, but the *server-side* re-check has to live on the target route itself (middleware, a policy, whatever you'd normally use), the same as it would for any other route in your app. This is a deliberate trade-off, not an oversight: submitting outside Livewire's request cycle is exactly what makes a full response possible.

    `RowAction::submit(fn ($row) => $url, $method = 'POST', ?fn ($row) => $data = null, $target = null)` and `BulkAction::submit($action, $method = 'POST', $data = [], $target = null)` work the same way — a per-row PDF, or exporting the current selection, follow the identical pattern. `BulkAction::submit()` always sends the selected keys as `selected[]` alongside `$data`, so you don't need to include them yourself.

`->align('left' | 'right')` (default `right`) places the action alongside search/filters or alongside columns/export/density — group actions with `ToolbarActionGroup::make([...])` to render them as one segmented control (the same visual language as the built-in density toggle) instead of separate buttons; a group with no authorized actions left in it renders nothing at all.

Call `->dropdown('Label')` on a group to render it as a single trigger button opening a menu instead (the same pattern as the built-in Filters/Columns buttons) — useful once a group has more than two or three items, where a segmented control would get too wide:

```php
ToolbarActionGroup::make([
    ToolbarAction::make('Sort by price')->action('sortByPrice'),
    ToolbarAction::make('Sort by stock')->action('sortByStock'),
    ToolbarAction::make('Sort by name')->action('sortByName'),
])->dropdown('Sort by...'),
```

`->icon($svg)` on the group sets the icon shown on the dropdown *trigger* button itself (`dropdown($label, $icon)` is shorthand for calling both in one line — either order works, and calling `dropdown()` again without an icon argument doesn't clear one already set). `->icon()`/`->cssClass()` on each individual `ToolbarAction` inside the group style that one item, the same as a standalone action — this applies whether the group renders as a segmented control or a dropdown menu.

Styling follows the same hooks as everything else: `->cssClass()` on the action (or group) overrides the default, which otherwise comes from `toolbarActionClasses()` / `toolbarActionGroupClasses()` / `toolbarActionDropdownClasses()` (see [Styling hooks](#styling-hooks)) — the dropdown's trigger button uses `toolbarActionClasses()` like any standalone action, only the open menu panel has its own hook.

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

Every structural compartment of the table — toolbar, filters panel, header/body cells, bulk actions bar, selection banner, empty state, error state, pagination — is a full-replace class hook: configurable globally (`config('livewire-datatable.classes')`, published to your app) or per-table by overriding the matching method on your `DataTableComponent` subclass:

```php
protected function tableWrapperClasses(): string
{
    return 'overflow-x-auto rounded-2xl border border-slate-300';
}

protected function toolbarClasses(): string
{
    return 'mb-6 flex flex-wrap items-center justify-between gap-4';
}
```

Whatever a hook returns becomes the *entire* class list for that element — there's no merging with a package default. That's deliberate: it's what makes every compartment genuinely restylable rather than only extendable, but it also means structural utilities the compartment needs to work (`overflow-x-auto`, `flex`, `overflow-hidden`, etc.) are your responsibility to keep if you touch one of these.

Available hooks: `rootClasses()`, `tableWrapperClasses()`, `tableClasses()`, `theadTrClasses()`, `thClasses()`, `tbodyTrClasses()`, `tdClasses()`, `paginationWrapperClasses()`, `toolbarClasses()`, `filtersPanelClasses()`, `bulkActionsBarClasses()`, `selectionBannerClasses()`, `emptyStateClasses()`, `columnsDropdownClasses()`, `errorStateClasses()`, `toolbarActionClasses()`, `toolbarActionGroupClasses()`, `toolbarActionDropdownClasses()`, `footerWrapperClasses()`, `footerItemClasses()`, `filterLabelClasses()`, `filterInputClasses()`, `filterSelectClasses()`, `filterMultiSelectClasses()` (see [Filters](#filters) for the per-filter `cssClass()` override), `offlineBannerClasses()`.

For a single column rather than the whole table, use `Column::thClass()` / `Column::tdClass()` instead (see [Columns](#columns)).

Need more than a class change on the empty state — a call-to-action instead of a flat message ("No products yet — create one")? `emptyStateView(): ?string` replaces the whole thing with a view of your own, receiving `$columns` and `$colspan` so it can still span the row correctly:

```php
public function emptyStateView(): ?string
{
    return 'partials.no-products-yet';
}
```

```blade
{{-- resources/views/partials/no-products-yet.blade.php --}}
<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-10 text-center">
        <p class="text-sm text-slate-500">No products yet.</p>
        <a href="{{ route('products.create') }}" class="mt-2 inline-block text-sm font-medium text-[var(--dt-primary,#4f46e5)]">Create your first one →</a>
    </td>
</tr>
```

One hook is global-only, with no per-table method: `config('livewire-datatable.classes.pagination_bar')`. Laravel renders the paginator's view (`tailwind.blade.php` / `simple-tailwind.blade.php`) in its own context outside the component's Blade scope, so it can't reach `$this` on your component — it applies to every table in the app.

**If pagination renders unstyled (plain gray Laravel markup instead of the package's theme).** The package registers its own themed view as the app's default Tailwind pagination view via `Paginator::useTailwind()`, controlled by `config('livewire-datatable.register_pagination_view')` (default `true`). If your own `AppServiceProvider` (or anything else booted after the package) also calls `Paginator::useTailwind()` or `Paginator::defaultView(...)`, it silently wins — provider boot order means the *last* registration applies, not the package's. Remove the conflicting call, or set `register_pagination_view` to `false` and register the package's `tailwind.blade.php` yourself at the point in boot order you need.

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
