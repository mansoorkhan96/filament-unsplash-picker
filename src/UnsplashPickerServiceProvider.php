<?php

namespace Mansoor\UnsplashPicker;

use BladeUI\Icons\Factory;
use Livewire\Livewire;
use Mansoor\UnsplashPicker\Livewire\UnsplashPickerComponent;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UnsplashPickerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'unsplash-picker';

    public static string $viewNamespace = 'unsplash-picker';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered()
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('up', [
                'path' => __DIR__.'/../resources/icons',
                'prefix' => 'up',
            ]);
        });
    }

    public function packageBooted(): void
    {
        Livewire::component('unsplash-picker-component', UnsplashPickerComponent::class);
    }
}
