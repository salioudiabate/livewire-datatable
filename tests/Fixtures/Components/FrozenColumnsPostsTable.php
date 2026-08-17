<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

use Salioudiabate\LivewireDatatable\Column;

class FrozenColumnsPostsTable extends PostsTable
{
    public function columns(): array
    {
        return [
            Column::make('Title', 'title')->searchable()->sortable()->frozen(150),
            Column::make('Status', 'status')->sortable(),
            Column::make('Views', 'views')->sortable(),
        ];
    }
}
