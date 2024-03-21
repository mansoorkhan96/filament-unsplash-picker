<?php

namespace Mansoor\UnsplashPicker;

use BladeUI\Icons\Factory;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Mansoor\UnsplashPicker\Components\UnsplashPickerComponent;
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
            ->hasConfigFile()
            ->hasTranslations()
            ->hasAssets()
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

        Livewire::component('unsplash-picker-component', UnsplashPickerComponent::class);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make(static::$name, __DIR__.'/../resources/dist/unsplash-picker.css')->loadedOnRequest(),
        ], 'mansoor/'.static::$name);
    }
}
