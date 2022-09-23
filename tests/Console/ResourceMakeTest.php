<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class ResourceMakeTest extends TestCase
{
    /** @test */
    public function a_resource_make_command_creates_resource()
    {
        $this->artisan('root:resource', ['name' => 'TestResource'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Resources/TestResource.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Resources/TestResource.php'));

        parent::tearDown();
    }
}
