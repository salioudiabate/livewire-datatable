<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\DeletablePostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\VetoingDeletablePostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

beforeEach(function () {
    Gate::define('delete-posts', fn ($user = null) => true);

    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Bravo', 'status' => 'published', 'views' => 20, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('deletes the selected rows when authorized', function () {
    Livewire::test(DeletablePostsTable::class)
        ->set('selected', ['1', '2'])
        ->call('runBulkAction', 'destroySelected');

    expect(Post::query()->count())->toBe(0);
});

it('clears the selection after a successful bulk delete', function () {
    Livewire::test(DeletablePostsTable::class)
        ->set('selected', ['1', '2'])
        ->call('runBulkAction', 'destroySelected')
        ->assertSet('selected', []);
});

it('skips rows that beforeDelete vetoes, leaving them in place', function () {
    Livewire::test(VetoingDeletablePostsTable::class)
        ->set('selected', ['1', '2'])
        ->call('destroySelected');

    expect(Post::query()->count())->toBe(1)
        ->and(Post::query()->whereKey(1)->exists())->toBeTrue();
});

it('aborts when deletePermission is not granted', function () {
    Gate::define('delete-posts', fn ($user = null) => false);

    $response = Livewire::test(DeletablePostsTable::class)
        ->set('selected', ['1'])
        ->call('destroySelected');

    $response->assertStatus(403);
});

it('aborts when running a bulk action that was not declared in bulkActions()', function () {
    $response = Livewire::test(DeletablePostsTable::class)
        ->set('selected', ['1'])
        ->call('runBulkAction', 'someUndeclaredMethod');

    $response->assertStatus(403);
});
