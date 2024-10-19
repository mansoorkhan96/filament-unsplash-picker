<?php

namespace Mansoor\UnsplashPicker\Actions\Concerns;

use Mansoor\UnsplashPicker\Enums\ImageSize;

trait HasImageSizes
{
    protected ImageSize $imageSize = ImageSize::Regular;

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

    public function getImageSize(): ImageSize
    {
        return $this->imageSize;
    }
}
