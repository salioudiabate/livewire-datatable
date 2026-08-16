<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

class UnboundPostsTable extends PostsTable
{
    protected function withoutUrlBinding(): bool
    {
        return true;
    }
}
