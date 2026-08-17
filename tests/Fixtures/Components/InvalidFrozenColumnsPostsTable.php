<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

use Salioudiabate\LivewireDatatable\Column;

/**
 * Status is frozen but Title, which comes before it, is not — an invalid,
 * non-contiguous-leading-run configuration that assertValidFrozenColumns()
 * must reject.
 */
class InvalidFrozenColumnsPostsTable extends PostsTable
{
    public function columns(): array
    {
        return [
            Column::make('Title', 'title')->searchable()->sortable(),
            Column::make('Status', 'status')->sortable()->frozen(100),
            Column::make('Views', 'views')->sortable(),
        ];
    }
}
