<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('defaults the export button label to the translation', function () {
    Livewire::test(PostsTable::class)->assertSee(__('livewire-datatable::livewire-datatable.export'));
});

it('lets the export button label be overridden per-table', function () {
    Livewire::test(new class extends PostsTable
    {
        public function exportLabel(): string
        {
            return 'Download data';
        }
    })->assertSee('Download data');
});

it('defaults the filters button label to the translation', function () {
    Livewire::test(PostsTable::class)->assertSee(__('livewire-datatable::livewire-datatable.filters'));
});

it('lets the filters button label be overridden per-table', function () {
    Livewire::test(new class extends PostsTable
    {
        public function filtersLabel(): string
        {
            return 'Refine';
        }
    })->assertSee('Refine');
});

it('defaults the search placeholder to the translation', function () {
    Livewire::test(PostsTable::class)->assertSeeHtml('placeholder="'.__('livewire-datatable::livewire-datatable.search').'"');
});

it('lets the search placeholder be overridden per-table', function () {
    Livewire::test(new class extends PostsTable
    {
        public function searchPlaceholder(): string
        {
            return 'Find a post...';
        }
    })->assertSeeHtml('placeholder="Find a post..."');
});
