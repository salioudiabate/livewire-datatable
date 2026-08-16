<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests\Fixtures\Components;

use RuntimeException;
use Salioudiabate\LivewireDatatable\DataTableComponent;

class BrokenTable extends DataTableComponent
{
    public function builder(): mixed
    {
        throw new RuntimeException('boom');
    }

    public function columns(): array
    {
        return [];
    }
}
