<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Console;

use Cone\Root\Tests\TestCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class InstallTest extends TestCase
{
    public function test_install_command(): void
    {
        $this->artisan('root:install', ['--seed' => true])
            ->expectsOutput('Root has been installed.')
            ->assertExitCode(Command::SUCCESS);

        $this->assertDirectoryExists($this->app->storagePath('framework/testing/disks/local/root-tmp'));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->app->path('Root'));
        File::delete($this->app->path('Providers/RootServiceProvider.php'));

        parent::tearDown();
    }
}
