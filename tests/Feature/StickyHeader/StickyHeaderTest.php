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

it('does not add sticky positioning or a bounded-height wrapper by default', function () {
    $html = Livewire::test(PostsTable::class)->html();

    expect($html)->not->toContain('position: sticky; top: 0; z-index: 10;')
        ->and($html)->not->toContain('max-height:');
});

it('pins the header and bounds the wrapper height once stickyHeader() is true', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }
    })->html();

    expect($html)->toContain('position: sticky; top: 0; z-index: 10;')
        ->and($html)->toContain('max-height: 70vh; overflow-y: auto;');
});

it('uses a custom max-height from stickyHeaderMaxHeight()', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }

        protected function stickyHeaderMaxHeight(): string
        {
            return '400px';
        }
    })->html();

    expect($html)->toContain('max-height: 400px; overflow-y: auto;');
});

it('combines sticky header positioning with an already-frozen column instead of one overwriting the other', function () {
    $html = Livewire::test(new class extends FrozenColumnsPostsTable
    {
        public function stickyHeader(): bool
        {
            return true;
        }
    })->html();

    expect($html)->toContain('position: sticky; left: 0px; width: 150px; min-width: 150px; z-index: 1; position: sticky; top: 0; z-index: 10;');
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
