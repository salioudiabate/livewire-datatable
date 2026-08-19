<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Filters\BooleanFilter;
use Salioudiabate\LivewireDatatable\Filters\DateRangeFilter;
use Salioudiabate\LivewireDatatable\Filters\MultiSelectFilter;
use Salioudiabate\LivewireDatatable\Filters\NumberRangeFilter;
use Salioudiabate\LivewireDatatable\Filters\SelectFilter;
use Salioudiabate\LivewireDatatable\Filters\TextFilter;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('renders the configured filter label classes on every filter type', function () {
    config(['livewire-datatable.classes.filter_label' => 'filter-label-marker-xyz']);

    Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [TextFilter::make('Title', 'title')];
        }
    })->assertSee('filter-label-marker-xyz', false);
});

it('renders the configured filter_input classes on a plain text filter', function () {
    config(['livewire-datatable.classes.filter_input' => 'filter-input-marker-xyz']);

    Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [TextFilter::make('Title', 'title')];
        }
    })->assertSee('filter-input-marker-xyz', false);
});

it('renders the configured filter_select classes on SelectFilter and BooleanFilter', function () {
    config(['livewire-datatable.classes.filter_select' => 'filter-select-marker-xyz']);

    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [
                SelectFilter::make('Status', 'status')->options(['draft' => 'Draft']),
                BooleanFilter::make('Featured', 'featured'),
            ];
        }
    })->html();

    expect(substr_count($html, 'filter-select-marker-xyz'))->toBe(2);
});

it('renders the configured filter_multiselect classes on MultiSelectFilter, distinct from filter_select', function () {
    config(['livewire-datatable.classes.filter_multiselect' => 'filter-multiselect-marker-xyz']);

    Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [MultiSelectFilter::make('Status', 'status')->options(['draft' => 'Draft'])];
        }
    })->assertSee('filter-multiselect-marker-xyz', false);
});

it('lets a single filter override its input classes via cssClass(), taking precedence over the global config', function () {
    config(['livewire-datatable.classes.filter_input' => 'filter-input-global-marker']);

    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [TextFilter::make('Title', 'title')->cssClass('filter-input-per-instance-marker')];
        }
    })->html();

    expect($html)->toContain('filter-input-per-instance-marker')
        ->and($html)->not->toContain('filter-input-global-marker');
});

it('applies cssClass() to both halves of a range filter', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [NumberRangeFilter::make('Views', 'views')->cssClass('range-input-marker-xyz')];
        }
    })->html();

    expect(substr_count($html, 'range-input-marker-xyz'))->toBe(2);
});

it('applies cssClass() to both halves of a date range filter', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [DateRangeFilter::make('Created', 'created_at')->cssClass('date-range-input-marker-xyz')];
        }
    })->html();

    expect(substr_count($html, 'date-range-input-marker-xyz'))->toBe(2);
});
