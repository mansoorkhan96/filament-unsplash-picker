<?php

namespace Mansoor\UnsplashPicker\Actions;

use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
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

        $this->modalContent(fn () => new HtmlString(Blade::render("@livewire('unsplash-picker-component', {$this->getOptions()})")));

        $this->action($this->uploadImage(...));
    }

    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;

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

    protected function uploadImage($arguments, Component $livewire, FileUpload $component)
    {
        $downloadLink = Arr::get($arguments, $this->getImageSize()->getPath());

        $filePath = UrlUploadedFile::createFromUrl($downloadLink)
            ->store(FileUploadConfiguration::directory(), ['disk' => FileUploadConfiguration::disk()]);

        $filePath = explode('/', $filePath)[1];

        $filePath = TemporaryUploadedFile::createFromLivewire($filePath);

        $component->state([$filePath]);
        $component->saveUploadedFiles();

        $filePath = Arr::first($component->getState());

        dispatch(new CleanupUnusedUploadedFile(
            model: $livewire->getModel(),
            column: $component->getStatePath(false),
            filePath: $filePath,
            diskName: $component->getDiskName()
        ))->delay(now()->addDay());
    }

    protected function getImageSize(): ImageSize
    {
        return $this->imageSize ?? ImageSize::Regular;
    }

    protected function getPerPage(): ?int
    {
        return $this->perPage;
    }

    protected function getOptions()
    {
        if (! $this->getPerPage()) {
            return;
        }

        return "['perPage' => {$this->getPerPage()}]";
    }
}
