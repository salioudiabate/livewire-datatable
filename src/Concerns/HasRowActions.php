<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Concerns;

use Salioudiabate\LivewireDatatable\RowAction;

trait HasRowActions
{
    /**
     * @return array<int, RowAction>
     */
    public function rowActions(): array
    {
        return [];
    }

    /**
     * @return array<int, RowAction>
     */
    public function visibleRowActions(mixed $row): array
    {
        return array_values(array_filter(
            $this->rowActions(),
            fn (RowAction $action) => $action->isVisible($row)
        ));
    }
}
