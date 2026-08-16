<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\DataSources;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Salioudiabate\LivewireDatatable\Exceptions\UnsupportedDataSourceException;

final class DataSourceFactory
{
    /**
     * @var array<class-string, Closure(object): DataSource>
     */
    private static array $extensions = [];

    /**
     * Resolves whatever builder() returned into a DataSource. A value that
     * already implements DataSource passes straight through — this is how
     * third-party adapters (e.g. for an API-backed source) plug in without
     * needing a registry entry.
     */
    public static function make(mixed $source): DataSource
    {
        return match (true) {
            $source instanceof DataSource => $source,
            $source instanceof EloquentBuilder => new EloquentDataSource($source),
            $source instanceof QueryBuilder => new QueryBuilderDataSource($source),
            $source instanceof RawSql => new RawSqlDataSource($source),
            $source instanceof Collection => new CollectionDataSource($source),
            is_array($source) => new CollectionDataSource(collect($source)),
            default => self::resolveExtension($source),
        };
    }

    /**
     * Registers an adapter for a pre-existing query type the factory
     * doesn't know about out of the box (e.g. Laravel Scout's Builder),
     * so builder() can keep returning that type directly instead of
     * requiring consumers to hand-wrap it in a DataSource themselves.
     *
     * @param  class-string  $class
     * @param  Closure(object): DataSource  $resolver
     */
    public static function extend(string $class, Closure $resolver): void
    {
        self::$extensions[$class] = $resolver;
    }

    private static function resolveExtension(mixed $source): DataSource
    {
        if (is_object($source)) {
            foreach (self::$extensions as $class => $resolver) {
                if ($source instanceof $class) {
                    return $resolver($source);
                }
            }
        }

        throw UnsupportedDataSourceException::forValue($source);
    }
}
