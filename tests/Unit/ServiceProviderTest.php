<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use Salioudiabate\LivewireDatatable\LivewireDataTableServiceProvider;

/**
 * Guards the exact vendor:publish tags documented in the README's
 * Installation section — if spatie/laravel-package-tools ever changes its
 * tag-naming convention, this fails loudly instead of the documented
 * commands silently doing nothing for a real consumer.
 */
it('registers the config publish tag', function () {
    expect(ServiceProvider::pathsToPublish(LivewireDataTableServiceProvider::class, 'livewire-datatable-config'))
        ->not->toBeEmpty();
});

it('registers the views publish tag', function () {
    expect(ServiceProvider::pathsToPublish(LivewireDataTableServiceProvider::class, 'livewire-datatable-views'))
        ->not->toBeEmpty();
});

it('registers the translations publish tag', function () {
    expect(ServiceProvider::pathsToPublish(LivewireDataTableServiceProvider::class, 'livewire-datatable-translations'))
        ->not->toBeEmpty();
});
