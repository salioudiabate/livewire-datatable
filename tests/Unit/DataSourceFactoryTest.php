<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Salioudiabate\LivewireDatatable\DataSources\CollectionDataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;
use Salioudiabate\LivewireDatatable\DataSources\DataSourceFactory;
use Salioudiabate\LivewireDatatable\DataSources\EloquentDataSource;
use Salioudiabate\LivewireDatatable\DataSources\PaginationResult;
use Salioudiabate\LivewireDatatable\DataSources\QueryBuilderDataSource;
use Salioudiabate\LivewireDatatable\DataSources\RawSql;
use Salioudiabate\LivewireDatatable\DataSources\RawSqlDataSource;
use Salioudiabate\LivewireDatatable\Exceptions\UnsupportedDataSourceException;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

it('resolves an Eloquent builder', function () {
    expect(DataSourceFactory::make(Post::query()))->toBeInstanceOf(EloquentDataSource::class);
});

it('resolves a Query Builder', function () {
    expect(DataSourceFactory::make(DB::table('dt_test_posts')))->toBeInstanceOf(QueryBuilderDataSource::class);
});

it('resolves a RawSql value object', function () {
    expect(DataSourceFactory::make(RawSql::query('select * from dt_test_posts')))
        ->toBeInstanceOf(RawSqlDataSource::class);
});

it('resolves a Collection', function () {
    expect(DataSourceFactory::make(collect([['id' => 1]])))->toBeInstanceOf(CollectionDataSource::class);
});

it('resolves a plain array', function () {
    expect(DataSourceFactory::make([['id' => 1]]))->toBeInstanceOf(CollectionDataSource::class);
});

it('passes an existing DataSource instance straight through', function () {
    $source = new CollectionDataSource(collect());

    expect(DataSourceFactory::make($source))->toBe($source);
});

it('throws for an unsupported value', function () {
    expect(fn () => DataSourceFactory::make('not-a-recognized-source'))
        ->toThrow(UnsupportedDataSourceException::class);
});

it('resolves a third-party query type registered via extend()', function () {
    // Stands in for a query type the factory has no built-in branch for
    // (e.g. a Laravel Scout Builder), to prove the extension point works
    // for types genuinely outside the four built-in adapters.
    $thirdPartyQuery = new class {};

    $customSource = new class implements DataSource
    {
        public function applySearch(string $term, array $searchableFields): static
        {
            return $this;
        }

        public function applySort(?string $field, string $direction): static
        {
            return $this;
        }

        public function applyWhere(string $field, string $operator, mixed $value): static
        {
            return $this;
        }

        public function applyWhereIn(string $field, array $values): static
        {
            return $this;
        }

        public function paginate(int $perPage, int $page): PaginationResult
        {
            return new PaginationResult(collect(), 0, $perPage, $page);
        }

        public function count(): int
        {
            return 0;
        }

        public function pluckKeys(string|Closure $keyResolver): array
        {
            return [];
        }

        public function aggregate(string $function, string $column): mixed
        {
            return null;
        }

        public function raw(): mixed
        {
            return null;
        }
    };

    DataSourceFactory::extend($thirdPartyQuery::class, fn () => $customSource);

    expect(DataSourceFactory::make($thirdPartyQuery))->toBe($customSource);
});
