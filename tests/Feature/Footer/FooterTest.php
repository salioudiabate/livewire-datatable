<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Beta', 'status' => 'draft', 'views' => 25, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Gamma', 'status' => 'published', 'views' => 5, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('renders no footer block by default', function () {
    config(['livewire-datatable.classes.footer_wrapper' => 'footer-wrapper-marker-xyz']);

    $html = Livewire::test(PostsTable::class)->html();

    expect($html)->not->toContain('footer-wrapper-marker-xyz');
});

it('renders footer() summary blocks split by align, defaulting unset align to right', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function footer(): array
        {
            return [
                ['label' => 'Count', 'value' => '3', 'align' => 'left'],
                ['label' => 'Total views', 'value' => '40'],
            ];
        }
    })->html();

    expect($html)->toContain('Count')
        ->and($html)->toContain('Total views')
        ->and($html)->toContain('40');
});

it('computes footer totals against filteredDataSource() so they cover every matching row, not just the current page', function () {
    $table = new class extends PostsTable
    {
        public function perPage(): int
        {
            return 1;
        }

        public function footer(): array
        {
            return [
                ['label' => 'Total views', 'value' => (string) $this->filteredDataSource()->aggregate('sum', 'views')],
            ];
        }
    };

    $html = Livewire::test($table)->html();

    // Only 1 row is shown on the page (perPage 1), but the footer sums all 3.
    expect($html)->toContain('Total views')
        ->and($html)->toContain('>40<');
});

it('only sums the filtered subset once a filter narrows the result set', function () {
    $table = new class extends PostsTable
    {
        public function footer(): array
        {
            return [
                ['label' => 'Total views', 'value' => (string) $this->filteredDataSource()->aggregate('sum', 'views')],
            ];
        }
    };

    $html = Livewire::test($table)
        ->set('filterValues.status', 'published')
        ->html();

    // Alpha (10) + Gamma (5) = 15, Beta (draft, 25) excluded.
    expect($html)->toContain('>15<');
});
