<?php

declare(strict_types=1);

use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('emits the configured theme colors as css custom properties scoped to .dt-root', function () {
    config(['livewire-datatable.theme.primary' => '#ff0000']);

    Livewire::test(PostsTable::class)
        ->assertSee('.dt-root', false)
        ->assertSee('--dt-primary: #ff0000;', false);
});

it('lets the primary color be aliased to a var() reference for design-system integration', function () {
    config(['livewire-datatable.theme.primary' => 'var(--brand-primary)']);

    Livewire::test(PostsTable::class)->assertSee('--dt-primary: var(--brand-primary);', false);
});

it('suppresses the injected style block when inject_theme_style is false', function () {
    config(['livewire-datatable.inject_theme_style' => false]);

    // Every filter/pagination view also references var(--dt-primary, ...)
    // as a Tailwind arbitrary-value fallback, so asserting against that
    // substring alone would always match; ".dt-root {" is unique to the
    // injected <style> rule itself.
    Livewire::test(PostsTable::class)->assertDontSee('.dt-root {', false);
});

it('falls back to the documented default colors when the theme config is empty', function () {
    config(['livewire-datatable.theme' => []]);

    Livewire::test(PostsTable::class)->assertSee('--dt-primary: #4f46e5;', false);
});
