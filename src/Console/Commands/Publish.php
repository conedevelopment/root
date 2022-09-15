<?php

declare(strict_types = 1);

namespace Cone\Root\Console\Commands;

use Cone\Root\RootServiceProvider;
use Illuminate\Console\Command;

class Publish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:publish {--force : Overwrite any existing files}
                                        {--packages : Update the "packages.json" file}
                                        {--tag=* : One or many tags that have assets you want to publish}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the Root assets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('packages')) {
            $this->packages();
        }

        return $this->call('vendor:publish', array_merge(
            ['--provider' => RootServiceProvider::class],
            $this->option('force') ? ['--force' => true] : [],
            ['--tag' => $this->option('tag') ?: ['root-compiled']]
        ));
    }

    /**
     * Update the "packages.json" file.
     *
     * @return void
     */
    protected function packages(): void
    {
        $rootPackages = json_decode(file_get_contents(__DIR__.'/../../../package.json'), true);

        if (file_exists($this->laravel->basePath('package.json'))) {
            $packages = json_decode(file_get_contents($this->laravel->basePath('package.json')), true);

            $packages['dependencies'] = array_replace(
                $packages['dependencies'] ?? [], $rootPackages['dependencies']
            );

            ksort($packages['dependencies']);
        }

        file_put_contents(
            $this->laravel->basePath('package.json'),
            json_encode($packages ?? $rootPackages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );

        $this->info('The "packages.json" file has been updated.');
    }
}
