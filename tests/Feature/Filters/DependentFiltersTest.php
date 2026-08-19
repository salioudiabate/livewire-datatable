<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Filters\SelectFilter;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

/**
 * There's no dedicated "dependent filter" API — filters() is a plain method
 * re-invoked on every render, with full access to $this->filterValues, so a
 * child filter's options() can already be computed from whatever the parent
 * is currently set to. The one piece a cascade needs beyond that — clearing
 * a now-stale child value when the parent changes — comes from Livewire
 * itself: updating a nested property like filterValues.country fires
 * updatedFilterValuesCountry() natively (see
 * vendor/livewire/livewire's SupportLifecycleHooks), no package involvement
 * required. This test exists to guard that this composition keeps working,
 * not to add a feature.
 */
it('supports country -> town cascading filters via filters() reading state plus a native Livewire nested update hook', function () {
    $table = new class extends PostsTable
    {
        private array $towns = [
            'FR' => ['paris' => 'Paris', 'lyon' => 'Lyon'],
            'CI' => ['abidjan' => 'Abidjan', 'yamoussoukro' => 'Yamoussoukro'],
        ];

        public function filters(): array
        {
            $country = $this->filterValues['country'] ?? null;

            return [
                SelectFilter::make('Country', 'country')->options(['FR' => 'France', 'CI' => "Côte d'Ivoire"]),
                SelectFilter::make('Town', 'town')->options($country ? $this->towns[$country] : []),
            ];
        }

        public function updatedFilterValuesCountry(): void
        {
            unset($this->filterValues['town']);
        }
    };

    $test = Livewire::test($table);

    expect($test->html())->not->toContain('Paris');

    $test->set('filterValues.country', 'FR');

    expect($test->html())->toContain('Paris')
        ->and($test->html())->toContain('Lyon')
        ->and($test->html())->not->toContain('Abidjan');

    $test->set('filterValues.town', 'paris');

    expect($test->get('filterValues.town'))->toBe('paris');

    $test->set('filterValues.country', 'CI');

    expect($test->get('filterValues.town'))->toBeNull()
        ->and($test->html())->toContain('Abidjan')
        ->and($test->html())->not->toContain('Paris');
});
