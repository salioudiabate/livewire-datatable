<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

class PrefixedPostsTableA extends PostsTable
{
    protected function urlKey(): string
    {
        return 'table-a';
    }
}
