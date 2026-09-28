<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Column;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now(),
    ]);
});

/**
 * Livewire re-instantiates the component without constructor arguments: the
 * fixture reads its setup from static properties instead.
 */
class BadgesPostsTable extends PostsTable
{
    public static array $tags = [];

    public static int $visible = 2;

    public static ?Closure $label = null;

    public function columns(): array
    {
        return [
            Column::make('Title', 'title'),
            Column::make('Tags', 'status')->format(fn () => self::$tags)->badges(self::$visible, self::$label),
        ];
    }
}

function badgesTable(array $tags, int $visible = 2, ?Closure $label = null): string
{
    BadgesPostsTable::$tags = $tags;
    BadgesPostsTable::$visible = $visible;
    BadgesPostsTable::$label = $label;

    return BadgesPostsTable::class;
}

it('shows the first badges and a "+N" trigger whose popover holds the rest', function () {
    $html = Livewire::test(badgesTable(['A1', 'B2', 'C3', 'D4', 'E5']))->html();

    expect($html)
        ->toContain('>A1</span>')
        ->toContain('>B2</span>')
        ->toContain('+3</button>')
        ->toContain('x-teleport="body"')
        ->toContain('>E5</span>')
        ->toContain('aria-label="3 more: C3, D4, E5"');
});

it('shows every badge when only one would be hidden', function () {
    $html = Livewire::test(badgesTable(['A1', 'B2', 'C3']))->html();

    expect($html)->toContain('>C3</span>')->not->toContain('x-teleport');
});

it('respects a custom visible count and a label resolver', function () {
    $html = Livewire::test(badgesTable([['name' => 'x'], ['name' => 'y'], ['name' => 'z'], ['name' => 'w']], 1, fn (array $item) => strtoupper($item['name'])))->html();

    expect($html)->toContain('>X</span>')->toContain('+3</button>')->toContain('>W</span>');
});

it('escapes badge labels', function () {
    expect(Livewire::test(badgesTable(['<b>bold</b>']))->html())
        ->toContain('&lt;b&gt;bold&lt;/b&gt;')
        ->not->toContain('<b>bold</b>');
});

it('exports every label, not only the visible ones', function () {
    $column = Column::make('Tags', 'tags')->badges();

    expect($column->isBadges())->toBeTrue()
        ->and($column->exportValue(['A1', 'B2', 'C3', 'D4'], null))->toBe('A1, B2, C3, D4')
        ->and($column->badgeLabels(null, null))->toBe([])
        ->and(Column::make('Plain', 'plain')->exportValue('x', null))->toBe('x');
});

it('lets the host app restyle the badges through config', function () {
    config(['livewire-datatable.classes.badge_variants' => ['gray' => 'my-badge'], 'livewire-datatable.classes.badge_more' => 'my-more']);

    expect(Livewire::test(badgesTable(['A1', 'B2', 'C3', 'D4']))->html())
        ->toContain('class="my-badge"')
        ->toContain('class="my-more"');
});
