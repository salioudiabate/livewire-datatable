<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Exceptions;

use InvalidArgumentException;

final class InvalidSortColumnException extends InvalidArgumentException
{
    public static function forField(string $field): self
    {
        return new self(
            "Cannot sort by [{$field}]: it is not declared as sortable() on any column returned by columns()."
        );
    }
}
