<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Gate;
use Pest\Browser\Api\Livewire as BrowserLivewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\DeletablePostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

function seedBrowserPosts(int $count = 15): void
{
    for ($i = 1; $i <= $count; $i++) {
        Post::create([
            'title' => sprintf('Post %02d', $i),
            'status' => $i % 2 === 0 ? 'published' : 'draft',
            'views' => $i * 10,
        ]);
    }
}

it('searches the table live as the user types', function () {
    seedBrowserPosts();
    Post::create(['title' => 'Zephyr Exclusive', 'status' => 'draft', 'views' => 0]);

    BrowserLivewire::test(PostsTable::class)
        ->assertSee('Post 01')
        ->type('[wire\:model\.live\.debounce\.300ms="search"]', 'Zephyr')
        ->waitForText('Zephyr Exclusive')
        ->assertSee('Zephyr Exclusive')
        ->assertDontSee('Post 01');
})->group('browser');

it('sorts by clicking a sortable column header', function () {
    // Views are the reverse of id order, so sorting actually changes the
    // first visible row instead of coincidentally matching the default
    // (unsorted, id-ascending) order. Toggling direction on a second click
    // is already covered by the Sorting feature test suite.
    foreach (range(1, 15) as $i) {
        Post::create([
            'title' => sprintf('Post %02d', $i),
            'status' => 'draft',
            'views' => (16 - $i) * 10,
        ]);
    }

    $page = BrowserLivewire::test(PostsTable::class);

    expect(trim((string) $page->text('tbody tr:first-child td:first-child')))->toBe('Post 01');

    $page->click('Views')->waitForText('Post 15');
    expect(trim((string) $page->text('tbody tr:first-child td:first-child')))->toBe('Post 15');
})->group('browser');

it('filters rows by the select filter', function () {
    seedBrowserPosts();

    BrowserLivewire::test(PostsTable::class)
        ->assertSee('Post 01')
        ->assertSee('Post 02')
        ->click('Filters')
        ->select('[wire\:model\.live="filterValues.status"]', 'published')
        ->waitForText('Post 02')
        ->assertSee('Post 02')
        ->assertDontSee('Post 01');
})->group('browser');

it('paginates to the second page', function () {
    seedBrowserPosts();

    BrowserLivewire::test(PostsTable::class)
        ->assertSee('Post 01')
        ->assertDontSee('Post 11')
        ->click('[aria-label="Go to page 2"]')
        ->waitForText('Post 11')
        ->assertSee('Post 11')
        ->assertDontSee('Post 01');
})->group('browser');

it('reveals the bulk actions bar with the selected count once a row is checked', function () {
    Gate::define('delete-posts', fn ($user = null) => true);
    seedBrowserPosts();
    $firstPostId = Post::query()->orderBy('id')->value('id');

    BrowserLivewire::test(DeletablePostsTable::class)
        ->assertDontSee('1 selected')
        ->click("[type=\"checkbox\"][value=\"{$firstPostId}\"]")
        ->waitForText('1 selected')
        ->assertSee('1 selected')
        ->assertSee('Delete');
})->group('browser');
