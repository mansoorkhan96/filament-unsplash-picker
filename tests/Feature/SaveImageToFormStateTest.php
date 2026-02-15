<?php

use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Livewire;
use Mansoor\UnsplashPicker\Tests\Support\TestFormComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestMultipleFormComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestSpatieFormComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestSpatieMultipleFormComponent;

beforeEach(function () {
    config()->set('services.unsplash.client_id', 'test-client-id');

    $storage = FileUploadConfiguration::storage();
    $directory = FileUploadConfiguration::directory();

    foreach ($storage->files($directory) as $file) {
        $storage->delete($file);
    }
});

it('saves the image for post image', function () {
    $photos = createLocalPhotos(1);

    Livewire::test(TestFormComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ])
        ->assertDispatched('add-file');

    $files = getTemporaryUploadedFiles();

    expect($files)->toHaveCount(1);

    cleanupLocalPhotos($photos);
});

it('saves the image for post image using spatie media library', function () {
    $photos = createLocalPhotos(1);

    Livewire::test(TestSpatieFormComponent::class)
        ->callFormComponentAction('image', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ])
        ->assertDispatched('add-file');

    $files = getTemporaryUploadedFiles();

    expect($files)->toHaveCount(1);

    cleanupLocalPhotos($photos);
});

it('saves multiple images for post attachments', function () {
    $photos = createLocalPhotos(3);

    Livewire::test(TestMultipleFormComponent::class)
        ->callFormComponentAction('photos', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ])
        ->assertDispatched('add-file');

    $files = getTemporaryUploadedFiles();

    expect($files)->toHaveCount(3);

    cleanupLocalPhotos($photos);
});

it('saves multiple images for post attachments using spatie media library', function () {
    $photos = createLocalPhotos(3);

    Livewire::test(TestSpatieMultipleFormComponent::class)
        ->callFormComponentAction('attachments', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ])
        ->assertDispatched('add-file');

    $files = getTemporaryUploadedFiles();

    expect($files)->toHaveCount(3);

    cleanupLocalPhotos($photos);
});

// Helper functions

function createLocalPhotos(int $count = 1): array
{
    $photos = [];

    for ($i = 0; $i < $count; $i++) {
        $tempFile = tempnam(sys_get_temp_dir(), 'test-img-') . '.png';
        $img = imagecreatetruecolor(10, 10);
        imagepng($img, $tempFile);
        imagedestroy($img);

        $photos[] = [
            'id' => "test_{$i}",
            'urls' => [
                'raw' => $tempFile,
                'full' => $tempFile,
                'regular' => $tempFile,
                'small' => $tempFile,
                'thumb' => $tempFile,
            ],
        ];
    }

    return $photos;
}

function getTemporaryUploadedFiles(): array
{
    $storage = FileUploadConfiguration::storage();
    $directory = FileUploadConfiguration::directory();

    return $storage->files($directory);
}

function cleanupLocalPhotos(array $photos): void
{
    foreach ($photos as $photo) {
        @unlink($photo['urls']['regular']);
    }
}
