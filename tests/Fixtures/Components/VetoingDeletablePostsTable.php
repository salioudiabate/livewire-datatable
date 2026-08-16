<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

class VetoingDeletablePostsTable extends DeletablePostsTable
{
    protected function beforeDelete(mixed $row): bool
    {
        return (int) $row->id !== 1;
    }
}
