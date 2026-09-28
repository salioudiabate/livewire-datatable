<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\Support\Badge;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\ToolbarAction;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Beta', 'status' => 'draft', 'views' => 0, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

/**
 * "views > 0" stands in for a boolean column; the toggle is enabled on published rows only.
 */
class TogglePostsTable extends PostsTable
{
    /** @var list<string> */
    public static array $toggled = [];

    public function columns(): array
    {
        return [
            Column::make('Title', 'title'),
            Column::make('Status', 'status')->badge(fn (string $status) => $status === 'published' ? 'success' : 'warning'),
            Column::make('Views', 'views')
                ->format(fn (int $views) => $views > 0)
                ->toggle('flip', fn ($row) => $row->status === 'published', 'Visible'),
        ];
    }

    public function toolbarActions(): array
    {
        return [ToolbarAction::make('New post')->action('create')->primary()];
    }

    public function flip(string $key): void
    {
        self::$toggled[] = $key;
    }

    public function create(): void {}
}

it('renders a badge per row with a variant chosen from the value', function () {
    expect(Livewire::test(TogglePostsTable::class)->html())
        ->toContain('text-emerald-700">published</span>')
        ->toContain('text-amber-700">draft</span>');
});

it('builds an escaped badge for composed cells, restylable per variant', function () {
    expect((string) Badge::html('<i>x</i>', 'danger'))->toContain('text-red-700')->toContain('&lt;i&gt;x&lt;/i&gt;');

    config(['livewire-datatable.classes.badge_variants' => ['danger' => 'badge badge-danger']]);

    expect((string) Badge::html('Oops', 'danger'))->toBe('<span class="badge badge-danger">Oops</span>'.PHP_EOL)
        ->and((string) Badge::html('Other', 'unknown'))->toContain('bg-slate-100');
});

it('renders the switch clickable only on rows its $enabled allows', function () {
    $html = Livewire::test(TogglePostsTable::class)->html();

    expect($html)
        ->toContain('aria-checked="true"')
        ->toContain('aria-label="Visible"')
        ->toContain("runColumnToggle('views', '1')")
        ->not->toContain("runColumnToggle('views', '2')");
});

it('calls the toggle action through runColumnToggle, re-checking column and row server-side', function () {
    TogglePostsTable::$toggled = [];
    $test = Livewire::test(TogglePostsTable::class);

    $test->call('runColumnToggle', 'views', '1');
    expect(TogglePostsTable::$toggled)->toBe(['1']);

    // Disabled row, unknown row, and a field that is not a toggle column are all refused.
    Livewire::test(TogglePostsTable::class)->call('runColumnToggle', 'views', '2')->assertForbidden();
    Livewire::test(TogglePostsTable::class)->call('runColumnToggle', 'views', '999')->assertForbidden();
    Livewire::test(TogglePostsTable::class)->call('runColumnToggle', 'title', '1')->assertForbidden();

    expect(TogglePostsTable::$toggled)->toBe(['1']);
});

it('styles a primary toolbar action with the theme color', function () {
    expect(Livewire::test(TogglePostsTable::class)->html())->toContain('bg-[var(--dt-primary,#4f46e5)] px-3 py-2 text-sm font-medium');
});

it('hides the density toggle from config and refreshes on the configured event', function () {
    config(['livewire-datatable.density_toggle' => false, 'livewire-datatable.refresh_event' => 'refresh-table']);

    $test = Livewire::test(TogglePostsTable::class);

    expect($test->instance()->showDensityToggle())->toBeFalse();

    $test->dispatch('refresh-table')->assertOk();
});
