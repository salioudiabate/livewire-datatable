<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\DataSources\Deletable;
use Salioudiabate\LivewireDatatable\DataSources\RawSql;
use Salioudiabate\LivewireDatatable\DataSources\RawSqlDataSource;

require_once __DIR__.'/Contract.php';

beforeEach(fn () => seedDataSourceContractFixture());

runDataSourceContractTests(fn () => new RawSqlDataSource(RawSql::query('select * from dt_test_posts')));

it('parameterizes bindings safely instead of interpolating them', function () {
    $source = new RawSqlDataSource(RawSql::query(
        'select * from dt_test_posts where status = ?',
        ['published']
    ));

    expect($source->count())->toBe(3);
});

it('does not implement Deletable', function () {
    $source = new RawSqlDataSource(RawSql::query('select * from dt_test_posts'));

    expect($source)->not->toBeInstanceOf(Deletable::class);
});

it('binds the search term as a parameter instead of interpolating it', function () {
    $source = new RawSqlDataSource(RawSql::query('select * from dt_test_posts'));

    // A classic boolean-injection payload in the *value* must be treated as
    // a literal search string (safely bound) — if it were concatenated into
    // the SQL text instead, "OR '1'='1" would make every row match.
    $result = $source->applySearch("nonexistent' OR '1'='1", ['title'])->paginate(10, 1);

    expect($result->total)->toBe(0);
});

it('treats a crafted sort field as an identifier, never as injected SQL', function () {
    $source = new RawSqlDataSource(RawSql::query('select * from dt_test_posts'));

    // Column names always come from the developer's own Column definitions,
    // never end-user input — but even so, applySort() must only ever be able
    // to influence the ORDER BY identifier slot, never break out of it to
    // alter the query's WHERE clause or append another statement.
    $result = $source->applySort('id" OR 1=1 --', 'asc')->paginate(10, 1);

    // The crafted identifier doesn't match a real column, so it has no
    // sorting effect — but crucially it must not have smuggled in a WHERE
    // condition: all 5 seeded rows are still returned, not a filtered subset.
    expect($result->total)->toBe(5);
});
