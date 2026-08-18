<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\BrokenTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\DeletablePostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FullFeaturedPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

it('renders the configured toolbar classes', function () {
    config(['livewire-datatable.classes.toolbar' => 'toolbar-marker-xyz']);

    Livewire::test(PostsTable::class)->assertSee('toolbar-marker-xyz', false);
});

it('lets the toolbar classes be overridden per-table', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarClasses(): string
        {
            return 'per-table-toolbar-marker';
        }
    })->assertSee('per-table-toolbar-marker', false);
});

it('renders the configured filters panel classes', function () {
    config(['livewire-datatable.classes.filters_panel' => 'filters-panel-marker-xyz']);

    Livewire::test(PostsTable::class)->assertSee('filters-panel-marker-xyz', false);
});

it('renders the configured bulk actions bar classes once a row is selected', function () {
    config(['livewire-datatable.classes.bulk_actions_bar' => 'bulk-bar-marker-xyz']);

    $post = Post::create(['title' => 'A post', 'status' => 'draft', 'views' => 0]);

    Livewire::test(DeletablePostsTable::class)
        ->set('selected', [$post->id])
        ->assertSee('bulk-bar-marker-xyz', false);
});

it('renders the configured selection banner classes once a page is fully selected', function () {
    config(['livewire-datatable.classes.selection_banner' => 'selection-banner-marker-xyz']);

    foreach (range(1, 15) as $i) {
        Post::create(['title' => "Post {$i}", 'status' => 'draft', 'views' => $i]);
    }

    Livewire::test(DeletablePostsTable::class)
        ->set('selectAll', true)
        ->assertSee('selection-banner-marker-xyz', false);
});

it('renders the configured empty state classes when there are no rows', function () {
    config(['livewire-datatable.classes.empty_state' => 'empty-state-marker-xyz']);

    Livewire::test(PostsTable::class)->assertSee('empty-state-marker-xyz', false);
});

it('renders the configured columns dropdown classes when a column is toggleable', function () {
    config(['livewire-datatable.classes.columns_dropdown' => 'columns-dropdown-marker-xyz']);

    Livewire::test(FullFeaturedPostsTable::class)->assertSee('columns-dropdown-marker-xyz', false);
});

it('renders the configured error state classes when rendering fails', function () {
    config(['livewire-datatable.classes.error_state' => 'error-state-marker-xyz']);

    Livewire::test(BrokenTable::class)->assertSee('error-state-marker-xyz', false);
});

it('renders the configured pagination bar classes (global only)', function () {
    config(['livewire-datatable.classes.pagination_bar' => 'pagination-bar-marker-xyz']);

    foreach (range(1, 15) as $i) {
        Post::create(['title' => "Post {$i}", 'status' => 'draft', 'views' => $i]);
    }

    Livewire::test(PostsTable::class)->assertSee('pagination-bar-marker-xyz', false);
});

it('applies a per-column tdClass to every body cell in that column', function () {
    Post::create(['title' => 'A post', 'status' => 'draft', 'views' => 0]);

    Livewire::test(new class extends PostsTable
    {
        public function columns(): array
        {
            return [
                Column::make('Title', 'title')->tdClass('td-class-marker-xyz'),
            ];
        }
    })->assertSee('td-class-marker-xyz', false);
});
