<?php

declare(strict_types=1);

it('boots the service provider and loads the config', function () {
    expect(config('livewire-datatable.theme.primary'))->toBe('#4f46e5');
});

it('loads the english translations', function () {
    expect(__('livewire-datatable::livewire-datatable.search'))->toBe('Search...');
});
