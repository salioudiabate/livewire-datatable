<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

beforeEach(function () {
    foreach (range(1, 15) as $i) {
        DB::table('dt_test_posts')->insert([
            'title' => "Post {$i}",
            'status' => $i <= 5 ? 'draft' : 'published',
            'views' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
});

it('returns every matching row across every page, not just perPage()-worth', function () {
    $component = new PostsTable;

    $rows = $component->allFilteredRows();

    expect($rows)->toHaveCount(15)
        ->and($rows->first())->toBeInstanceOf(Post::class);
});

it('narrows to the current search/filters, same scope as filteredDataSource()', function () {
    $component = new PostsTable;
    $component->filterValues = ['status' => 'draft'];

    expect($component->allFilteredRows())->toHaveCount(5);
});

it('still returns the complete, correctly-ordered set when chunkSize forces multiple pagination round-trips', function () {
    $component = new PostsTable;

    $rows = $component->allFilteredRows(chunkSize: 4);

    expect($rows)->toHaveCount(15)
        ->and($rows->pluck('title')->all())->toBe(
            collect(range(1, 15))->map(fn (int $i) => "Post {$i}")->all()
        );
});
