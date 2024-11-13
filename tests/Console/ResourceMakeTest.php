<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ResourceMakeTest extends TestCase
{
    public function test_resource_make_command(): void
    {
        $this->artisan('root:resource', ['name' => 'TestResource'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Resources/TestResource.php'));
    }

    public function tearDown(): void
    {
        File::delete($this->app->path('Root/Resources/TestResource.php'));

        parent::tearDown();
    }
}
