<?php

namespace Mansoor\UnsplashPicker\Tests\Support;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;
use Mansoor\UnsplashPicker\Enums\ImageSize;

class TestFormWithSizeComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public string $imageSizeName = 'Regular';

    public function mount(string $imageSizeName = 'Regular'): void
    {
        $this->imageSizeName = $imageSizeName;
        $this->form->fill();
    }

    protected function resolveImageSize(): ImageSize
    {
        return constant(ImageSize::class . '::' . $this->imageSizeName);
    }

    public function form(Schema $schema): Schema
    {
        $action = TestUnsplashPickerAction::make();

        match ($this->resolveImageSize()) {
            ImageSize::Raw => $action->raw(),
            ImageSize::Full => $action->full(),
            ImageSize::Regular => $action->regular(),
            ImageSize::Small => $action->small(),
            ImageSize::Thumbnail => $action->thumbnail(),
        };

        return $schema
            ->components([
                FileUpload::make('photo')
                    ->image()
                    ->hintAction($action),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return <<<'BLADE'
        <div>
            {{ $this->form }}
        </div>
        BLADE;
    }
}
