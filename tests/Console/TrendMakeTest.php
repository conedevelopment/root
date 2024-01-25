<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class TrendMakeTest extends TestCase
{
    public function test_a_trend_make_command_creates_trend_widget(): void
    {
        $this->artisan('root:trend', ['name' => 'TrendWidget'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Widgets/TrendWidget.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Widgets/TrendWidget.php'));

        parent::tearDown();
    }
}
