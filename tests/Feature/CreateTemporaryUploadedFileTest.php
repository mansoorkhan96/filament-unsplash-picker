<?php

use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

beforeEach(function () {
    // Ensure the Livewire temp storage disk is initialized
    FileUploadConfiguration::storage();
});

it('returns a temporary url string from a valid image url', function () {
    $tempImage = createTempPng();

    $result = UnsplashPickerAction::createTemporaryUploadedFileFromUrl($tempImage);

    expect($result)->toBeString()
        ->and($result)->toContain('/livewire/preview-file/');

    @unlink($tempImage);
});

it('throws an exception for an unreachable url', function () {
    UnsplashPickerAction::createTemporaryUploadedFileFromUrl('file:///nonexistent/path/image.png');
})->throws(Exception::class, "Can't open file from url");

it('stores the file in livewire temp upload directory', function () {
    $tempImage = createTempPng();

    UnsplashPickerAction::createTemporaryUploadedFileFromUrl($tempImage);

    $storage = FileUploadConfiguration::storage();
    $directory = FileUploadConfiguration::directory();
    $files = $storage->files($directory);

    expect($files)->not->toBeEmpty();

    @unlink($tempImage);
});

it('handles jpeg image with correct mime type', function () {
    $tempImage = createTempJpeg();

    $result = UnsplashPickerAction::createTemporaryUploadedFileFromUrl($tempImage);

    expect($result)->toBeString()
        ->and($result)->toContain('/livewire/preview-file/');

    @unlink($tempImage);
});

it('returns a signed url that includes a signature parameter', function () {
    $tempImage = createTempPng();

    $result = UnsplashPickerAction::createTemporaryUploadedFileFromUrl($tempImage);

    expect($result)->toContain('signature=');

    @unlink($tempImage);
});

// Helper functions

function createTempPng(): string
{
    $tempFile = tempnam(sys_get_temp_dir(), 'test-img-') . '.png';

    // Create a minimal 1x1 PNG
    $img = imagecreatetruecolor(1, 1);
    imagepng($img, $tempFile);
    imagedestroy($img);

    return $tempFile;
}

function createTempJpeg(): string
{
    $tempFile = tempnam(sys_get_temp_dir(), 'test-img-') . '.jpg';

    // Create a minimal 1x1 JPEG
    $img = imagecreatetruecolor(1, 1);
    imagejpeg($img, $tempFile);
    imagedestroy($img);

    return $tempFile;
}
