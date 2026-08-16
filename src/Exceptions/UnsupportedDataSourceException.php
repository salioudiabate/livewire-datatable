<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Exceptions;

use InvalidArgumentException;

final class UnsupportedDataSourceException extends InvalidArgumentException
{
    public static function forValue(mixed $source): self
    {
        $type = is_object($source) ? $source::class : gettype($source);

        return new self(
            "The DataTable's builder() method returned a value of type [{$type}] that isn't a supported data source. ".
            'Return an Eloquent\Builder, a Query\Builder, RawSql::query(...), an array, a Collection, or a class '.
            'implementing DataSource. For other query types, register an adapter with DataSourceFactory::extend().'
        );
    }
}
