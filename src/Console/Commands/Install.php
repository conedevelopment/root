<?php

namespace Cone\Root\Console\Commands;

use Cone\Root\Database\Seeders\RootTestDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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

        File::ensureDirectoryExists(Storage::disk('local')->path('chunks'));

        if ($this->option('seed')) {
            $status = $this->call('db:seed', ['--class' => RootTestDataSeeder::class]);
        }

        $status = $this->call('vendor:publish', ['--tag' => 'root-provider']);

        return $status;
    }
}
