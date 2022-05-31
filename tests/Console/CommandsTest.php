<?php

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CommandsTest extends TestCase
{
    /** @test */
    public function a_command_can_clear_chunks()
    {
        Storage::disk('local')->put(
            'chunks/test.chunk',
            UploadedFile::fake()->create('test.chunk')
        );

        $this->artisan('root:clear-chunks')
            ->expectsOutput('File chunks are cleared!')
            ->assertExitCode(Command::SUCCESS);
    }
}
