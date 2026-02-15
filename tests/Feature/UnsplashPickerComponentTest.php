<?php

use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Mansoor\UnsplashPicker\Livewire\UnsplashPickerComponent;
use Mansoor\UnsplashPicker\Tests\Fixtures\UnsplashApiResponse;

beforeEach(function () {
    config()->set('services.unsplash.client_id', 'test-client-id');
});

it('can mount with default properties', function () {
    Http::fake();

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => '',
        'perPage' => 20,
    ])
        ->assertOk()
        ->assertSet('search', '')
        ->assertSet('perPage', 20)
        ->assertSet('useSquareDisplay', true)
        ->assertSet('isMultiple', false)
        ->assertSet('numberOfSelectableImages', 1)
        ->assertSet('page', 1)
        ->assertSet('searching', false);
});

it('can mount with custom properties', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 10,
        'useSquareDisplay' => false,
        'isMultiple' => true,
        'numberOfSelectableImages' => 5,
    ])
        ->assertOk()
        ->assertSet('search', 'nature')
        ->assertSet('perPage', 10)
        ->assertSet('useSquareDisplay', false)
        ->assertSet('isMultiple', true)
        ->assertSet('numberOfSelectableImages', 5);
});

it('returns empty array when search is blank', function () {
    Http::fake();

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => '',
        'perPage' => 20,
    ]);

    Http::assertNothingSent();
});

it('calls unsplash api when search has value', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.unsplash.com/search/photos')
            && $request['query'] === 'nature'
            && $request['per_page'] === 20
            && $request['page'] === 1
            && $request['client_id'] === 'test-client-id';
    });
});

it('sends correct query parameters to unsplash api', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'mountains',
        'perPage' => 15,
    ]);

    Http::assertSent(function ($request) {
        return $request['query'] === 'mountains'
            && $request['per_page'] === 15;
    });
});

it('sets total pages from api response', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 3, totalPages: 7),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])->assertSet('totalPages', 7);
});

it('sets searching to false after api response', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])->assertSet('searching', false);
});

it('renders photographer names from search results', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 2),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->assertSee('Photographer 0')
        ->assertSee('Photographer 1');
});

it('renders image thumbnails from search results', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 1),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])->assertSeeHtml('https://images.unsplash.com/photo_1?ixlib=rb-4.0.3&amp;q=75&amp;fm=jpg&amp;w=200&amp;fit=max');
});

it('renders photographer profile links', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 1),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])->assertSeeHtml('https://unsplash.com/@photographer_0');
});

it('shows no results message when search returns empty', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::emptySearchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'xyznonexistent',
        'perPage' => 20,
    ])->assertSeeHtml("your search didn't return any results");
});

it('throws exception when api returns error', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::errorResponse(),
            401,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ]);
})->throws(Exception::class, 'OAuth error: The access token is invalid');

it('throws exception with custom error message from api', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::errorResponse('Rate Limit Exceeded'),
            403,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ]);
})->throws(Exception::class, 'Rate Limit Exceeded');

it('can go to next page', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 5),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->assertSet('page', 1)
        ->call('nextPage')
        ->assertSet('page', 2);
});

it('can go to previous page', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 5),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->call('nextPage')
        ->call('nextPage')
        ->assertSet('page', 3)
        ->call('previousPage')
        ->assertSet('page', 2);
});

it('sends correct page number to api on pagination', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 5),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->call('nextPage')
        ->assertSet('page', 2);

    Http::assertSent(function ($request) {
        return $request['page'] === 2;
    });
});

it('resets page to 1 when search changes', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 5),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->call('nextPage')
        ->call('nextPage')
        ->assertSet('page', 3)
        ->fillForm(['search' => 'mountains'])
        ->assertSet('page', 1);
});

it('returns correct per page value', function () {
    Http::fake();

    $component = new UnsplashPickerComponent;
    $component->perPage = 15;

    expect($component->getPerPage())->toBe(15);
});

it('applies square display css class when enabled', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 1),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
        'useSquareDisplay' => true,
    ])->assertSeeHtml('grid grid-cols-3 lg:grid-cols-4 gap-4');
});

it('applies masonry css class when square display is disabled', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 1),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
        'useSquareDisplay' => false,
    ])->assertSeeHtml('columns-3 space-y-4 lg:columns-4');
});

it('renders the search form', function () {
    Http::fake();

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => '',
        'perPage' => 20,
    ])->assertSee('Search photos...');
});

it('renders square mode toggle', function () {
    Http::fake();

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => '',
        'perPage' => 20,
    ])->assertSee('Square Mode');
});

it('renders pagination buttons when results exist', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 3, totalPages: 5),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->assertSee('Previous')
        ->assertSee('Next');
});

it('does not render pagination buttons when no results', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::emptySearchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'xyznonexistent',
        'perPage' => 20,
    ])
        ->assertDontSee('Previous')
        ->assertDontSee('Next');
});

it('renders multiple photos from search results', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(photoCount: 4),
            200,
        ),
    ]);

    $component = Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ]);

    for ($i = 0; $i < 4; $i++) {
        $component->assertSee("Photographer {$i}");
    }
});

it('uses client id from config', function () {
    config()->set('services.unsplash.client_id', 'my-custom-key');

    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ]);

    Http::assertSent(function ($request) {
        return $request['client_id'] === 'my-custom-key';
    });
});

it('handles server error response', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::errorResponse('Internal Server Error'),
            500,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ]);
})->throws(Exception::class, 'Internal Server Error');

it('disables next page action on the last page', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 3),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->call('nextPage')
        ->call('nextPage')
        ->assertSet('page', 3)
        ->assertActionDisabled('nextPage');
});

it('disables previous page action on the first page', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 3),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->assertSet('page', 1)
        ->assertActionDisabled('previousPage');
});

it('disables both pagination actions when there is only one page', function () {
    Http::fake([
        'api.unsplash.com/search/photos*' => Http::response(
            UnsplashApiResponse::searchResponse(totalPages: 1),
            200,
        ),
    ]);

    Livewire::test(UnsplashPickerComponent::class, [
        'search' => 'nature',
        'perPage' => 20,
    ])
        ->assertActionDisabled('nextPage')
        ->assertActionDisabled('previousPage');
});
