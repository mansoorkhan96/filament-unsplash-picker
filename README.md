# Unsplash Picker for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mansoor/filament-unsplash-picker.svg?style=flat-square)](https://packagist.org/packages/mansoor/filament-unsplash-picker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mansoor/filament-unsplash-picker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mansoor/filament-unsplash-picker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mansoor/filament-unsplash-picker/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mansoor/filament-unsplash-picker/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mansoor/filament-unsplash-picker.svg?style=flat-square)](https://packagist.org/packages/mansoor/filament-unsplash-picker)

Unsplash gallery for Filament. Search and pick any image from Unsplash.com, specify which size to use.

![](./screenshot.jpg)

## Installation

You can install the package via composer:

```bash
composer require mansoor/filament-unsplash-picker
```

Add your Unsplash Client ID to your `.env` file

```env
UNSPLASH_CLIENT_ID=your-unsplash-client-id-goes-here
```

**Note: Package includes a queueable job to clear/delete unused files after 24 hours. Make sure to set your queue connection to any async driver**

```php
QUEUE_CONNECTION=database
```

## Usage

Just add the `UnsplashPickerAction` to your FileUpload Field.

```php
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

Forms\Components\FileUpload::make('featured_image')
    ->image()
    ->hintAction(UnsplashPickerAction::make())
```

## Specifying Disk and Directory

If you have specified the directory and disk to your FileUpload field, it will respect the configuration and upload to the correct path.

```php
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

Forms\Components\FileUpload::make('featured_image')
    ->image()
    ->disk('public')
    ->directory('posts/featured-images')
    ->hintAction(UnsplashPickerAction::make())
```

## Specifying Image Size

You can specify which image size to use.

```php
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

Forms\Components\FileUpload::make('featured_image')
    ->image()
    ->hintAction(UnsplashPickerAction::make()->regular())
```

**Available sizes:**

- `->raw()`
- `->full()`
- `->regular()`
- `->small()`
- `->thumbnail()`

# Specifying Per Page

You can specify how many image results should show on a single page.

Update per_page option in `.env`

```php
'per_page' => env('UNSPLASH_PICKER_PER_PAGE', 20),
```

You can also set different per page option for each `UnsplashPickerAction` by appending `->perPage()` method

```php
Forms\Components\FileUpload::make('featured_image')
    ->image()
    ->hintAction(
        UnsplashPickerAction::make()
            ->thumbnail()
            ->perPage(20)
    )
```

# Enable/Disable Square Mode

You can choose to dispaly images in square which uses `aspect-square` class from Tailwind CSS or disable it to display images in original height.

Update use_square_display option in `.env` to apply this setting globally.

```php
'use_square_display' => env('UNSPLASH_PICKER_USE_SQUARE_DISPLAY', true),
```

Or, You can disable this setting for each `UnsplashPickerAction` by appending `->useSquareDisplay(false)` method

```php
Forms\Components\FileUpload::make('featured_image')
    ->image()
    ->hintAction(
        UnsplashPickerAction::make()
            ->thumbnail()
            ->perPage(20)
            ->useSquareDisplay(false)
    )
```

## Customization

The `UnsplashPickerAction` is simple Filament Form Action and you append all the available methods. The Image picker component is a livewire component, you can extend and override the methods.

Here is a very cool example that shows how to get other data for selected unsplash image:

```php
use Filament\Forms\Components\Actions\Action;
use Livewire\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Set;
use Illuminate\Support\Arr;

UnsplashPickerAction::make()
    ->regular()
    ->action(function (Action $action, $arguments, Component $livewire, FileUpload $component, Set $set) {
        $action->uploadImage($arguments, $livewire, $component);

        $creatorName = Arr::get($arguments, 'user.name');

        $altText = Arr::get($arguments, 'alt_description');
        $creditText = "Photo by {$creatorName}, from unsplash.com";
        $creditUrl = Arr::get($arguments, 'user.links.html');

        $set('featured_image_alt', $altText);
        $set('featured_image_credit.text', $creditText);
        $set('featured_image_credit.url', $creditUrl);
    })
```

You dont need to but you can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-unsplash-picker-config"
```

This is the contents of the published config file:

```php
return [
    'unsplash_client_id' => env('UNSPLASH_CLIENT_ID'),
    'per_page' => env('UNSPLASH_PICKER_PER_PAGE', 20),
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-unsplash-picker-views"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mansoor Ahmed](https://github.com/mansoorkhan96)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
