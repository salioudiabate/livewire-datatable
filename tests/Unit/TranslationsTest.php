<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;

it('keeps every locale in exact key parity with the canonical English file', function () {
    $en = array_keys(require __DIR__.'/../../resources/lang/en/livewire-datatable.php');
    $fr = array_keys(require __DIR__.'/../../resources/lang/fr/livewire-datatable.php');

    sort($en);
    sort($fr);

    expect($fr)->toBe($en);
});

it('has no empty translation values in either locale', function () {
    $en = require __DIR__.'/../../resources/lang/en/livewire-datatable.php';
    $fr = require __DIR__.'/../../resources/lang/fr/livewire-datatable.php';

    foreach (['en' => $en, 'fr' => $fr] as $locale => $lines) {
        foreach ($lines as $key => $value) {
            expect(trim((string) $value))->not->toBe('', "Empty translation for [{$locale}::{$key}]");
        }
    }
});

it('renders the French locale when the app locale is set to fr', function () {
    app()->setLocale('fr');

    DB::table('dt_test_posts')->insert([
        'title' => 'Alpha',
        'status' => 'published',
        'views' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::test(PostsTable::class)
        ->assertSee('Rechercher...')
        ->assertSee('Filtres');
});
