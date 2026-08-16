<?php

declare(strict_types=1);

use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PrefixedPostsTableA;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PrefixedPostsTableB;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\UnboundPostsTable;

function callProtected(object $object, string $method, mixed ...$args): mixed
{
    $reflection = new ReflectionMethod($object, $method);
    $reflection->setAccessible(true);

    return $reflection->invokeArgs($object, $args);
}

it('derives a default url key from the component class name', function () {
    $queryString = callProtected(new PostsTable, 'queryString');

    expect($queryString['search']['as'])->toBe('posts-table-q')
        ->and($queryString['page']['as'])->toBe('posts-table-page');
});

it('binds search, filters, sort and pagination state, each with a clean-url "except" default', function () {
    $queryString = callProtected(new PostsTable, 'queryString');

    expect(array_keys($queryString))->toBe(['search', 'filterValues', 'sortField', 'sortDirection', 'page', 'perPage'])
        ->and($queryString['search']['except'])->toBe('')
        ->and($queryString['page']['except'])->toBe(1)
        ->and($queryString['sortDirection']['except'])->toBe('asc');
});

it('produces zero query-string alias collisions between two differently-prefixed instances on the same page', function () {
    $a = callProtected(new PrefixedPostsTableA, 'queryString');
    $b = callProtected(new PrefixedPostsTableB, 'queryString');

    $aliasesA = array_column($a, 'as');
    $aliasesB = array_column($b, 'as');

    expect($aliasesA)->not->toBe([])
        ->and(array_intersect($aliasesA, $aliasesB))->toBe([]);
});

it('returns no query-string bindings at all when url binding is disabled', function () {
    expect(callProtected(new UnboundPostsTable, 'queryString'))->toBe([]);
});
