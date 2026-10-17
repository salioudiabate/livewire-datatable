<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);
});

it('labels every cell and makes the first column the card title by default', function () {
    $html = Livewire::test(PostsTable::class)->html();

    expect($html)->toContain('dt-cards')
        ->and($html)->toContain('data-label="Title"')
        ->and($html)->toContain('data-label="Views"')
        ->and($html)->toContain('@media (max-width: 639.98px)')
        ->and(preg_match_all('/data-label="[^"]*"\s+data-dt-cell="title"/', $html))->toBe(1)
        ->and($html)->toMatch('/data-label="Title"\s+data-dt-cell="title"/');
});

it('uses the column marked cardTitle() and leaves hideOnCards() columns out of the card', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function columns(): array
        {
            return [
                Column::make('Title', 'title'),
                Column::make('Status', 'status')->cardTitle(),
                Column::make('Views', 'views')->hideOnCards(),
            ];
        }
    })->html();

    expect($html)->toMatch('/data-label="Status"\s+data-dt-cell="title"/')
        ->and($html)->toMatch('/data-label="Views"\s+data-dt-card-hidden/')
        ->and($html)->not->toMatch('/data-label="Title"\s+data-dt-cell="title"/');
});

it('keeps the plain table when mobileCards() is off, and honours a custom breakpoint', function () {
    $off = Livewire::test(new class extends PostsTable
    {
        public function mobileCards(): bool
        {
            return false;
        }
    })->html();
    $custom = Livewire::test(new class extends PostsTable
    {
        public function mobileCardsBreakpoint(): int
        {
            return 768;
        }
    })->html();

    expect($off)->not->toContain('dt-cards')->and($off)->not->toContain('@media (max-width:')
        ->and($custom)->toContain('@media (max-width: 767.98px)');
});
