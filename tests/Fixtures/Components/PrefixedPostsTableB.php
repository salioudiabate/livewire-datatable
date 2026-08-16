<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

class PrefixedPostsTableB extends PostsTable
{
    protected function urlKey(): string
    {
        return 'table-b';
    }
}
