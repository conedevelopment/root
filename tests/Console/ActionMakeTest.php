<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class ActionMakeTest extends TestCase
{
    /** @test */
    public function an_action_make_command_creates_action()
    {
        $this->artisan('root:action', ['name' => 'TestAction'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Actions/TestAction.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Actions/TestAction.php'));

        parent::tearDown();
    }
}
