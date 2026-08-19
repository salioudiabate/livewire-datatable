<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Components\PostsTable;
use Salioudiabate\LivewireDatatable\Tests\Fixtures\Models\Post;

beforeEach(function () {
    DB::table('dt_test_posts')->insert([
        ['title' => 'Alpha', 'status' => 'published', 'views' => 10, 'created_at' => now(), 'updated_at' => now()],
        ['title' => 'Bravo', 'status' => 'archived', 'views' => 5, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('does not make rows clickable by default', function () {
    Livewire::test(PostsTable::class)
        ->assertDontSeeHtml('cursor-pointer')
        ->assertDontSeeHtml('window.location');
});

it('makes each row navigate to rowUrl() and adds a pointer cursor', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function rowUrl(mixed $row): ?string
        {
            return "/posts/{$row->id}";
        }
    })->html();

    expect($html)
        ->toContain('/posts/1')
        ->toContain('/posts/2')
        ->toContain('cursor-pointer')
        ->toContain("window.location = '/posts/1'");
});

it('leaves a specific row non-clickable when rowUrl() returns null for it', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function rowUrl(mixed $row): ?string
        {
            return $row->status === 'archived' ? null : "/posts/{$row->id}";
        }
    })->html();

    $archivedPost = Post::query()->where('status', 'archived')->firstOrFail();

    expect($html)->toContain("window.location = '/posts/1'")
        ->not->toContain("window.location = '/posts/{$archivedPost->id}'");
});

it('guards the click handler so clicking an inner interactive element does not navigate', function () {
    $html = Livewire::test(new class extends PostsTable
    {
        public function rowUrl(mixed $row): ?string
        {
            return "/posts/{$row->id}";
        }
    })->html();

    expect($html)->toContain("\$event.target.closest('a, button, input, select, textarea, label')");
});
