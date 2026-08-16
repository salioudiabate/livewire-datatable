<?php

declare(strict_types=1);

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\DataSources\QueryBuilderDataSource;
use Salioudiabate\LivewireDatatable\Filters\BooleanFilter;
use Salioudiabate\LivewireDatatable\Filters\DateFilter;
use Salioudiabate\LivewireDatatable\Filters\DateRangeFilter;
use Salioudiabate\LivewireDatatable\Filters\MultiSelectFilter;
use Salioudiabate\LivewireDatatable\Filters\NumberFilter;
use Salioudiabate\LivewireDatatable\Filters\NumberRangeFilter;
use Salioudiabate\LivewireDatatable\Filters\SelectFilter;
use Salioudiabate\LivewireDatatable\Filters\TextFilter;

/**
 * @return array<int, array{id: int, status: string, price: int, active: bool, category: string, published_at: string}>
 */
function filterTestRows(): array
{
    return [
        ['id' => 1, 'status' => 'draft', 'price' => 10, 'active' => true, 'category' => 'a', 'published_at' => '2024-01-01'],
        ['id' => 2, 'status' => 'published', 'price' => 50, 'active' => false, 'category' => 'b', 'published_at' => '2024-02-01'],
        ['id' => 3, 'status' => 'published', 'price' => 30, 'active' => true, 'category' => 'a', 'published_at' => '2024-03-01'],
    ];
}

function filterTestSource(): CollectionDataSource
{
    return new CollectionDataSource(collect(filterTestRows()));
}

// --- Base Filter mechanics -------------------------------------------------

it('exposes key, label and a stable view name', function () {
    $filter = TextFilter::make('Status', 'status');

    expect($filter->key())->toBe('status')
        ->and($filter->label())->toBe('Status')
        ->and($filter->view())->toBe('livewire-datatable::filters.text');
});

it('is inactive for a missing or empty value by default', function () {
    $filter = TextFilter::make('Status', 'status');

    expect($filter->isActive([]))->toBeFalse()
        ->and($filter->isActive(['status' => '']))->toBeFalse()
        ->and($filter->isActive(['status' => 'draft']))->toBeTrue();
});

it('using() lets a closure fully control an immutable Collection source by returning the new state', function () {
    $filter = TextFilter::make('Status', 'status')->using(
        fn (Collection $items, mixed $value) => $items->filter(fn (array $row) => $row['status'] === $value)->values()
    );

    $result = $filter->apply(filterTestSource(), ['status' => 'draft'])->paginate(10, 1);

    expect($result->total)->toBe(1);
});

it('using() mutates a live Query Builder in place, matching the common closure idiom of returning $query', function () {
    DB::table('dt_test_posts')->insert(['title' => 'a', 'status' => 'draft', 'views' => 0, 'created_at' => now(), 'updated_at' => now()]);
    DB::table('dt_test_posts')->insert(['title' => 'b', 'status' => 'published', 'views' => 0, 'created_at' => now(), 'updated_at' => now()]);

    $filter = TextFilter::make('Status', 'status')->using(
        fn (QueryBuilder $query, mixed $value) => $query->where('status', $value)
    );

    $source = new QueryBuilderDataSource(DB::table('dt_test_posts'));
    $result = $filter->apply($source, ['status' => 'draft'])->paginate(10, 1);

    expect($result->total)->toBe(1);
});

// --- TextFilter --------------------------------------------------------

it('TextFilter default behavior searches the given field', function () {
    $result = TextFilter::make('Status', 'status')
        ->apply(filterTestSource(), ['status' => 'draft'])
        ->paginate(10, 1);

    expect($result->total)->toBe(1);
});

// --- SelectFilter --------------------------------------------------------

it('SelectFilter default behavior applies an exact match', function () {
    $filter = SelectFilter::make('Status', 'status')->options(['draft' => 'Draft', 'published' => 'Published']);

    $result = $filter->apply(filterTestSource(), ['status' => 'published'])->paginate(10, 1);

    expect($result->total)->toBe(2)
        ->and($filter->getOptions())->toBe(['draft' => 'Draft', 'published' => 'Published']);
});

// --- MultiSelectFilter -----------------------------------------------------

