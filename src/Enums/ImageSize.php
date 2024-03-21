<?php

namespace Mansoor\UnsplashPicker\Enums;

enum ImageSize
{
    case Raw;
    case Full;
    case Regular;
    case Small;
    case Thumbnail;

    public function getPath(): string
    {
        return match ($this) {
            self::Raw => 'urls.raw',
            self::Full => 'urls.full',
            self::Regular => 'urls.regular',
            self::Small => 'urls.small',
            self::Thumbnail => 'urls.thumb',
        };
    }
}
