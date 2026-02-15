<?php

use Filament\Support\Enums\Width;
use Livewire\Livewire;
use Mansoor\UnsplashPicker\Tests\Fixtures\UnsplashApiResponse;
use Mansoor\UnsplashPicker\Tests\Support\TestFormComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestFormWithHooksComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestFormWithSizeComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestMultipleFormComponent;
use Mansoor\UnsplashPicker\Tests\Support\TestUnsplashPickerAction;

beforeEach(function () {
    config()->set('services.unsplash.client_id', 'test-client-id');
    TestUnsplashPickerAction::resetTracking();
    TestFormWithHooksComponent::resetHookTracking();
});

it('has the unsplash picker action on file upload', function () {
    Livewire::test(TestFormComponent::class)
        ->assertFormComponentActionExists('photo', 'unsplash_picker_action')
        ->assertFormComponentActionHasLabel('photo', 'unsplash_picker_action', 'Pick from Unsplash')
        ->assertFormComponentActionHasIcon('photo', 'unsplash_picker_action', 'up-unsplash');
});

it('opens a modal with screen large width', function () {
    Livewire::test(TestFormComponent::class)
        ->mountFormComponentAction('photo', 'unsplash_picker_action')
        ->assertFormComponentActionMounted('photo', 'unsplash_picker_action');
});

it('modal has screen large width', function () {
    $component = Livewire::test(TestFormComponent::class);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photo'],
        ],
    ]);

    expect($action->getModalWidth())->toBe(Width::ScreenLarge);
});

it('does not show description in single mode', function () {
    $component = Livewire::test(TestFormComponent::class);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photo'],
        ],
    ]);

    expect($action->getModalDescription())->toBeNull();
});

it('shows description with selectable count in multiple mode', function () {
    $component = Livewire::test(TestMultipleFormComponent::class);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photos'],
        ],
    ]);

    expect($action->getModalDescription())->toContain('You may select 5 photos');
});

it('is not disabled in single mode', function () {
    Livewire::test(TestFormComponent::class)
        ->assertFormComponentActionEnabled('photo', 'unsplash_picker_action');
});

it('is disabled when multiple mode and max files reached', function () {
    $component = Livewire::test(TestMultipleFormComponent::class)
        ->set('data.photos', ['file1.jpg', 'file2.jpg', 'file3.jpg', 'file4.jpg', 'file5.jpg']);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photos'],
        ],
    ]);

    expect($action->isDisabled())->toBeTrue();
});

it('is not disabled when multiple mode and max files not reached', function () {
    $component = Livewire::test(TestMultipleFormComponent::class)
        ->set('data.photos', ['file1.jpg', 'file2.jpg']);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photos'],
        ],
    ]);

    expect($action->isDisabled())->toBeFalse();
});

it('downloads images and dispatches add-file events when submitting with selected images', function () {
    $photos = UnsplashApiResponse::photos(2);

    Livewire::test(TestFormWithSizeComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ])
        ->assertDispatched('add-file');

    expect(TestUnsplashPickerAction::$downloadedUrls)->toHaveCount(2);
    expect(TestUnsplashPickerAction::$dispatchedFiles)->toHaveCount(2);
});

it('uses regular size URL by default', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithSizeComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestUnsplashPickerAction::$downloadedUrls[0])
        ->toBe($photos[0]['urls']['regular']);
});

it('uses raw size URL when configured with raw()', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithSizeComponent::class, ['imageSizeName' => 'Raw'])
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestUnsplashPickerAction::$downloadedUrls[0])
        ->toBe($photos[0]['urls']['raw']);
});

it('uses full size URL when configured with full()', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithSizeComponent::class, ['imageSizeName' => 'Full'])
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestUnsplashPickerAction::$downloadedUrls[0])
        ->toBe($photos[0]['urls']['full']);
});

