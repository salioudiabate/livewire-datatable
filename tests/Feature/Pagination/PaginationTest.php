<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    foreach (range(1, 15) as $i) {
        DB::table('dt_test_posts')->insert([
            'title' => "Post {$i}",
            'status' => 'published',
            'views' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
});

it('paginates using the default per-page size', function () {
    $test = Livewire::test(PostsTable::class);

    expect($test->instance()->rows->total())->toBe(15)
        ->and($test->instance()->rows->perPage())->toBe(10)
        ->and($test->instance()->rows->count())->toBe(10);
});

it('navigates to the next and previous page', function () {
    $test = Livewire::test(PostsTable::class)->call('nextPage');

    expect($test->get('page'))->toBe(2);

    $test->call('previousPage');

    expect($test->get('page'))->toBe(1);
});

it('never goes below page 1', function () {
    $test = Livewire::test(PostsTable::class)->call('previousPage');

    expect($test->get('page'))->toBe(1);
});

it('jumps directly to a given page', function () {
    $test = Livewire::test(PostsTable::class)->call('gotoPage', 2);

    expect($test->get('page'))->toBe(2)
        ->and($test->instance()->rows->currentPage())->toBe(2);
});

it('resets to page 1 when perPage changes', function () {
    Livewire::test(PostsTable::class)
        ->call('nextPage')
        ->set('perPage', 25)
        ->assertSet('page', 1);
});

it('resets to page 1 when the search term changes', function () {
    Livewire::test(PostsTable::class)
        ->call('nextPage')
        ->set('search', 'Post 1')
        ->assertSet('page', 1);
});

it('resets to page 1 when a filter changes', function () {
    Livewire::test(PostsTable::class)
        ->call('nextPage')
        ->set('filterValues.status', 'published')
        ->assertSet('page', 1);
});
