<?php

namespace Mansoor\UnsplashPicker\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * @internal Internal field component for UnsplashPicker to manage state of selected images.
 */
class UnsplashPickerField extends Field
{
    protected string $view = 'unsplash-picker::forms.components.unsplash-picker-field';
}
