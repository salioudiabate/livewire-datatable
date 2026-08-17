<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\Exceptions\InvalidSortColumnException;
use Salioudiabate\LivewireDatatable\Exceptions\MissingRecordKeyException;
use Salioudiabate\LivewireDatatable\Exceptions\NonDeletableDataSourceException;
use Salioudiabate\LivewireDatatable\Exceptions\UnsupportedDataSourceException;
use Salioudiabate\LivewireDatatable\Filters\Filter;
use Salioudiabate\LivewireDatatable\Filters\RangeFilter;

arch('the whole package uses strict types')
    ->expect('Salioudiabate\LivewireDatatable')
    ->toUseStrictTypes();

arch('no debug statements are left behind')
    ->expect('Salioudiabate\LivewireDatatable')
    ->not->toUse(['dd', 'dump', 'var_dump', 'ray', 'die']);

arch('Concerns are traits, not classes')
    ->expect('Salioudiabate\LivewireDatatable\Concerns')
    ->toBeTraits();

arch('every DataSource adapter is final')
    ->expect('Salioudiabate\LivewireDatatable\DataSources')
    ->classes()
    ->toBeFinal();

arch('every concrete DataSource adapter implements the DataSource contract')
    ->expect([
        'Salioudiabate\LivewireDatatable\DataSources\EloquentDataSource',
        'Salioudiabate\LivewireDatatable\DataSources\QueryBuilderDataSource',
        'Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource',
        'Salioudiabate\LivewireDatatable\DataSources\RawSqlDataSource',
    ])
    ->toImplement(DataSource::class);

arch('every exception is final and extends a native SPL exception')
    ->expect([
        InvalidSortColumnException::class,
        MissingRecordKeyException::class,
        NonDeletableDataSourceException::class,
        UnsupportedDataSourceException::class,
    ])
    ->toBeFinal()
    ->toImplement(Throwable::class);

arch('Filter and RangeFilter stay abstract base classes')
    ->expect([Filter::class, RangeFilter::class])
    ->toBeAbstract();

arch('every concrete filter is final')
    ->expect('Salioudiabate\LivewireDatatable\Filters')
    ->classes()
    ->toBeFinal()
    ->ignoring([Filter::class, RangeFilter::class]);

arch('every concrete filter extends Filter')
    ->expect('Salioudiabate\LivewireDatatable\Filters')
    ->classes()
    ->toExtend(Filter::class)
    ->ignoring([Filter::class, RangeFilter::class]);

arch('src never depends on the tests it is tested by')
    ->expect('Salioudiabate\LivewireDatatable')
    ->not->toUse('Salioudiabate\LivewireDatatable\Tests');
