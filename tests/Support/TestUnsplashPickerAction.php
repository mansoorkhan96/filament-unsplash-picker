<?php

namespace Mansoor\UnsplashPicker\Tests\Support;

use Illuminate\Support\Arr;
use Livewire\Component;
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

class TestUnsplashPickerAction extends UnsplashPickerAction
{
    public static array $downloadedUrls = [];

    public static array $dispatchedFiles = [];

    public static function resetTracking(): void
    {
        static::$downloadedUrls = [];
        static::$dispatchedFiles = [];
    }

    public function uploadImage(array $data, Component $livewire)
    {
        foreach ($data['selectedImages'] ?? [] as $image) {
            $downloadLink = Arr::get($image, $this->getImageSize()->getPath());

            static::$downloadedUrls[] = $downloadLink;

            $fakePath = 'https://example.com/tmp/' . uniqid() . '.jpg';
            static::$dispatchedFiles[] = $fakePath;

            $livewire->dispatch('add-file', $fakePath);
        }
    }
}
