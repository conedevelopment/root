<?php

declare(strict_types=1);

namespace Cone\Root\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class FilterMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'root:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new filter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return match ($this->option('type')) {
            default => __DIR__.'/../../../stubs/SelectFilter.stub',
        };
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Root\\Filters';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     */
    protected function buildClass($name): string
    {
        $class = parent::buildClass($name);

        return $this->replaceMultiple($class);
    }

    /**
     * Replace the multiple related code.
     */
    protected function replaceMultiple(string $class): string
    {
        if ($this->option('multiple')) {
            $class = str_replace([PHP_EOL.'%%multiple%%', '%%/multiple%%'], '', $class);
        }

        return preg_replace('/\s%%multiple%%.*%%\/multiple%%/s', '', $class);
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['type', null, InputOption::VALUE_OPTIONAL, 'The filter type', 'select'],
            ['multiple', null, InputOption::VALUE_NONE, 'Mark the filter as multiple'],
        ];
    }
}
