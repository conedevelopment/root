<?php

namespace Cone\Root\Console\Commands;

use Cone\Root\Database\Seeders\RootTestDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
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
     */
    public function handle(): void
    {
        $this->call('migrate');

        File::ensureDirectoryExists(Config::get('root.media.tmp_dir'));

        if ($this->option('seed')) {
            $this->call('db:seed', ['--class' => RootTestDataSeeder::class]);
        }

        $this->call('vendor:publish', ['--tag' => ['root-provider', 'root-user-resource']]);

        $this->registerServiceProvider();
    }

    /**
     * Register the Root service provider in the application configuration file.
     */
    protected function registerServiceProvider(): void
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

        file_put_contents($this->laravel->path('Providers/RootServiceProvider.php'), str_replace(
            ['namespace App\\Providers;', 'use App\\Models\\User;'],
            ["namespace {$namespace}\\Providers;", "use {$namespace}\\Models\\User;"],
            file_get_contents($this->laravel->path('Providers/RootServiceProvider.php'))
        ));

        file_put_contents($this->laravel->path('Root/Resources/UserResource.php'), str_replace(
            'namespace App\\Root\\Resources;',
            "namespace {$namespace}\\Root\\Resources;",
            file_get_contents($this->laravel->path('Root/Resources/UserResource.php'))
        ));
    }
}
