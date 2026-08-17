<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function () {
    File::deleteDirectory(app_path('Livewire'));
});

it('generates a DataTableComponent subclass', function () {
    $this->artisan('make:datatable', ['name' => 'PostsTable'])->assertSuccessful();

    $path = app_path('Livewire/PostsTable.php');

    expect(File::exists($path))->toBeTrue();

    $contents = File::get($path);

    expect($contents)->toContain('namespace App\Livewire;')
        ->and($contents)->toContain('class PostsTable extends DataTableComponent')
        ->and($contents)->toContain('public function builder(): mixed')
        ->and($contents)->toContain('public function columns(): array')
        ->and($contents)->toContain('public function filters(): array')
        ->and($contents)->not->toContain('{{')
        ->and($contents)->not->toContain('}}');
});

it('scaffolds a typed Eloquent builder() when --model is given', function () {
    $this->artisan('make:datatable', ['name' => 'PostsTable', '--model' => 'Post'])
        ->assertSuccessful();

    $contents = File::get(app_path('Livewire/PostsTable.php'));

    expect($contents)->toContain('use App\Models\Post;')
        ->and($contents)->toContain('use Illuminate\Database\Eloquent\Builder;')
        ->and($contents)->toContain('public function builder(): Builder')
        ->and($contents)->toContain('return Post::query();')
        ->and($contents)->not->toContain('YourModel');
});

it('respects a fully-qualified --model class name', function () {
    $this->artisan('make:datatable', ['name' => 'PostsTable', '--model' => '\Domain\Blog\Models\Post'])
        ->assertSuccessful();

    $contents = File::get(app_path('Livewire/PostsTable.php'));

    expect($contents)->toContain('use Domain\Blog\Models\Post;')
        ->and($contents)->toContain('return Post::query();');
});

it('supports nested namespaces via slashes in the name', function () {
    $this->artisan('make:datatable', ['name' => 'Admin/PostsTable'])->assertSuccessful();

    $path = app_path('Livewire/Admin/PostsTable.php');

    expect(File::exists($path))->toBeTrue()
        ->and(File::get($path))->toContain('namespace App\Livewire\Admin;');
});

it('refuses to overwrite an existing file without --force', function () {
    $this->artisan('make:datatable', ['name' => 'PostsTable'])->assertSuccessful();

    $this->artisan('make:datatable', ['name' => 'PostsTable'])->assertFailed();
});

it('overwrites an existing file when --force is given', function () {
    $this->artisan('make:datatable', ['name' => 'PostsTable'])->assertSuccessful();

    $this->artisan('make:datatable', ['name' => 'PostsTable', '--model' => 'Post', '--force' => true])
        ->assertSuccessful();

    expect(File::get(app_path('Livewire/PostsTable.php')))->toContain('return Post::query();');
});
