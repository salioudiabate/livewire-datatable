<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\FrozenColumnsPostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);
});

it('does not add sticky positioning to the header by default', function () {
    $html = Livewire::test(PostsTable::class)->html();

    expect($html)->not->toContain('position: sticky; top:');
});

it('pins the header at the top of the scroll context once stickyHeader() is true', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }
    })->html();

    expect($html)->toContain('position: sticky; top: 0px; z-index: 10;');
});

it('offsets the sticky header by stickyHeaderOffset()', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }

        protected function stickyHeaderOffset(): int
        {
            return 64;
        }
    })->html();

    expect($html)->toContain('position: sticky; top: 64px; z-index: 10;');
});

it('combines sticky header positioning with an already-frozen column instead of one overwriting the other', function () {
    $html = Livewire::test(new class extends FrozenColumnsPostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }
    })->html();

    expect($html)->toContain('position: sticky; left: 0px; width: 150px; min-width: 150px; z-index: 1; position: sticky; top: 0px; z-index: 10;');
});

it('gives every sticky header cell an opaque background so scrolled content does not bleed through', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }
    })->html();

    expect(substr_count($html, 'bg-slate-50'))->toBeGreaterThanOrEqual(3); // thead itself + each <th>
});
