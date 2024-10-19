<?php

namespace Mansoor\UnsplashPicker;

use BladeUI\Icons\Factory;
use Filament\Forms\Components\BaseFileUpload;
use Livewire\Livewire;
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;
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
                'path' => __DIR__ . '/../resources/icons',
                'prefix' => 'up',
            ]);
        });
    }

    public function packageBooted(): void
    {
        BaseFileUpload::configureUsing(function (BaseFileUpload $component) {
            $component->extraAlpineAttributes(UnsplashPickerAction::getExtraAlpineAttributes(...));
        });

        Livewire::component('unsplash-picker-component', UnsplashPickerComponent::class);
    }
}
