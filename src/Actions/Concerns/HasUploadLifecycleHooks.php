<?php

namespace Mansoor\UnsplashPicker\Actions\Concerns;

use Closure;

trait HasUploadLifecycleHooks
{
    protected ?Closure $afterUpload = null;

    protected ?Closure $beforeUpload = null;

    public function beforeUpload(?Closure $callback): static
    {
        $this->beforeUpload = $callback;

        return $this;
    }

    public function afterUpload(?Closure $callback): static
    {
        $this->afterUpload = $callback;

        return $this;
    }
}
