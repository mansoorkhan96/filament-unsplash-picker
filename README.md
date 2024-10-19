# Unsplash Picker for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mansoor/filament-unsplash-picker.svg?style=flat-square)](https://packagist.org/packages/mansoor/filament-unsplash-picker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mansoor/filament-unsplash-picker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mansoor/filament-unsplash-picker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mansoor/filament-unsplash-picker/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mansoor/filament-unsplash-picker/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mansoor/filament-unsplash-picker.svg?style=flat-square)](https://packagist.org/packages/mansoor/filament-unsplash-picker)

Unsplash gallery for Filament. Search and pick any image from Unsplash.com, specify which size to use.

![](./screenshot.jpg)

## Installation

You can install the plugin via composer:

```bash
composer require mansoor/filament-unsplash-picker
```

Add Unsplash Client ID to `config/services.php`

```php
'unsplash' => [
    'client_id' => env('UNSPLASH_CLIENT_ID'),
],
```

Integrate plugin Tailwind CSS by [creating a custom Filament theme](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme). After you have created your custom theme, add Unsplash Picker views to your new theme's `tailwind.config.js` file located in `resources/css/filament/admin/tailwind.config.js`:

```js
content: [
    ...
    './vendor/mansoor/filament-unsplash-picker/resources/views/**/*.blade.php',
],
```

## Usage

Just add the `UnsplashPickerAction` to your FileUpload Field's action.

```php
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

Forms\Components\FileUpload::make('featured_image')
    ->image()
    ->hintAction(
        UnsplashPickerAction::make()
    )
```

This plugin also supports all the features for [Spatie Media Libaray Plugin](https://filamentphp.com/plugins/filament-spatie-media-library)

```php
SpatieMediaLibraryFileUpload::make('featured_image')
    ->image()
    ->hintAction(
        UnsplashPickerAction::make()
    )
```

## Specifying Image Size

You can specify which image size to use.

```php
UnsplashPickerAction::make()
    ->regular()
```

**Available sizes:**

-   `->raw()`
-   `->full()`
-   `->regular()`
-   `->small()`
-   `->thumbnail()`

## Choose multiple photos

If you add `->multiple()` to your FileUpload field, the plugin will allow you to pick multiple images. The plugin respects the validation so you will only be able to pick max files set by the FileUpload field.

```php
FileUpload::make('featured_image')
    ->multiple() // This will indicate the plugin to allow the user to pick multiple files
    ->hintAction(
        UnsplashPickerAction::make()
    )
```

## Specifying Per Page

You may specify how many photos to show per page by appending `->perPage()` method.

```php
UnsplashPickerAction::make()
    ->perPage(20)
```

## Enable/Disable Square Mode

You can choose to dispaly images in square which uses `aspect-square` class from Tailwind CSS or disable it to display images in original height.

```php
UnsplashPickerAction::make()
    ->useSquareDisplay(false)
```

### Default search

You may set the default search input.

```php
UnsplashPickerAction::make()
    ->defaultSearch('Hello world')
```

You can also pass a custom closure to get search input from a field and return the search string.

```php
UnsplashPickerAction::make()
    ->defaultSearch(fn (Get $get) => $get('title'))
```

### Hooks

Similar to core Filament, Unsplash picker provides two hooks `beforeUpload` and `afterUpload` to let you use Unsplash data.

```php
UnsplashPickerAction::make()
    ->afterUpload(function (array $data) {
        dd($data);
    })
```

## Customization

The `UnsplashPickerAction` is simple Filament Form Action and you may override all the available methods. The Image picker component is a Livewire component, which is easy to extend.

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-unsplash-picker-views"
```

## Upgrade to 1.x

This plugin is re-written but it is very small and simple, so upgrade is very easy. If you follow the docs from top to bottom, you should be good to use the latest version.

-   This plugin no longer ships with config file. Hence per_page, use_square_display are no longer supported. You may use `Action::configureUsing()` in service provider to achieve the same. You may also delete the config file
-   `unsplash_client_id` has been removed to from plugin config file. You may add it to `config/services.php`. Please check Installation section.
-   Latest version of plugin requires to add a custom theme. Please check Installation section.
-   The need for using queueable job to clear/delete un-used media is now removed. So you may use any queue connection desired.
-   The language file has been renamed and the structure has changed very much.

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

-   [Mansoor Ahmed](https://github.com/mansoorkhan96)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
