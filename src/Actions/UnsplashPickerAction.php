<?php

namespace Mansoor\UnsplashPicker\Actions;

use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mansoor\UnsplashPicker\Enums\ImageSize;
use Mansoor\UnsplashPicker\Helpers\UrlUploadedFile;
use Mansoor\UnsplashPicker\Jobs\CleanupUnusedUploadedFile;

class UnsplashPickerAction extends Action
{
    protected ?ImageSize $imageSize = null;

    protected ?int $perPage = null;

    protected ?bool $useSquareDisplay = null;

    public static function getDefaultName(): ?string
    {
        return 'unsplash_picker';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('unsplash-picker::unsplash-picker.pick_from_unsplash'));

        $this->icon('up-unsplash');

        $this->modalSubmitAction(fn (StaticAction $action) => $action->hidden());

        $this->modalWidth(fn (MountableAction $action): ?MaxWidth => MaxWidth::ScreenLarge);

        $this->modalContent(fn () => view('unsplash-picker::unsplash-picker', ['options' => $this->getOptions()]));

        $this->action($this->uploadImage(...));
    }

    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function useSquareDisplay(bool $useSquareDisplay): static
    {
        $this->useSquareDisplay = $useSquareDisplay;

        return $this;
    }

    public function raw(): static
    {
        $this->imageSize = ImageSize::Raw;

        return $this;
    }

    public function full(): static
    {
        $this->imageSize = ImageSize::Full;

        return $this;
    }

    public function regular(): static
    {
        $this->imageSize = ImageSize::Regular;

        return $this;
    }

    public function small(): static
    {
        $this->imageSize = ImageSize::Small;

        return $this;
    }

    public function thumbnail(): static
    {
        $this->imageSize = ImageSize::Thumbnail;

        return $this;
    }

    public function imageSize(ImageSize $imageSize): static
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function uploadImage($arguments, Component $livewire, FileUpload $component)
    {
        $downloadLink = Arr::get($arguments, $this->getImageSize()->getPath());

        $filePath = UrlUploadedFile::createFromUrl($downloadLink)
            ->store(FileUploadConfiguration::directory(), ['disk' => FileUploadConfiguration::disk()]);

        $filePath = explode('/', $filePath)[1];

        $filePath = TemporaryUploadedFile::createFromLivewire($filePath);

        $component->state([$filePath]);
        $component->saveUploadedFiles();

        $filePath = Arr::first($component->getState());

        if (env('QUEUE_CONNECTION') !== 'sync') {
            dispatch(new CleanupUnusedUploadedFile(
                model: $livewire->getModel(),
                column: $component->getStatePath(false),
                filePath: $filePath,
                diskName: $component->getDiskName()
            ))->delay(now()->addDay());
        }
    }

    public function getImageSize(): ImageSize
    {
        return $this->imageSize ?? ImageSize::Regular;
    }

    public function getPerPage(): ?int
    {
        return $this->perPage ?? config('unsplash-picker.per_page');
    }

    public function shouldUseSquareDisplay(): ?bool
    {
        return $this->useSquareDisplay ?? config('unsplash-picker.use_square_display');
    }

    public function getOptions(): array
    {
        return [
            'perPage' => $this->getPerPage(),
            'useSquareDisplay' => $this->shouldUseSquareDisplay(),
        ];
    }
}
