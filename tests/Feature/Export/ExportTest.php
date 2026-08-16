<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FullFeaturedPostsTable;

beforeEach(function () {
    foreach (range(1, 3) as $i) {
        DB::table('dt_test_posts')->insert([
            'title' => "Post {$i}",
            'status' => 'published',
            'views' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
});

it('triggers a file download from the export action', function () {
    Livewire::test(FullFeaturedPostsTable::class)
        ->call('export')
        ->assertFileDownloaded();
});

it('exports only the currently visible columns', function () {
    // "Views" is toggleable(visibleByDefault: false) on this fixture.
    $test = Livewire::test(FullFeaturedPostsTable::class);

    $csv = captureStreamedResponse($test->instance()->export());
    $header = strtok($csv, "\n");

    expect($header)->toContain('Title')
        ->and($header)->toContain('Status')
        ->and($header)->not->toContain('Views');
});

it('includes a previously-hidden column once it is toggled visible', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class)->call('toggleColumnVisibility', 'views');

    $csv = captureStreamedResponse($test->instance()->export());
    $header = strtok($csv, "\n");

    expect($header)->toContain('Views');
});

it('respects the current search term when exporting', function () {
    $test = Livewire::test(FullFeaturedPostsTable::class)->set('search', 'Post 1');

    $csv = captureStreamedResponse($test->instance()->export());
    $lines = array_values(array_filter(explode("\n", $csv)));

    expect($lines)->toHaveCount(2);
});