it('uses small size URL when configured with small()', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithSizeComponent::class, ['imageSizeName' => 'Small'])
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestUnsplashPickerAction::$downloadedUrls[0])
        ->toBe($photos[0]['urls']['small']);
});

it('uses thumbnail size URL when configured with thumbnail()', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithSizeComponent::class, ['imageSizeName' => 'Thumbnail'])
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestUnsplashPickerAction::$downloadedUrls[0])
        ->toBe($photos[0]['urls']['thumb']);
});

it('does nothing when submitting with no selected images', function () {
    Livewire::test(TestFormWithSizeComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => [],
        ])
        ->assertNotDispatched('add-file');

    expect(TestUnsplashPickerAction::$downloadedUrls)->toBeEmpty();
    expect(TestUnsplashPickerAction::$dispatchedFiles)->toBeEmpty();
});

it('calls beforeUpload hook before images are downloaded', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithHooksComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestFormWithHooksComponent::$beforeUploadCalled)->toBeTrue();
    expect(TestFormWithHooksComponent::$callOrder[0])->toBe('beforeUpload');
});

it('calls afterUpload hook after images are downloaded', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithHooksComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestFormWithHooksComponent::$afterUploadCalled)->toBeTrue();
    expect(TestFormWithHooksComponent::$callOrder)->toHaveCount(2);
    expect(TestFormWithHooksComponent::$callOrder[1])->toBe('afterUpload');
});

it('calls beforeUpload before afterUpload in correct order', function () {
    $photos = UnsplashApiResponse::photos(2);

    Livewire::test(TestFormWithHooksComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ]);

    expect(TestFormWithHooksComponent::$callOrder)->toBe(['beforeUpload', 'afterUpload']);
});

it('does not error when hooks are null by default', function () {
    $photos = UnsplashApiResponse::photos(1);

    Livewire::test(TestFormWithSizeComponent::class)
        ->callFormComponentAction('photo', 'unsplash_picker_action', [
            'selectedImages' => $photos,
        ])
        ->assertDispatched('add-file');

    expect(TestUnsplashPickerAction::$downloadedUrls)->toHaveCount(1);
});

it('shows remaining selectable count when some files are already uploaded', function () {
    $component = Livewire::test(TestMultipleFormComponent::class)
        ->set('data.photos', ['file1.jpg', 'file2.jpg']);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photos'],
        ],
    ]);

    expect($action->getModalDescription())->toContain('You may select 3 photos');
});

it('shows singular description when only 1 selectable image remains', function () {
    $component = Livewire::test(TestMultipleFormComponent::class)
        ->set('data.photos', ['file1.jpg', 'file2.jpg', 'file3.jpg', 'file4.jpg']);

    $action = $component->instance()->getAction([
        [
            'name' => 'unsplash_picker_action',
            'context' => ['schemaComponent' => 'form.photos'],
        ],
    ]);

    expect($action->getModalDescription())->toContain('You may select 1 photo');
});

it('passes correct numberOfSelectableImages to modal form when slots are partially filled', function () {
    $component = Livewire::test(TestMultipleFormComponent::class)
        ->set('data.photos', ['file1.jpg', 'file2.jpg'])
        ->mountFormComponentAction('photos', 'unsplash_picker_action');

    $instance = $component->instance();
    $mountedAction = $instance->getMountedAction();

    $schema = $mountedAction->getSchema(
        \Filament\Schemas\Schema::make($instance)
            ->key('mountedActionSchema0')
            ->statePath('mountedActions.0.data')
    );

    $components = $schema->getComponents();
    $livewireComponent = $components[0];

    expect($livewireComponent)->toBeInstanceOf(\Filament\Schemas\Components\Livewire::class);
    expect($livewireComponent->getComponent())->toBe(\Mansoor\UnsplashPicker\Livewire\UnsplashPickerComponent::class);

    $props = $livewireComponent->getComponentProperties();
    expect($props['numberOfSelectableImages'])->toBe(3);
});
