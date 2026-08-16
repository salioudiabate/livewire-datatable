<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\BrokenTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Bravo', 'status' => 'draft', 'views' => 40, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Charlie', 'status' => 'published', 'views' => 20, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('renders the table with all rows', function () {
    Livewire::test(PostsTable::class)
        ->assertSee('Alpha')
        ->assertSee('Bravo')
        ->assertSee('Charlie');
});

it('filters rows via the search box', function () {
    Livewire::test(PostsTable::class)
        ->set('search', 'Alpha')
        ->assertSee('Alpha')
        ->assertDontSee('Bravo')
        ->assertDontSee('Charlie');
});

it('applies a select filter', function () {
    Livewire::test(PostsTable::class)
        ->set('filterValues.status', 'draft')
        ->assertSee('Bravo')
        ->assertDontSee('Alpha')
        ->assertDontSee('Charlie');
});

it('resets filters back to showing everything', function () {
    Livewire::test(PostsTable::class)
        ->set('filterValues.status', 'draft')
        ->call('resetFilters')
        ->assertSee('Alpha')
        ->assertSee('Bravo')
        ->assertSee('Charlie');
});

it('reports the active filter count', function () {
    Livewire::test(PostsTable::class)
        ->assertSet('filterValues', [])
        ->set('filterValues.status', 'draft')
        ->assertSeeInOrder(['Filters', '1']);
});

it('shows the empty state when nothing matches', function () {
    Livewire::test(PostsTable::class)
        ->set('search', 'nonexistent-term')
        ->assertSee('No results found');
});

it('falls back to the error view instead of a hard crash when builder() throws', function () {
    Livewire::test(BrokenTable::class)
        ->assertSee('This table could not be loaded.');
});
