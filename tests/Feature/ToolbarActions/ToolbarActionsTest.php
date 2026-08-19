<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\ToolbarAction;
use Salioudiabate\LivewireDatatable\ToolbarActionGroup;

it('renders a url-triggered toolbar action as a link', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Docs')->url('https://example.test/docs')];
        }
    })
        ->assertSee('Docs')
        ->assertSeeHtml('href="https://example.test/docs"');
});

it('renders a dispatch-triggered toolbar action wired to $dispatch', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Nouveau')->dispatch('openModal', ['component' => 'foo'])];
        }
    })->assertSee("\$dispatch('openModal', JSON.parse(", false);
});

it('calls the target method through runToolbarAction when an action-triggered button is clicked', function () {
    $test = Livewire::test(new class extends PostsTable
    {
        public bool $wasCalled = false;

        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Ping')->action('ping')];
        }

        public function ping(): void
        {
            $this->wasCalled = true;
        }
    });

    $test->call('runToolbarAction', 'ping');

    expect($test->get('wasCalled'))->toBeTrue();
});

it('aborts with a 403 when running a toolbar action that was not declared', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Ping')->action('ping')];
        }

        public function ping(): void {}
    })->call('runToolbarAction', 'somethingElse')->assertStatus(403);
});

it('hides an unauthorized toolbar action and blocks running it directly', function () {
    $test = Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Delete all')->action('destroyAll')->permission('never-granted')];
        }

        public function destroyAll(): void {}
    });

    $test->assertDontSee('Delete all');

    $test->call('runToolbarAction', 'destroyAll')->assertStatus(403);
});

it('splits toolbar actions between the left and right toolbar groups', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarAction::make('LeftOne')->align('left')->action('noop'),
                ToolbarAction::make('RightOne')->action('noop'),
            ];
        }

        public function noop(): void {}
    })
        ->assertSeeInOrder(['Filters', 'LeftOne', 'RightOne'])
        ->assertSee('LeftOne')
        ->assertSee('RightOne');
});

it('renders a toolbar action group as a single segmented control', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Group A')->action('noop'),
                    ToolbarAction::make('Group B')->action('noop'),
                ]),
            ];
        }

        public function noop(): void {}
    })->assertSee('Group A')->assertSee('Group B');
});

it('drops an empty toolbar action group when none of its actions are authorized', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Hidden')->action('noop')->permission('never-granted'),
                ]),
            ];
        }

        public function noop(): void {}
    })->assertDontSee('Hidden');
});

it('renders the configured toolbar action classes', function () {
    config(['livewire-datatable.classes.toolbar_action' => 'toolbar-action-marker-xyz']);

    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarAction::make('Ping')->action('noop')];
        }

        public function noop(): void {}
    })->assertSee('toolbar-action-marker-xyz', false);
});

it('renders the configured toolbar action group classes', function () {
    config(['livewire-datatable.classes.toolbar_action_group' => 'toolbar-action-group-marker-xyz']);

    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [ToolbarActionGroup::make([ToolbarAction::make('Ping')->action('noop')])];
        }

        public function noop(): void {}
    })->assertSee('toolbar-action-group-marker-xyz', false);
});

it('renders a toolbar action group as a dropdown once dropdown() is called', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Sort by price')->action('noop'),
                    ToolbarAction::make('Sort by stock')->action('noop'),
                ])->dropdown('Trier par'),
            ];
        }

        public function noop(): void {}
    })
        ->assertSee('Trier par')
        ->assertSee('Sort by price')
        ->assertSee('Sort by stock')
        ->assertSeeHtml('x-data="{ open: false }"');
});

it('drops an empty toolbar action dropdown when none of its actions are authorized', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Hidden')->action('noop')->permission('never-granted'),
                ])->dropdown('Trier par'),
            ];
        }

        public function noop(): void {}
    })->assertDontSee('Trier par')->assertDontSee('Hidden');
});

it('renders the configured toolbar action dropdown panel classes', function () {
    config(['livewire-datatable.classes.toolbar_action_dropdown' => 'toolbar-action-dropdown-marker-xyz']);

    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([ToolbarAction::make('Ping')->action('noop')])->dropdown('Menu'),
            ];
        }

        public function noop(): void {}
    })->assertSee('toolbar-action-dropdown-marker-xyz', false);
});

it('renders an icon set via ToolbarActionGroup::icon() on the dropdown trigger', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([ToolbarAction::make('Ping')->action('noop')])
                    ->icon('<svg class="group-icon-marker-xyz"></svg>')
                    ->dropdown('Menu'),
            ];
        }

        public function noop(): void {}
    })->assertSeeHtml('group-icon-marker-xyz');
});

it('keeps an icon set via icon() even when dropdown() is called afterward with no icon argument', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([ToolbarAction::make('Ping')->action('noop')])
                    ->icon('<svg class="group-icon-order-marker-xyz"></svg>')
                    ->dropdown('Menu'), // no icon arg here — must not wipe the one set above
            ];
        }

        public function noop(): void {}
    })->assertSeeHtml('group-icon-order-marker-xyz');
});

it('renders per-item icon and cssClass on a segmented group action, not just standalone ones', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Prix')
                        ->action('noop')
                        ->icon('<svg class="item-icon-marker-xyz"></svg>')
                        ->cssClass('item-class-marker-xyz'),
                ]),
            ];
        }

        public function noop(): void {}
    })
        ->assertSeeHtml('item-icon-marker-xyz')
        ->assertSeeHtml('item-class-marker-xyz');
});

it('renders per-item icon and cssClass on a dropdown item, not just standalone ones', function () {
    Livewire::test(new class extends PostsTable
    {
        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Prix')
                        ->action('noop')
                        ->icon('<svg class="dropdown-item-icon-marker-xyz"></svg>')
                        ->cssClass('dropdown-item-class-marker-xyz'),
                ])->dropdown('Trier par'),
            ];
        }

        public function noop(): void {}
    })
        ->assertSeeHtml('dropdown-item-icon-marker-xyz')
        ->assertSeeHtml('dropdown-item-class-marker-xyz');
});

it('runs a dropdown item action through runToolbarAction the same as any other action', function () {
    $test = Livewire::test(new class extends PostsTable
    {
        public bool $wasCalled = false;

        public function toolbarActions(): array
        {
            return [
                ToolbarActionGroup::make([
                    ToolbarAction::make('Ping')->action('ping'),
                ])->dropdown('Menu'),
            ];
        }

        public function ping(): void
        {
            $this->wasCalled = true;
        }
    });

    $test->call('runToolbarAction', 'ping');

    expect($test->get('wasCalled'))->toBeTrue();
});
