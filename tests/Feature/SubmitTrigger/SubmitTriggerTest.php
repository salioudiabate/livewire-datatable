<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\BulkAction;
use Salioudiabate\LivewireDatatable\RowAction;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\DeletablePostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\ToolbarAction;

it('renders a bulk action icon on the wire:click-triggered button, not just submit()', function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);

    Livewire::test(new class extends DeletablePostsTable
    {
        public function bulkActions(): array
        {
            return [
                BulkAction::make('destroySelected', 'Delete')->icon('<svg class="icon-marker-xyz"></svg>'),
            ];
        }
    })
        ->set('selected', ['1'])
        ->assertSeeHtml('icon-marker-xyz');
});

it('renders a row action icon on the url-linked and wire:click-triggered items, not just submit()', function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);

    Livewire::test(new class extends PostsTable
    {
        public function rowActions(): array
        {
            return [
                RowAction::make('View')->url(fn ($row) => "/posts/{$row->id}")->icon('<svg class="icon-marker-url"></svg>'),
                RowAction::make('Archive')->action('archive')->icon('<svg class="icon-marker-action"></svg>'),
            ];
        }

        public function archive(string $id): void {}
    })
        ->assertSeeHtml('icon-marker-url')
        ->assertSeeHtml('icon-marker-action');
});

it('renders a submit-triggered toolbar action as a real form post', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarAction::make('Export PDF')->submit('/export-pdf', 'POST', ['status' => 'draft'], '_blank'),
            ];
        }
    })
        ->assertSeeHtml('action="/export-pdf"')
        ->assertSeeHtml('method="POST"')
        ->assertSeeHtml('target="_blank"')
        ->assertSeeHtml('name="status"')
        ->assertSeeHtml('value="draft"')
        ->assertSeeHtml('name="_token"'); // CSRF present for a non-GET submission
});

it('omits CSRF and method spoofing for a GET submit-triggered action', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Export PDF')->submit('/export-pdf', 'GET')];
        }
    })->html();

    expect($html)->not->toContain('name="_token"')
        ->and($html)->not->toContain('name="_method"')
        ->and($html)->toContain('method="GET"');
});

it('spoofs the HTTP method for a PUT submit-triggered action', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Archive')->submit('/archive', 'PUT')];
        }
    })
        ->assertSeeHtml('method="POST"')
        ->assertSeeHtml('name="_method"')
        ->assertSeeHtml('value="PUT"');
});

it('resolves a closure submit() data payload against the component\'s current state', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarAction::make('Export PDF')->submit('/export-pdf', 'POST', fn () => ['search' => $this->search]),
            ];
        }
    })
        ->set('search', 'widget')
        ->assertSeeHtml('name="search"')
        ->assertSeeHtml('value="widget"');
});

it('guards a submit-triggered toolbar action with the overridable confirmation', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarAction::make('Export PDF')->submit('/export-pdf')->confirm('Generate the PDF?'),
            ];
        }
    })
        ->assertSeeHtml('window.LivewireDatatable?.confirm')
        ->assertSeeHtml('&#039;Generate the PDF?&#039;')
        ->assertSeeHtml('$el.submit()');
});

it('renders a submit-triggered row action resolved per-row', function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);

    Livewire::test(new class extends PostsTable
    {
        public function rowActions(): array
        {
            return [
                RowAction::make('PDF')->submit(fn ($row) => "/posts/{$row->id}/pdf", 'POST', fn ($row) => ['id' => $row->id]),
            ];
        }
    })
        ->assertSeeHtml('action="/posts/1/pdf"')
        ->assertSeeHtml('name="id"')
        ->assertSeeHtml('value="1"');
});

it('sends the current selection as selected[] on a submit-triggered bulk action', function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Bravo', 'status' => 'published', 'views' => 5, 'created_at' => now(), 'updated_at' => now()],
    ]);

    Livewire::test(new class extends DeletablePostsTable
    {
        public function bulkActions(): array
        {
            return [
                BulkAction::make('exportSelected', 'Export selection')->submit('/export-selection'),
            ];
        }
    })
        ->set('selected', ['1', '2'])
        ->assertSeeHtml('action="/export-selection"')
        ->assertSeeHtml('name="selected[]"')
        ->assertSeeHtml('value="1"')
        ->assertSeeHtml('value="2"');
});

it('routes every wire-triggered confirmation through window.LivewireDatatable.confirm, falling back to confirm()', function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $html = Livewire::test(new class extends DeletablePostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Clear')->action('clearAll')->confirm('Clear everything?')];
        }

        public function rowActions(): array
        {
            return [RowAction::make('Delete')->action('deleteRow')->confirm('Delete "this" row?')];
        }

        public function bulkActions(): array
        {
            return [BulkAction::make('destroySelected', 'Delete')->confirm('Delete selected?')];
        }
    })->set('selected', ['1'])->html();

    expect($html)
        ->toContain('window.LivewireDatatable?.confirm ?? ((message, proceed) => window.confirm(message) && proceed())')
        ->toContain("\$wire.runToolbarAction('clearAll')")
        ->toContain("\$wire.runBulkAction('destroySelected')")
        ->toContain("\$wire.runRowAction('deleteRow'")
        // The message is JS-encoded: a quote in it cannot break out of the attribute.
        ->toContain('Delete \\u0022this\\u0022 row?')
        ->not->toContain('confirm(\'Clear everything?\') &&');
});
