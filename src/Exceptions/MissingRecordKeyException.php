<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Exceptions;

use RuntimeException;

final class MissingRecordKeyException extends RuntimeException
{
    public static function forRow(mixed $row, string $context = 'row selection'): self
    {
        $type = is_object($row) ? $row::class : gettype($row);

        return new self(
            "Could not resolve a unique key for a row of type [{$type}] while performing {$context}. ".
            'Override recordKey() on your DataTableComponent to return the field name (or a Closure) that '.
            'uniquely identifies each row.'
        );
    }
}
