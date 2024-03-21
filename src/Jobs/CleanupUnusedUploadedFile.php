<?php

namespace Mansoor\UnsplashPicker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CleanupUnusedUploadedFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $model, public string $column, public string $filePath, public string $diskName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $model = $this->model::where($this->column, $this->filePath)->first();

        if ($model) {
            return;
        }

        $storage = Storage::disk($this->diskName);

        if (! $storage->exists($this->filePath)) {
            return;
        }

        $storage->delete($this->filePath);
    }
}
