<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FieldMakeTest extends TestCase
{
    public function test_field_make_command(): void
    {
        $this->artisan('root:field', [
            'name' => 'TestField',
            '--template' => 'foo',
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists($this->app->path('Root/Fields/TestField.php'));
    }

    protected function tearDown(): void
    {
        File::delete($this->app->path('Root/Fields/TestField.php'));

        parent::tearDown();
    }
}
