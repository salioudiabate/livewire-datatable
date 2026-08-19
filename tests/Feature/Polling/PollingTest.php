<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('does not poll by default', function () {
    Livewire::test(PostsTable::class)->assertDontSeeHtml('wire:poll');
});

it('adds wire:poll with the configured interval once pollInterval() is set', function () {
    Livewire::test(new class extends PostsTable
    {
        public function pollInterval(): ?int
        {
            return 5000;
        }
    })->assertSeeHtml('wire:poll.5000ms="$refresh"');
});
