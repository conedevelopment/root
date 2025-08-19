<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ActionMakeTest extends TestCase
{
    public function test_action_make_command(): void
    {
        $this->artisan('root:action', ['name' => 'TestAction'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('Root/Actions/TestAction.php'));
    }

    protected function tearDown(): void
    {
        File::delete($this->app->path('Root/Actions/TestAction.php'));

        parent::tearDown();
    }
}
