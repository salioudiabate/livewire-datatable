<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FrozenColumnsPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FrozenColumnsWithSelectionPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\InvalidFrozenColumnsPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);
});

it('pins a frozen column at the left edge with its declared width', function () {
    $html = Livewire::test(FrozenColumnsPostsTable::class)->html();

    expect($html)->toContain('position: sticky; left: 0px; width: 150px; min-width: 150px;');
});

it('offsets the frozen column past the selection checkbox column when bulk actions are enabled', function () {
    Gate::define('delete-posts', fn ($user = null) => true);

    $html = Livewire::test(FrozenColumnsWithSelectionPostsTable::class)->html();

    expect($html)->toContain('position: sticky; left: 44px; width: 150px; min-width: 150px;')
        ->and($html)->toContain('position: sticky; left: 0; z-index: 1;');
});

it('falls back to the error view when a frozen column is not a leading, contiguous run', function () {
    Livewire::test(InvalidFrozenColumnsPostsTable::class)
        ->assertSee('This table could not be loaded.');
});

it('leaves non-frozen tables completely unaffected by sticky positioning', function () {
    $html = Livewire::test(PostsTable::class)->html();

    expect($html)->not->toContain('position: sticky');
});
