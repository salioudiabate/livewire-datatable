<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\DeletablePostsTable;
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

it('does not render selection checkboxes when there are no bulk actions', function () {
    Livewire::test(PostsTable::class)->assertDontSee('wire:model.live="selectAll"', false);
});

it('renders selection checkboxes once bulk actions are declared', function () {
    Livewire::test(DeletablePostsTable::class)->assertSee('wire:model.live="selectAll"', false);
});

it('selects and deselects an individual row', function () {
    $test = Livewire::test(DeletablePostsTable::class)->set('selected', ['1']);

    expect($test->get('selected'))->toBe(['1']);

    $test->set('selected', []);

    expect($test->get('selected'))->toBe([]);
});

it('selecting the header checkbox selects only the current page, not every filtered row', function () {
    $test = Livewire::test(DeletablePostsTable::class)->set('selectAll', true);

    expect($test->get('selected'))->toHaveCount(10);
});

it('auto-checks the header checkbox once every row on the page is individually selected', function () {
    $ids = DB::table('dt_test_posts')->orderBy('id')->limit(10)->pluck('id')
        ->map(fn ($id) => (string) $id)->all();

    $test = Livewire::test(DeletablePostsTable::class)->set('selected', $ids);

    expect($test->get('selectAll'))->toBeTrue();
});

it('does not auto-check the header checkbox for a partial page selection', function () {
    $test = Livewire::test(DeletablePostsTable::class)->set('selected', ['1']);

    expect($test->get('selectAll'))->toBeFalse();
});

it('expands selection to every filtered row across all pages via selectAllFiltered', function () {
    $test = Livewire::test(DeletablePostsTable::class)
        ->set('selectAll', true)
        ->call('selectAllFiltered');

    expect($test->get('selected'))->toHaveCount(15)
        ->and($test->get('selectAll'))->toBeTrue();
});

it('clears the selection regardless of the current selectAll state', function () {
    $test = Livewire::test(DeletablePostsTable::class)
        ->set('selected', ['1', '2'])
        ->call('clearSelection');

    expect($test->get('selected'))->toBe([])
        ->and($test->get('selectAll'))->toBeFalse();
});

it('clears the selection when the search term changes', function () {
    Livewire::test(DeletablePostsTable::class)
        ->set('selected', ['1'])
        ->set('search', 'Post 1')
        ->assertSet('selected', []);
});

it('exposes visibleRowKeys() publicly, scoped to the current page only', function () {
    $component = new DeletablePostsTable;

    expect($component->visibleRowKeys())->toHaveCount(10);
});

it('exposes allFilteredKeys() publicly, scoped to every matching row across all pages', function () {
    $component = new DeletablePostsTable;

    expect($component->allFilteredKeys())->toHaveCount(15);
});

it('narrows allFilteredKeys() to whatever search/filters are currently active, same as selectAllFiltered()', function () {
    $component = new DeletablePostsTable;
    $component->search = 'Post 1';

    // "Post 1", "Post 10".."Post 15" all match the substring "Post 1".
    expect($component->allFilteredKeys())->toHaveCount(7);
});
