<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class ValueMakeTest extends TestCase
{
    public function test_a_value_make_command_creates_value_widget(): void
    {
        $this->artisan('root:value', ['name' => 'ValueWidget'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Widgets/ValueWidget.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Widgets/ValueWidget.php'));

        parent::tearDown();
    }
}
