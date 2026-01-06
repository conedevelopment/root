<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Jobs;

use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\Queue;

final class PerformConversionsTest extends TestCase
{
    public function test_perform_conversions_job_can_be_dispatched(): void
    {
        Queue::fake();

        $medium = Medium::factory()->make();

        PerformConversions::dispatch($medium);

        Queue::assertPushed(PerformConversions::class);
    }

    public function test_perform_conversions_job_calls_convert_on_medium(): void
    {
        $medium = $this->createMock(Medium::class);
        $medium->expects($this->once())->method('convert');

        $job = new PerformConversions($medium);
        $job->handle();
    }
}
