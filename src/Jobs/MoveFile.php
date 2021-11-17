<?php

namespace Cone\Root\Jobs;

use Cone\Root\Models\Medium;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
     * The medium instance.
     *
     * @var \Cone\Root\Models\Medium
     */
    public Medium $medium;

    /**
     * The path to the file.
     *
     * @var string
     */
    public string $path;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param  \Cone\Root\Models\Medium  $medium
     * @param  string  $path
     * @return void
     */
    public function __construct(Medium $medium, string $path)
    {
        $this->path = $path;
        $this->medium = $medium;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Storage::disk($this->medium->disk)->makeDirectory(
            dirname($this->medium->getPath())
        );

        Storage::disk($this->medium->disk)->move(
            $this->path, $this->medium->getPath()
        );
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        $this->medium->delete();

        File::delete($this->path);
    }
}

