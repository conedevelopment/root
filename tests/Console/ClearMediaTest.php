<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Console;

use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class ClearMediaTest extends TestCase
{
    public function test_clear_media_command(): void
    {
        $medium = Medium::factory()->create();

        $this->artisan('root:clear-media', ['all' => true])
            ->expectsOutput('1 media have been deleted!')
            ->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseMissing('root_media', ['id' => $medium->getKey()]);
    }
}
