<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('shows the built-in empty state message by default', function () {
    Livewire::test(PostsTable::class)
        ->assertSee(__('livewire-datatable::livewire-datatable.no_results'))
        ->assertSee(__('livewire-datatable::livewire-datatable.no_results_hint'));
});

it('renders a custom view instead once emptyStateView() is set', function () {
    View::addLocation(__DIR__.'/../../Fixtures/views');

    $html = Livewire::test(new class extends PostsTable
    {
        public function emptyStateView(): ?string
        {
            return 'custom-empty-state';
        }
    })->html();

    expect($html)
        ->toContain('No posts yet — create one!')
        ->toContain('custom-empty-state-marker-xyz')
        ->not->toContain(__('livewire-datatable::livewire-datatable.no_results'));
});

it('passes the correct colspan through to a custom empty state view', function () {
    View::addLocation(__DIR__.'/../../Fixtures/views');

    // PostsTable has 3 columns and no bulk actions/row actions declared.
    $html = Livewire::test(new class extends PostsTable
    {
        public function emptyStateView(): ?string
        {
            return 'custom-empty-state';
        }
    })->html();

    expect($html)->toContain('colspan="3"');
});
