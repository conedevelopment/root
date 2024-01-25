<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CommandsTest extends TestCase
{
    public function test_a_command_can_clear_chunks(): void
    {
        Storage::disk('local')->putFileAs(
            '',
            UploadedFile::fake()->create('test.chunk'),
            'root-tmp/test.chunk'
        );

        $this->artisan('root:clear-chunks')
            ->expectsOutput('1 chunks are cleared!')
            ->assertExitCode(Command::SUCCESS);
    }
}
