<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

use Illuminate\Database\Eloquent\Builder;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataTableComponent;
use Salioudiabate\LivewireDatatable\RowAction;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

class FullFeaturedPostsTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Post::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Title', 'title')->searchable()->sortable(),
            Column::make('Status', 'status')->sortable()->toggleable(),
            Column::make('Views', 'views')->sortable()->toggleable(visibleByDefault: false),
        ];
    }

    public function rowActions(): array
    {
        return [
            RowAction::make('View')->url(fn (Post $post) => "/posts/{$post->id}"),
            RowAction::make('Archive')
                ->action('archivePost')
                ->visible(fn (Post $post) => $post->status !== 'archived'),
        ];
    }

    public function archivePost(string $id): void
    {
        Post::query()->whereKey($id)->update(['status' => 'archived']);
    }
}
