<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishTest extends TestCase
{
    public function test_publish_command(): void
    {
        $this->artisan('root:publish', ['--packages' => true])
            ->expectsOutput('Root files has been published.')
            ->assertExitCode(Command::SUCCESS);

        $this->assertDirectoryExists($this->app->publicPath('vendor/root'));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->app->publicPath('vendor'));

        parent::tearDown();
    }
}
