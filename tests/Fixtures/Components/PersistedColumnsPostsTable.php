<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

class PersistedColumnsPostsTable extends FullFeaturedPostsTable
{
    protected function persistColumnVisibility(): ?string
    {
        return 'test-table';
    }
}
