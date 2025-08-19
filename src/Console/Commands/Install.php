<?php

declare(strict_types=1);

namespace Cone\Root\Console\Commands;

use Cone\Root\Database\Seeders\RootTestDataSeeder;
use Cone\Root\RootServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

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

        $this->call('vendor:publish', [
            '--provider' => RootServiceProvider::class,
            '--tag' => 'root-stubs',
        ]);

        $this->info('Root has been installed.');
    }
}
