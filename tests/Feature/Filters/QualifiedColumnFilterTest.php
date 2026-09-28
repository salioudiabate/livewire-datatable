<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Filters\BooleanFilter;
use Salioudiabate\LivewireDatatable\Filters\SelectFilter;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Beta', 'status' => 'draft', 'views' => 0, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

/**
 * A qualified column (needed once the query joins) must still filter: its dots are kept for the
 * query but not in the Livewire state key, or wire:model would nest the value out of reach.
 */
class QualifiedFilterPostsTable extends PostsTable
{
    public function filters(): array
    {
        return [
            SelectFilter::make('Status', 'dt_test_posts.status')->options(['published' => 'Published', 'draft' => 'Draft']),
            BooleanFilter::make('Popular', 'popular')->column('dt_test_posts.views'),
        ];
    }
}

it('filters on a qualified column through a dot-free state key', function () {
    $filter = (new QualifiedFilterPostsTable)->filters()[0];

    expect($filter->key())->toBe('dt_test_posts__status')
        ->and($filter->getColumn())->toBe('dt_test_posts.status');

    Livewire::test(QualifiedFilterPostsTable::class)
        ->set('filterValues.dt_test_posts__status', 'published')
        ->assertSee('Alpha')
        ->assertDontSee('Beta')
        ->assertSeeHtml('wire:model.live="filterValues.dt_test_posts__status"');
});

it('lets column() point a filter at another column than its key', function () {
    $filter = (new QualifiedFilterPostsTable)->filters()[1];

    expect($filter->key())->toBe('popular')->and($filter->getColumn())->toBe('dt_test_posts.views');
});
