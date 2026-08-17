<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PersistedDensityPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);
});

it('defaults to comfortable density', function () {
    Livewire::test(PostsTable::class)->assertSet('density', 'comfortable');
});

it('changes density and reflects it in the rendered header padding', function () {
    $test = Livewire::test(PostsTable::class)->call('setDensity', 'compact');

    expect($test->instance()->density)->toBe('compact')
        ->and($test->html())->toContain('px-3 py-1.5');
});

it('ignores an invalid density value', function () {
    $test = Livewire::test(PostsTable::class)->call('setDensity', 'ultra-wide');

    expect($test->instance()->density)->toBe('comfortable');
});

it('persists density across a fresh mount when persistDensity() is set', function () {
    Livewire::test(PersistedDensityPostsTable::class)->call('setDensity', 'spacious');

    $fresh = Livewire::test(PersistedDensityPostsTable::class);

    expect($fresh->instance()->density)->toBe('spacious');
});

it('does not leak density state between components without persistDensity()', function () {
    Livewire::test(PostsTable::class)->call('setDensity', 'spacious');

    $fresh = Livewire::test(PostsTable::class);

    expect($fresh->instance()->density)->toBe('comfortable');
});
