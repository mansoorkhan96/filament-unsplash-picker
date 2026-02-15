<?php

namespace Mansoor\UnsplashPicker\Tests\Support;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;
use Mansoor\UnsplashPicker\Actions\UnsplashPickerAction;

class TestSpatieFormComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('images')
                    ->image()
                    ->hintAction(UnsplashPickerAction::make()),
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
