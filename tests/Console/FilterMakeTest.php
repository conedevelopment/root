<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class FilterMakeTest extends TestCase
{
    public function test_a_filter_make_command_creates_filter(): void
    {
        $this->artisan('root:filter', [
            'name' => 'TestFilter',
            '--type' => 'select',
            '--multiple' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Filters/TestFilter.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Filters/TestFilter.php'));

        parent::tearDown();
    }
}
