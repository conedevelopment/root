<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;

class FieldMakeTest extends TestCase
{
    public function test_a_field_make_command_creates_field(): void
    {
        $this->artisan('root:field', [
            'name' => 'TestField',
            '--template' => 'foo',
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
