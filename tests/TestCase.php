<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Salioudiabate\LivewireDatatable\LivewireDataTableServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireDataTableServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }

    protected function setUpDatabase(): void
    {
        Schema::create('dt_test_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('dt_test_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dt_test_author_id')->nullable();
            $table->string('title');
            $table->string('status')->default('draft');
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });

        Schema::create('dt_test_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dt_test_post_id')->constrained('dt_test_posts')->restrictOnDelete();
            $table->string('body');
        });
    }
}
