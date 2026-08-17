<?php

declare(strict_types=1);

namespace Salioudiabate\LivewireDatatable;

use Illuminate\Pagination\Paginator;
use Salioudiabate\LivewireDatatable\Console\Commands\MakeDataTableCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireDataTableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-datatable')
            ->hasConfigFile('livewire-datatable')
            ->hasViews('livewire-datatable')
            ->hasTranslations()
            ->hasCommand(MakeDataTableCommand::class);
    }

    public function packageBooted(): void
    {
        if (config('livewire-datatable.register_pagination_view', true)) {
            Paginator::defaultView('livewire-datatable::vendor.livewire.tailwind');
            Paginator::defaultSimpleView('livewire-datatable::vendor.livewire.simple-tailwind');
        }
    }
}
