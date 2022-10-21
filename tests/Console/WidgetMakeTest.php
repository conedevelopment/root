<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class WidgetMakeTest extends TestCase
{
    /** @test */
    public function a_widget_make_command_creates_widget()
    {
        $this->artisan('root:widget', ['name' => 'TestWidget'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Widgets/TestWidget.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Widgets/TestWidget.php'));

        parent::tearDown();
    }
}
