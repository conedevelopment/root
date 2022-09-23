<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class ExtractMakeTest extends TestCase
{
    /** @test */
    public function an_extract_make_command_creates_extract()
    {
        $this->artisan('root:extract', ['name' => 'TestExtract'])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Extracts/TestExtract.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Extracts/TestExtract.php'));

        parent::tearDown();
    }
}
