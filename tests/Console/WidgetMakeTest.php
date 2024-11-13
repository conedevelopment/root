<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WidgetMakeTest extends TestCase
{
    public function test_widget_make_command(): void
    {
        $this->artisan('root:widget', ['name' => 'TestWidget'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Widgets/TestWidget.php'));
    }

    public function tearDown(): void
    {
        File::delete($this->app->path('Root/Widgets/TestWidget.php'));

        parent::tearDown();
    }
}
