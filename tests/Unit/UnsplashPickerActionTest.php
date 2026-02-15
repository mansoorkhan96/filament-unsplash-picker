<?php

use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;
use Mansoor\UnsplashPicker\Enums\ImageSize;

it('has the correct default name', function () {
    expect(UnsplashPickerAction::getDefaultName())->toBe('unsplash_picker_action');
});

it('has default per page of 20', function () {
    $action = UnsplashPickerAction::make();

    expect($action->getPerPage())->toBe(20);
});

it('can set per page', function () {
    $action = UnsplashPickerAction::make()->perPage(10);

    expect($action->getPerPage())->toBe(10);
});

it('has square display enabled by default', function () {
    $action = UnsplashPickerAction::make();

    expect($action->shouldUseSquareDisplay())->toBeTrue();
});

it('can disable square display', function () {
    $action = UnsplashPickerAction::make()->useSquareDisplay(false);

    expect($action->shouldUseSquareDisplay())->toBeFalse();
});

it('has empty default search', function () {
    $action = UnsplashPickerAction::make();

    expect($action->getDefaultSearch())->toBe('');
});

it('can set default search as string', function () {
    $action = UnsplashPickerAction::make()->defaultSearch('nature');

    expect($action->getDefaultSearch())->toBe('nature');
});

it('can set default search as closure', function () {
    $action = UnsplashPickerAction::make()->defaultSearch(fn () => 'mountains');

    expect($action->getDefaultSearch())->toBe('mountains');
});

it('defaults to regular image size', function () {
    $action = UnsplashPickerAction::make();

    expect($action->getImageSize())->toBe(ImageSize::Regular);
});

it('can set image size to raw', function () {
    $action = UnsplashPickerAction::make()->raw();

    expect($action->getImageSize())->toBe(ImageSize::Raw);
});

it('can set image size to full', function () {
    $action = UnsplashPickerAction::make()->full();

    expect($action->getImageSize())->toBe(ImageSize::Full);
});

it('can set image size to regular', function () {
    $action = UnsplashPickerAction::make()->regular();

    expect($action->getImageSize())->toBe(ImageSize::Regular);
});

it('can set image size to small', function () {
    $action = UnsplashPickerAction::make()->small();

    expect($action->getImageSize())->toBe(ImageSize::Small);
});

it('can set image size to thumbnail', function () {
    $action = UnsplashPickerAction::make()->thumbnail();

    expect($action->getImageSize())->toBe(ImageSize::Thumbnail);
});

it('can set image size using enum', function () {
    $action = UnsplashPickerAction::make()->imageSize(ImageSize::Full);

    expect($action->getImageSize())->toBe(ImageSize::Full);
});

it('can chain image size methods fluently', function () {
    $action = UnsplashPickerAction::make()
        ->raw()
        ->full()
        ->small()
        ->regular();

    expect($action->getImageSize())->toBe(ImageSize::Regular);
});

it('can set before upload hook', function () {
    $called = false;
    $action = UnsplashPickerAction::make()->beforeUpload(function () use (&$called) {
        $called = true;
    });

    expect($action)->toBeInstanceOf(UnsplashPickerAction::class);
});

it('can set after upload hook', function () {
    $called = false;
    $action = UnsplashPickerAction::make()->afterUpload(function () use (&$called) {
        $called = true;
    });

    expect($action)->toBeInstanceOf(UnsplashPickerAction::class);
});

it('returns fluent interface for all configuration methods', function () {
    $action = UnsplashPickerAction::make();

    expect($action->perPage(10))->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->useSquareDisplay(false))->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->defaultSearch('test'))->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->raw())->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->full())->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->regular())->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->small())->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->thumbnail())->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->imageSize(ImageSize::Raw))->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->beforeUpload(fn () => null))->toBeInstanceOf(UnsplashPickerAction::class)
        ->and($action->afterUpload(fn () => null))->toBeInstanceOf(UnsplashPickerAction::class);
});

it('can chain all configuration methods', function () {
    $action = UnsplashPickerAction::make()
        ->perPage(15)
        ->useSquareDisplay(false)
        ->defaultSearch('landscape')
        ->full()
        ->beforeUpload(fn () => null)
        ->afterUpload(fn () => null);

    expect($action->getPerPage())->toBe(15)
        ->and($action->shouldUseSquareDisplay())->toBeFalse()
        ->and($action->getDefaultSearch())->toBe('landscape')
        ->and($action->getImageSize())->toBe(ImageSize::Full);
});
