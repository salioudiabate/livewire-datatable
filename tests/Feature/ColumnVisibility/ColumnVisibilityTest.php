<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FullFeaturedPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PersistedColumnsPostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha',
        'status' => 'published',
        'views' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('hides a column whose toggleable default is false', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class);

    expect($test->get('hiddenColumns'))->toBe(['views']);
});

it('shows a toggleable(true) column by default', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class);

    expect($test->instance()->isColumnHidden('status'))->toBeFalse();
});

it('toggles a column between visible and hidden', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class)->call('toggleColumnVisibility', 'views');

    expect($test->instance()->isColumnHidden('views'))->toBeFalse();

    $test->call('toggleColumnVisibility', 'views');

    expect($test->instance()->isColumnHidden('views'))->toBeTrue();
});

it('excludes hidden columns from visibleColumns() while columns() still lists all of them', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class);

    expect($test->instance()->columns())->toHaveCount(3);

    $visibleFields = collect($test->instance()->visibleColumns())->map(fn ($c) => $c->getField())->all();

    expect($visibleFields)->toBe(['title', 'status']);
});

it('never hides a non-toggleable column even if its field ends up in hiddenColumns', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class)->set('hiddenColumns', ['title']);

    $visibleFields = collect($test->instance()->visibleColumns())->map(fn ($c) => $c->getField())->all();

    expect($visibleFields)->toContain('title');
});

it('persists column visibility across a fresh mount when persistColumnVisibility() is set', function () {
    Livewire::test(PersistedColumnsPostsTable::class)->call('toggleColumnVisibility', 'status');

    $fresh = Livewire::test(PersistedColumnsPostsTable::class);

    expect($fresh->instance()->isColumnHidden('status'))->toBeTrue();
});

it('does not leak visibility state between components without persistColumnVisibility()', function () {
    Livewire::test(FullFeaturedPostsTable::class)->call('toggleColumnVisibility', 'status');

    $fresh = Livewire::test(FullFeaturedPostsTable::class);

    expect($fresh->instance()->isColumnHidden('status'))->toBeFalse();
});
