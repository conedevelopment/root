<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class FilterMakeTest extends TestCase
{
    public function test_filter_make_command(): void
    {
        $this->artisan('root:filter', [
            'name' => 'TestFilter',
            '--type' => 'select',
            '--multiple' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('Root/Filters/TestFilter.php'));
    }

    protected function tearDown(): void
    {
        File::delete($this->app->path('Root/Filters/TestFilter.php'));

        parent::tearDown();
    }
}
