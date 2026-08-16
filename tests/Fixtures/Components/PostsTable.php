<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

use Illuminate\Database\Eloquent\Builder;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\DataTableComponent;
use Salioudiabate\LivewireDatatable\Filters\SelectFilter;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

class PostsTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Post::query();
    }

    public function columns(): array
    {
        return [
            Column::make('Title', 'title')->searchable()->sortable(),
            Column::make('Status', 'status')->sortable(),
            Column::make('Views', 'views')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status', 'status')->options(['draft' => 'Draft', 'published' => 'Published']),
        ];
    }
}
