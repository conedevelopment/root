<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class FieldMakeTest extends TestCase
{
    /** @test */
    public function a_field_make_command_creates_field()
    {
        $this->artisan('root:field', [
                'name' => 'TestField',
                '--component' => 'foo',
            ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('/Root/Fields/TestField.php'));
    }

    public function tearDown(): void
    {
        unlink($this->app->path('/Root/Fields/TestField.php'));

        parent::tearDown();
    }
}
