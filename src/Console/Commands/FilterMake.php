<?php

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
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/Filter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Root\\Filters';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['input', null, InputOption::VALUE_NONE, 'Mark the filter as input'],
            ['select', null, InputOption::VALUE_NONE, 'Mark the filter as select'],
        ];
    }
}
