<?php

namespace Mansoor\UnsplashPicker\Components;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UnsplashPickerComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public string $search = '';

    public int $page = 1;

    public ?int $totalPages = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('search')
                    ->live(debounce: 300)
                    ->hiddenLabel()
                    ->autocomplete(false)
                    ->placeholder(__('unsplash-picker::unsplash-picker.search_for_an_image')),
            ]);
    }

    #[Computed]
    public function getImages()
    {
        $response = Http::get('https://api.unsplash.com/search/photos', [
            'query' => $this->search,
            'per_page' => $this->getPerPage(),
            'page' => $this->page,
            'client_id' => config('services.unsplash.client_id'),
        ]);

        throw_if($response->failed(), new Exception(Arr::get($response->json(), 'errors.0')));

        $this->totalPages = Arr::get($response->json(), 'total_pages');

        return Arr::get($response->json(), 'results');
    }

    public function nextPageAction(): Action
    {
        return Action::make('nextPage')
            ->button()
            ->label(__('unsplash-picker::unsplash-picker.next'))
            ->color('gray')
            ->disabled(fn () => $this->totalPages <= 1 || $this->page === $this->totalPages)
            ->action(function () {
                $this->nextPage();
            });
    }

    public function previousPageAction(): Action
    {
        return Action::make('previousPage')
            ->button()
            ->color('gray')
            ->label(__('unsplash-picker::unsplash-picker.previous'))
            ->disabled(fn () => $this->totalPages <= 1 || $this->page === 1)
            ->action(function () {
                $this->previousPage();
            });
    }

    public function nextPage()
    {
        $this->page++;
    }

    public function previousPage()
    {
        $this->page--;
    }

    #[Computed]
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getPerPage(): int
    {
        return 9;
    }

    public function render()
    {
        return view('unsplash-picker::unsplash-picker');
    }
}
