<?php

declare(strict_types=1);

namespace Cone\Root\Jobs;

use Cone\Root\Models\Medium;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File as Stream;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MoveFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Medium $medium,
        public readonly string $path,
        public readonly bool $preserve = true
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $directory = dirname($this->medium->getPath());

        Storage::disk($this->medium->disk)->makeDirectory($directory);

        Storage::disk($this->medium->disk)->putFileAs(
            $directory, new Stream($this->path), basename($this->medium->getPath())
        );

        if (! $this->preserve && ! filter_var($this->path, FILTER_VALIDATE_URL)) {
            File::delete($this->path);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->medium->delete();
    }
}
