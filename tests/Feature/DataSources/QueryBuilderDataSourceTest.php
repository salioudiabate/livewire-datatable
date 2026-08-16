<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Salioudiabate\LivewireDatatable\DataSources\QueryBuilderDataSource;

require_once __DIR__.'/Contract.php';

beforeEach(fn () => seedDataSourceContractFixture());

runDataSourceContractTests(fn () => new QueryBuilderDataSource(DB::table('dt_test_posts')));

it('deletes rows one at a time and reports the summary', function () {
    $source = new QueryBuilderDataSource(DB::table('dt_test_posts'));

    $summary = $source->deleteByKeys('id', [1, 2]);

    expect($summary->deletedKeys)->toBe([1, 2])
        ->and($summary->deletedCount())->toBe(2)
        ->and($summary->hasFailures())->toBeFalse()
        ->and(DB::table('dt_test_posts')->count())->toBe(3);
});

it('skips a row when beforeDelete returns false', function () {
    $source = new QueryBuilderDataSource(DB::table('dt_test_posts'));

    $summary = $source->deleteByKeys('id', [1, 2], beforeDelete: fn (object $row) => $row->id !== 1);

    expect($summary->deletedKeys)->toBe([2])
        ->and(DB::table('dt_test_posts')->where('id', 1)->exists())->toBeTrue();
});

it('records a failure instead of aborting the batch on a foreign key violation', function () {
    DB::table('dt_test_comments')->insert(['id' => 1, 'dt_test_post_id' => 1, 'body' => 'hello']);

    $source = new QueryBuilderDataSource(DB::table('dt_test_posts'));

    $summary = $source->deleteByKeys('id', [1, 2]);

    expect($summary->deletedKeys)->toBe([2])
        ->and(array_keys($summary->failures))->toBe([1])
        ->and(DB::table('dt_test_posts')->where('id', 1)->exists())->toBeTrue();
});

it('refuses to delete when the query is not backed by a plain table name', function () {
    $query = DB::connection()->query()->fromSub('select * from dt_test_posts', 'dt_sub');

    $source = new QueryBuilderDataSource($query);

    expect(fn () => $source->deleteByKeys('id', [1]))->toThrow(RuntimeException::class);
});
