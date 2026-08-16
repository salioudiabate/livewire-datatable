<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Exceptions;

use RuntimeException;

final class NonDeletableDataSourceException extends RuntimeException
{
    public static function forSource(object $dataSource): self
    {
        return new self(sprintf(
            'deletePermission() is set but [%s] does not support deletion. '.
            'Bulk delete is only available for Eloquent and Query Builder data sources.',
            $dataSource::class
        ));
    }
}
