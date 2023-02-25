<?php

namespace Cone\Root\Jobs;

use Cone\Root\Models\Medium;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PerformConversions implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The medium instance.
     */
    public Medium $medium;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(Medium $medium)
    {
        $this->medium = $medium;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->medium->convert();
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->medium->delete();
    }
}
