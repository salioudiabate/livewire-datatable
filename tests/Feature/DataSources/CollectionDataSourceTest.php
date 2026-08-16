<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;
use Salioudiabate\LivewireDatatable\DataSources\Deletable;

require_once __DIR__.'/Contract.php';

runDataSourceContractTests(fn () => new CollectionDataSource(collect(dataSourceContractFixtureRows())));

it('resolves a plain PHP array to a CollectionDataSource via the factory', function () {
    $source = DataSourceFactory::make(dataSourceContractFixtureRows());

    expect($source)->toBeInstanceOf(CollectionDataSource::class)
        ->and($source->count())->toBe(5);
});

it('does not implement Deletable', function () {
    $source = new CollectionDataSource(collect(dataSourceContractFixtureRows()));

    expect($source)->not->toBeInstanceOf(Deletable::class);
});

it('rejects an unsupported aggregate function', function () {
    $source = new CollectionDataSource(collect(dataSourceContractFixtureRows()));

    expect(fn () => $source->aggregate('median', 'views'))->toThrow(InvalidArgumentException::class);
});
