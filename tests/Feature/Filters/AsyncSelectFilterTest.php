<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Filters\AsyncSelectFilter;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'draft', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Beta', 'status' => 'published', 'views' => 25, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

function statusAsyncFilter(): AsyncSelectFilter
{
    return AsyncSelectFilter::make('Status', 'status')
        ->optionsUsing(fn (string $term) => collect(['draft' => 'Draft', 'published' => 'Published'])
            ->filter(fn (string $label) => $term === '' || str_contains(strtolower($label), strtolower($term)))
            ->all())
        ->labelUsing(fn (string $value) => match ($value) {
            'draft' => 'Draft',
            'published' => 'Published',
            default => null,
        });
}

it('shows a placeholder and every option when no search term has been typed and nothing is selected', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })->html();

    expect($html)->toContain('Select an option...')
        ->and($html)->toContain('Draft')
        ->and($html)->toContain('Published');
});

it('narrows the option list to what the resolver returns for the current search term', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })
        ->set('filterSearchTerms.status', 'dra')
        ->html();

    expect($html)->toContain('Draft')
        ->and($html)->not->toContain('Published');
});

it('resolves the selected value to its label via labelUsing() instead of showing the raw stored value', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })
        ->set('filterValues.status', 'draft')
        ->html();

    expect($html)->toContain('Draft')
        ->and($html)->not->toContain('Select an option...');
});

it('actually filters the table rows once a value is selected, same as SelectFilter', function () {
    $test = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })->set('filterValues.status', 'published');

    expect($test->instance()->rows->total())->toBe(1);
});

it('offers a clear option only once a value is selected', function () {
    $withoutValue = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })->html();

    $withValue = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })->set('filterValues.status', 'draft')->html();

    expect($withoutValue)->not->toContain('Clear selection')
        ->and($withValue)->toContain('Clear selection');
});

it('safely embeds an option value containing a single quote in wire:click instead of breaking out of it', function () {
    // optionsUsing() is developer-supplied and may key its options by
    // free-text values (a category name, not always a safe int/UUID id) —
    // raw string interpolation into wire:click would let a value like
    // this one break out of the Livewire action-call syntax entirely.
    $html = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [
                AsyncSelectFilter::make('Status', 'status')
                    ->optionsUsing(fn (string $term) => ["o'brien" => "O'Brien"]),
            ];
        }
    })->html();

    expect($html)->toContain('\u0027')
        ->and($html)->not->toContain("'o'brien'");
});

it('clears both filterValues and filterSearchTerms on resetFilters()', function () {
    $test = Livewire::test(new class extends PostsTable
    {
        public function filters(): array
        {
            return [statusAsyncFilter()];
        }
    })
        ->set('filterValues.status', 'draft')
        ->set('filterSearchTerms.status', 'dra')
        ->call('resetFilters');

    expect($test->get('filterValues'))->toBe([])
        ->and($test->get('filterSearchTerms'))->toBe([]);
});
