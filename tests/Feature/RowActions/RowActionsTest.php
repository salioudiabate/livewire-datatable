<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\RowAction;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FullFeaturedPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Bravo', 'status' => 'archived', 'views' => 5, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('renders a url-based row action as a plain link', function () {
    Livewire::test(FullFeaturedPostsTable::class)->assertSee('/posts/1', false);
});

it('runs the row action method with the resolved row key', function () {
    Livewire::test(FullFeaturedPostsTable::class)->call('archivePost', '1');

    expect(Post::query()->find(1)->status)->toBe('archived');
});

it('filters row actions per-row via visible()', function () {
    $component = new FullFeaturedPostsTable;

    $archivedRow = Post::query()->find(2);
    $labels = collect($component->visibleRowActions($archivedRow))->map(fn ($a) => $a->getLabel())->all();

    expect($labels)->toBe(['View']);
});

it('shows every action for a row that satisfies all visible() conditions', function () {
    $component = new FullFeaturedPostsTable;

    $publishedRow = Post::query()->find(1);
    $labels = collect($component->visibleRowActions($publishedRow))->map(fn ($a) => $a->getLabel())->all();

    expect($labels)->toBe(['View', 'Archive']);
});

it('safely embeds a row key containing a single quote in wire:click instead of breaking out of it', function () {
    // recordKey() can be a Closure resolving to any free-text column, not
    // just a safe int/UUID id — raw string interpolation into wire:click
    // would let a value like this one break out of the Livewire action-call
    // syntax entirely.
    DB::table('dt_test_posts')->insert([
        'title' => "O'Brien's Post", 'status' => 'draft', 'views' => 1, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $html = Livewire::test(new class extends PostsTable
    {
        public function recordKey(): string
        {
            return 'title';
        }

        public function rowActions(): array
        {
            return [RowAction::make('Archive')->action('archivePost')];
        }

        public function archivePost(string $title): void
        {
            //
        }
    })->html();

    expect($html)->toContain('\u0027')
        ->and($html)->not->toContain("archivePost('o'brien's post')");
});
