<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TrendMakeTest extends TestCase
{
    public function test_trend_make_command(): void
    {
        $this->artisan('root:trend', ['name' => 'TrendWidget'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('Root/Widgets/TrendWidget.php'));
    }

    public function tearDown(): void
    {
        File::delete($this->app->path('Root/Widgets/TrendWidget.php'));

        parent::tearDown();
    }
}
