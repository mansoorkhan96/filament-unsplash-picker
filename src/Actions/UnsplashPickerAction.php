<?php

namespace Mansoor\UnsplashPicker\Actions;

use Closure;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Get;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Mansoor\UnsplashPicker\Actions\Concerns\HasImageSizes;
use Mansoor\UnsplashPicker\Actions\Concerns\HasUploadLifecycleHooks;
use Mansoor\UnsplashPicker\Forms\Components\UnsplashPickerField;
use Mansoor\UnsplashPicker\Livewire\UnsplashPickerComponent;

class UnsplashPickerAction extends Action
{
    use HasImageSizes;
    use HasUploadLifecycleHooks;

    protected int $perPage = 20;

    protected bool $useSquareDisplay = true;

    protected string|Closure $search = '';

    public static function getDefaultName(): ?string
    {
        return 'unsplash_picker_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('unsplash-picker::unsplash-picker-action.label'));

        $this->icon('up-unsplash');

        $this->disabled(function (FileUpload $component) {
            if ($component->isMultiple()) {
                return count($component->getUploadedFiles()) === $component->getMaxFiles();
            }

            return false;
        });

        $this->modalWidth(fn (): ?MaxWidth => MaxWidth::ScreenLarge);

        $this->modalDescription(function (FileUpload $component) {
            if (! $component->isMultiple()) {
                return;
            }

            $numberOfSelectableImages = $component->getMaxFiles() - count($component->getState());

            return trans_choice(
                'unsplash-picker::unsplash-picker-action.description',
                $numberOfSelectableImages,
                ['numberOfSelectableImages' => $numberOfSelectableImages]
            );
        });

        $this->form(function (FileUpload $component, Get $get) {
            return [
                Livewire::make(UnsplashPickerComponent::class, [
                    'search' => $this->getDefaultSearch(),
                    'perPage' => $this->getPerPage(),
                    'useSquareDisplay' => $this->shouldUseSquareDisplay(),
                    'isMultiple' => $component->isMultiple(),
                    'numberOfSelectableImages' => $component->isMultiple()
                        ? $component->getMaxFiles() - count($component->getState())
                        : 1,
                ])->key($component->getKey().'actions.form.unplash_picker'),

                UnsplashPickerField::make('selectedImages'),
            ];
        });

        $this->action(function (array $data, Component $livewire) {
            $this->evaluate($this->beforeUpload);

            $this->uploadImage($data, $livewire);

            $this->evaluate($this->afterUpload);
        });
    }

    public function uploadImage(array $data, Component $livewire)
    {
        foreach ($data['selectedImages'] ?? [] as $image) {
            $downloadLink = Arr::get($image, $this->getImageSize()->getPath());

            $filePath = self::createTemporaryUploadedFileFromUrl($downloadLink);

            $livewire->dispatch('add-file', $filePath);
        }
    }

    public static function createTemporaryUploadedFileFromUrl(string $url)
    {
        if (! $stream = @fopen($url, 'r')) {
            throw new Exception('Can\'t open file from url '.$url);
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'url-file-');

        file_put_contents($tempFilePath, $stream);

        $mimeType = mime_content_type($tempFilePath);

        $tempFile = (new UploadedFile($tempFilePath, basename($url, $mimeType)))
            ->store(FileUploadConfiguration::directory(), ['disk' => FileUploadConfiguration::disk()]);

        $filePath = explode('/', $tempFile)[1];

        $file = TemporaryUploadedFile::createFromLivewire($filePath);

        return $file->temporaryUrl();
    }

    public static function getExtraAlpineAttributes(FileUpload $component): array
    {
        $isMultiple = $component->isMultiple() ? 'true' : 'false';

        return [
            'x-on:add-file.window' => '
                const pond = FilePond.find($el.querySelector(".filepond--root"));
                const isMultiple = '.$isMultiple.'

                if (! isMultiple) {
                    pond.removeFiles({ revert: true });

                    // wait until filepond removes the file
                    setTimeout(() => pond.addFile($event.detail), 500)
                } else {
                    pond.addFile($event.detail)
                }
            ',
        ];
    }

    public function defaultSearch(string|Closure $search): static
    {
        $this->search = $search;

        return $this;
    }

    public function getDefaultSearch(): string
    {
        if (is_string($this->search)) {
            return $this->search ?? '';
        }

        return $this->evaluate($this->search) ?? '';
    }

    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    public function useSquareDisplay(bool $useSquareDisplay): static
    {
        $this->useSquareDisplay = $useSquareDisplay;

        return $this;
    }

    public function shouldUseSquareDisplay(): ?bool
    {
        return $this->useSquareDisplay;
    }
}
