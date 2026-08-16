<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

use Salioudiabate\LivewireDatatable\BulkAction;

class DeletablePostsTable extends PostsTable
{
    public function bulkActions(): array
    {
        return [
            BulkAction::make('destroySelected', 'Delete'),
        ];
    }

    protected function deletePermission(): ?string
    {
        return 'delete-posts';
    }
}
