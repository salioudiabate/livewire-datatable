<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Salioudiabate\LivewireDatatable\DataSources\EloquentDataSource;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

require_once __DIR__.'/Contract.php';

beforeEach(fn () => seedDataSourceContractFixture());

runDataSourceContractTests(fn () => new EloquentDataSource(Post::query()));

it('searches across a related model via dot notation', function () {
    DB::table('dt_test_authors')->insert(['id' => 1, 'name' => 'Jane Doe']);
    DB::table('dt_test_posts')->where('id', 1)->update(['dt_test_author_id' => 1]);

    $source = new EloquentDataSource(Post::query());

    $result = $source->applySearch('Jane', ['author.name'])->paginate(perPage: 10, page: 1);

    expect($result->total)->toBe(1)
        ->and(collect($result->items)->pluck('id')->all())->toBe([1]);
});

it('deletes rows one at a time and reports the summary', function () {
    $source = new EloquentDataSource(Post::query());

    $summary = $source->deleteByKeys('id', [1, 2]);

    expect($summary->deletedKeys)->toBe([1, 2])
        ->and($summary->deletedCount())->toBe(2)
        ->and($summary->hasFailures())->toBeFalse()
        ->and(Post::query()->count())->toBe(3);
});

it('skips a row when beforeDelete returns false', function () {
    $source = new EloquentDataSource(Post::query());

    $summary = $source->deleteByKeys('id', [1, 2], beforeDelete: fn (Post $post) => $post->id !== 1);

    expect($summary->deletedKeys)->toBe([2])
        ->and(Post::query()->whereKey(1)->exists())->toBeTrue();
});

it('invokes afterDelete for every successfully deleted row', function () {
    $source = new EloquentDataSource(Post::query());
    $seen = [];

    $source->deleteByKeys('id', [1, 2], afterDelete: function (Post $post) use (&$seen) {
        $seen[] = $post->id;
    });

    expect($seen)->toBe([1, 2]);
});

it('records a failure instead of aborting the batch on a foreign key violation', function () {
    DB::table('dt_test_comments')->insert(['id' => 1, 'dt_test_post_id' => 1, 'body' => 'hello']);

    $source = new EloquentDataSource(Post::query());

    $summary = $source->deleteByKeys('id', [1, 2]);

    expect($summary->deletedKeys)->toBe([2])
        ->and($summary->hasFailures())->toBeTrue()
        ->and(array_keys($summary->failures))->toBe([1])
        ->and(Post::query()->whereKey(1)->exists())->toBeTrue();
});
