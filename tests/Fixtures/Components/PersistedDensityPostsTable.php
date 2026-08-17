<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

class PersistedDensityPostsTable extends PostsTable
{
    protected function persistDensity(): ?string
    {
        return 'test-table';
    }
}