it('MultiSelectFilter requires a non-empty array to be active', function () {
    $filter = MultiSelectFilter::make('Category', 'category');

    expect($filter->isActive([]))->toBeFalse()
        ->and($filter->isActive(['category' => []]))->toBeFalse()
        ->and($filter->isActive(['category' => 'a']))->toBeFalse()
        ->and($filter->isActive(['category' => ['a']]))->toBeTrue();
});

it('MultiSelectFilter default behavior applies a whereIn', function () {
    $filter = MultiSelectFilter::make('Category', 'category')->options(['a' => 'A', 'b' => 'B']);

    $result = $filter->apply(filterTestSource(), ['category' => ['a']])->paginate(10, 1);

    expect($result->total)->toBe(2);
});

// --- BooleanFilter (tri-state) ----------------------------------------------

it('BooleanFilter treats false as an active, meaningful value, unlike a generic emptiness check', function () {
    $filter = BooleanFilter::make('Active', 'active');

    expect($filter->isActive([]))->toBeFalse()
        ->and($filter->isActive(['active' => '']))->toBeFalse()
        ->and($filter->isActive(['active' => false]))->toBeTrue()
        ->and($filter->isActive(['active' => '0']))->toBeTrue();
});

it('BooleanFilter default behavior applies the correct boolean comparison', function () {
    $result = BooleanFilter::make('Active', 'active')
        ->apply(filterTestSource(), ['active' => '0'])
        ->paginate(10, 1);

    expect($result->total)->toBe(1);
});

// --- DateFilter --------------------------------------------------------

it('DateFilter default behavior applies an exact match', function () {
    $result = DateFilter::make('Published', 'published_at')
        ->apply(filterTestSource(), ['published_at' => '2024-02-01'])
        ->paginate(10, 1);

    expect($result->total)->toBe(1);
});

// --- DateRangeFilter / RangeFilter -------------------------------------

it('RangeFilter derives two state keys with the _from/_to convention', function () {
    $filter = DateRangeFilter::make('Published', 'published_at');

    expect($filter->stateKeys())->toBe(['published_at_from', 'published_at_to']);
});

it('RangeFilter is active when only one bound is set', function () {
    $filter = DateRangeFilter::make('Published', 'published_at');

    expect($filter->isActive([]))->toBeFalse()
        ->and($filter->isActive(['published_at_from' => '2024-01-15']))->toBeTrue()
        ->and($filter->isActive(['published_at_to' => '2024-01-15']))->toBeTrue();
});

it('DateRangeFilter applies both bounds when both are present', function () {
    $result = DateRangeFilter::make('Published', 'published_at')
        ->apply(filterTestSource(), ['published_at_from' => '2024-01-15', 'published_at_to' => '2024-02-15'])
        ->paginate(10, 1);

    expect($result->total)->toBe(1);
});

it('DateRangeFilter applies a single bound when only one side is given', function () {
    $result = DateRangeFilter::make('Published', 'published_at')
        ->apply(filterTestSource(), ['published_at_from' => '2024-02-01'])
        ->paginate(10, 1);

    expect($result->total)->toBe(2);
});

// --- NumberFilter / NumberRangeFilter ---------------------------------------

it('NumberFilter casts a numeric string before comparing', function () {
    $result = NumberFilter::make('Price', 'price')
        ->apply(filterTestSource(), ['price' => '30'])
        ->paginate(10, 1);

    expect($result->total)->toBe(1);
});

it('NumberRangeFilter applies >= and <= across both bounds', function () {
    $result = NumberRangeFilter::make('Price', 'price')
        ->apply(filterTestSource(), ['price_from' => '20', 'price_to' => '40'])
        ->paginate(10, 1);

    expect($result->total)->toBe(1);
});

// --- Every filter type exposes a distinct, stable view name ----------------

it('every built-in filter type resolves to its own dedicated Blade partial', function () {
    expect(TextFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.text')
        ->and(DateFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.date')
        ->and(DateRangeFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.date-range')
        ->and(SelectFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.select')
        ->and(MultiSelectFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.multi-select')
        ->and(NumberFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.number')
        ->and(NumberRangeFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.number-range')
        ->and(BooleanFilter::make('l', 'k')->view())->toBe('livewire-datatable::filters.boolean');
});
