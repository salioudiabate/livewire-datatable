<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Salioudiabate\LivewireDatatable\DataSources\DataSource;

/**
 * Canonical fixture rows shared by every adapter's contract test, so all
 * four are proven against the exact same logical dataset.
 *
 * @return array<int, array{id: int, title: string, status: string, views: int}>
 */
function dataSourceContractFixtureRows(): array
{
    return [
        ['id' => 1, 'title' => 'Alpha', 'status' => 'published', 'views' => 10],
        ['id' => 2, 'title' => 'Bravo', 'status' => 'draft', 'views' => 40],
        ['id' => 3, 'title' => 'Charlie', 'status' => 'published', 'views' => 20],
        ['id' => 4, 'title' => 'Delta', 'status' => 'draft', 'views' => 5],
        ['id' => 5, 'title' => 'Echo', 'status' => 'published', 'views' => 30],
    ];
}

function seedDataSourceContractFixture(): void
{
    foreach (dataSourceContractFixtureRows() as $row) {
        DB::table('dt_test_posts')->insert([
            ...$row,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

/**
 * Liskov Substitution, made testable: every DataSource adapter must satisfy
 * this exact same set of behavioral expectations against the same logical
 * dataset. Called once per adapter from its own thin test file.
 *
 * @param  Closure(): DataSource  $makeDataSource
 */
function runDataSourceContractTests(Closure $makeDataSource): void
{
    it('counts all rows before any filtering', function () use ($makeDataSource) {
        expect($makeDataSource()->count())->toBe(5);
    });

    it('searches across the given fields', function () use ($makeDataSource) {
        $result = $makeDataSource()->applySearch('Bravo', ['title'])->paginate(perPage: 10, page: 1);

        expect($result->total)->toBe(1)
            ->and(collect($result->items)->pluck('title')->all())->toBe(['Bravo']);
    });

    it('finds no rows when the search term matches nothing', function () use ($makeDataSource) {
        expect($makeDataSource()->applySearch('nonexistent-term', ['title'])->count())->toBe(0);
    });

    it('returns everything when the search term is empty', function () use ($makeDataSource) {
        expect($makeDataSource()->applySearch('', ['title'])->count())->toBe(5);
    });

    it('sorts ascending and descending', function () use ($makeDataSource) {
        $asc = $makeDataSource()->applySort('views', 'asc')->paginate(perPage: 10, page: 1);
        expect(collect($asc->items)->pluck('title')->all())
            ->toBe(['Delta', 'Alpha', 'Charlie', 'Echo', 'Bravo']);

        $desc = $makeDataSource()->applySort('views', 'desc')->paginate(perPage: 10, page: 1);
        expect(collect($desc->items)->pluck('title')->all())
            ->toBe(['Bravo', 'Echo', 'Charlie', 'Alpha', 'Delta']);
    });

    it('leaves ordering untouched when no sort field is given', function () use ($makeDataSource) {
        expect($makeDataSource()->applySort(null, 'asc')->count())->toBe(5);
    });

    it('paginates results', function () use ($makeDataSource) {
        $page1 = $makeDataSource()->applySort('views', 'asc')->paginate(perPage: 2, page: 1);

        expect($page1->total)->toBe(5)
            ->and($page1->perPage)->toBe(2)
            ->and($page1->currentPage)->toBe(1)
            ->and($page1->lastPage())->toBe(3)
            ->and(collect($page1->items)->pluck('title')->all())->toBe(['Delta', 'Alpha']);

        $page3 = $makeDataSource()->applySort('views', 'asc')->paginate(perPage: 2, page: 3);
        expect(collect($page3->items)->pluck('title')->all())->toBe(['Bravo']);
    });

    it('plucks keys by field name', function () use ($makeDataSource) {
        $ids = $makeDataSource()->applySort('id', 'asc')->pluckKeys('id');

        expect(array_map(intval(...), $ids))->toBe([1, 2, 3, 4, 5]);
    });

    it('plucks keys via a closure', function () use ($makeDataSource) {
        $titles = $makeDataSource()->applySort('id', 'asc')->pluckKeys(fn (mixed $row) => data_get($row, 'title'));

        expect($titles)->toBe(['Alpha', 'Bravo', 'Charlie', 'Delta', 'Echo']);
    });

    it('filters by an exact comparison', function () use ($makeDataSource) {
        $result = $makeDataSource()->applyWhere('status', '=', 'draft')->paginate(perPage: 10, page: 1);

        expect($result->total)->toBe(2)
            ->and(collect($result->items)->pluck('title')->sort()->values()->all())->toBe(['Bravo', 'Delta']);
    });

    it('filters by a comparison operator other than equals', function () use ($makeDataSource) {
        $result = $makeDataSource()->applyWhere('views', '>=', 20)->paginate(perPage: 10, page: 1);

        expect($result->total)->toBe(3);
    });

    it('filters by a set of values', function () use ($makeDataSource) {
        $result = $makeDataSource()->applyWhereIn('id', [1, 3])->paginate(perPage: 10, page: 1);

        expect($result->total)->toBe(2)
            ->and(collect($result->items)->pluck('title')->sort()->values()->all())->toBe(['Alpha', 'Charlie']);
    });

    it('aggregates sum, min, max and count', function () use ($makeDataSource) {
        expect((float) $makeDataSource()->aggregate('sum', 'views'))->toBe(105.0)
            ->and((float) $makeDataSource()->aggregate('min', 'views'))->toBe(5.0)
            ->and((float) $makeDataSource()->aggregate('max', 'views'))->toBe(40.0)
            ->and($makeDataSource()->aggregate('count', 'views'))->toBe(5);
    });

    it('rejects an aggregate function outside sum/avg/min/max/count instead of dynamically dispatching it', function () use ($makeDataSource) {
        // aggregate()'s $function ends up as a dynamic method call on the
        // underlying query/collection object ($query->{$function}(...)).
        // Every adapter must reject anything outside the known set before
        // that happens — a real query engine has far more callable methods
        // than just aggregate functions (delete(), truncate(), update()...).
        $makeDataSource()->aggregate('delete', 'views');
    })->throws(InvalidArgumentException::class);

    it('exposes the underlying raw object', function () use ($makeDataSource) {
        expect($makeDataSource()->raw())->not->toBeNull();
    });
}
