<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ValueMakeTest extends TestCase
{
    public function test_value_make_command(): void
    {
        $this->artisan('root:value', ['name' => 'ValueWidget'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Widgets/ValueWidget.php'));
    }

    public function tearDown(): void
    {
        File::delete($this->app->path('Root/Widgets/ValueWidget.php'));

        parent::tearDown();
    }
}
