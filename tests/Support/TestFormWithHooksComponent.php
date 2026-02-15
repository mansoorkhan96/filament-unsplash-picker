<?php

namespace Mansoor\UnsplashPicker\Tests\Support;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;

class TestFormWithHooksComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public static bool $beforeUploadCalled = false;

    public static bool $afterUploadCalled = false;

    public static array $callOrder = [];

    public static function resetHookTracking(): void
    {
        static::$beforeUploadCalled = false;
        static::$afterUploadCalled = false;
        static::$callOrder = [];
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $action = TestUnsplashPickerAction::make()
            ->beforeUpload(function () {
                static::$beforeUploadCalled = true;
                static::$callOrder[] = 'beforeUpload';
            })
            ->afterUpload(function () {
                static::$afterUploadCalled = true;
                static::$callOrder[] = 'afterUpload';
            });

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
