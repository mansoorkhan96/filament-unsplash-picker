<?php

use Mansoor\UnsplashPicker\Enums\ImageSize;

it('has all expected cases', function () {
    $cases = ImageSize::cases();

    expect($cases)->toHaveCount(5)
        ->and(array_map(fn ($case) => $case->name, $cases))->toBe([
            'Raw', 'Full', 'Regular', 'Small', 'Thumbnail',
        ]);
});

it('returns correct path for raw', function () {
    expect(ImageSize::Raw->getPath())->toBe('urls.raw');
});

it('returns correct path for full', function () {
    expect(ImageSize::Full->getPath())->toBe('urls.full');
});

it('returns correct path for regular', function () {
    expect(ImageSize::Regular->getPath())->toBe('urls.regular');
});

it('returns correct path for small', function () {
    expect(ImageSize::Small->getPath())->toBe('urls.small');
});

it('returns correct path for thumbnail', function () {
    expect(ImageSize::Thumbnail->getPath())->toBe('urls.thumb');
});

it('can extract url from photo data using path', function () {
    $photo = [
        'urls' => [
            'raw' => 'https://images.unsplash.com/photo?raw',
            'full' => 'https://images.unsplash.com/photo?full',
            'regular' => 'https://images.unsplash.com/photo?regular',
            'small' => 'https://images.unsplash.com/photo?small',
            'thumb' => 'https://images.unsplash.com/photo?thumb',
        ],
    ];

    expect(data_get($photo, ImageSize::Raw->getPath()))->toBe('https://images.unsplash.com/photo?raw')
        ->and(data_get($photo, ImageSize::Full->getPath()))->toBe('https://images.unsplash.com/photo?full')
        ->and(data_get($photo, ImageSize::Regular->getPath()))->toBe('https://images.unsplash.com/photo?regular')
        ->and(data_get($photo, ImageSize::Small->getPath()))->toBe('https://images.unsplash.com/photo?small')
        ->and(data_get($photo, ImageSize::Thumbnail->getPath()))->toBe('https://images.unsplash.com/photo?thumb');
});
