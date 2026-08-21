<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('renders a wire:offline banner by default', function () {
    $html = Livewire::test(PostsTable::class)->html();

    expect($html)->toContain('wire:offline')
        ->and($html)->toContain('Internet connection lost')
        ->and($html)->toContain('Actions will resume once you');
});

it('hides the offline banner entirely when showOfflineIndicator() returns false', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function showOfflineIndicator(): bool
        {
            return false;
        }
    })->html();

    expect($html)->not->toContain('wire:offline');
});

it('renders the configured offline banner classes', function () {
    config(['livewire-datatable.classes.offline_banner' => 'offline-banner-marker-xyz']);

    Livewire::test(PostsTable::class)->assertSee('offline-banner-marker-xyz', false);
});

it('lets the offline banner classes be overridden per-table', function () {
    Livewire::test(new class extends PostsTable
    {
        public function offlineBannerClasses(): string
        {
            return 'per-table-offline-marker';
        }
    })->assertSee('per-table-offline-marker', false);
});
