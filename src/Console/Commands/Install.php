<?php

declare(strict_types = 1);

namespace Cone\Root\Console\Commands;

use Cone\Root\Database\Seeders\RootTestDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:install {--seed : Seed the database with fake data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Root';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $status = $this->call('migrate');

        File::ensureDirectoryExists(Storage::disk('local')->path('root-chunks'));

        if ($this->option('seed')) {
            $status = $this->call('db:seed', ['--class' => RootTestDataSeeder::class]);
        }

        $status = $this->call('vendor:publish', ['--tag' => ['root-provider', 'root-user-resource']]);

        $this->registerRootServiceProvider();

        return $status;
    }

    /**
     * Register the Root service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerRootServiceProvider(): void
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents($this->laravel->configPath('app.php'));

        if (str_contains($appConfig, $namespace.'\\Providers\\RootServiceProvider::class')) {
            return;
        }

        file_put_contents($this->laravel->configPath('app.php'), str_replace(
            "{$namespace}\\Providers\\RouteServiceProvider::class,",
            sprintf(
                '%1$s\\Providers\\RootServiceProvider::class,%2$s%3$s%1$s\\Providers\\RouteServiceProvider::class,',
                $namespace, PHP_EOL, str_repeat(' ', 8)
            ),
            $appConfig
        ));

        file_put_contents(app_path('Providers/RootServiceProvider.php'), str_replace(
            ["namespace App\\Providers;", "use App\\Models\\User;"],
            ["namespace {$namespace}\\Providers;", "use {$namespace}\\Models\\User;"],
            file_get_contents(app_path('Providers/RootServiceProvider.php'))
        ));

        file_put_contents(app_path('Root/Resources/UserResource.php'), str_replace(
            "namespace App\\Root\\Resources;",
            "namespace {$namespace}\\Root\\Resources;",
            file_get_contents(app_path('Root/Resources/UserResource.php'))
        ));
    }
}
