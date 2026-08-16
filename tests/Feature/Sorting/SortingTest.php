<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Exceptions\InvalidSortColumnException;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Bravo', 'status' => 'draft', 'views' => 40, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('sorts ascending on the first click and descending on the second click of the same column', function () {
    $test = Livewire::test(PostsTable::class)->call('sortBy', 'title');

    expect($test->get('sortField'))->toBe('title')
        ->and($test->get('sortDirection'))->toBe('asc');

    $test->call('sortBy', 'title');

    expect($test->get('sortDirection'))->toBe('desc');
});

it('resets to ascending when switching to a different column', function () {
    $test = Livewire::test(PostsTable::class)
        ->call('sortBy', 'title')
        ->call('sortBy', 'title');

    expect($test->get('sortDirection'))->toBe('desc');

    $test->call('sortBy', 'views');

    expect($test->get('sortField'))->toBe('views')
        ->and($test->get('sortDirection'))->toBe('asc');
});

it('actually reorders the rendered rows', function () {
    $test = Livewire::test(PostsTable::class)->call('sortBy', 'views');

    expect($test->instance()->rows->pluck('title')->all())->toBe(['Alpha', 'Bravo']);

    $test->call('sortBy', 'views');

    expect($test->instance()->rows->pluck('title')->all())->toBe(['Bravo', 'Alpha']);
});

it('rejects sorting by a field that is not declared sortable in columns()', function () {
    Livewire::test(PostsTable::class)->call('sortBy', 'not_a_declared_column');
})->throws(InvalidSortColumnException::class);
