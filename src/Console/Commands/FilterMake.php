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
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name): string
    {
        $class = parent::buildClass($name);

        $class = $this->replaceComponent($class);

        return $this->replaceMultiple($class);
    }

    /**
     * Replace the component related code.
     *
     * @param  string  $class
     * @return string
     */
    protected function replaceComponent(string $class): string
    {
        if ($component = $this->option('component')) {
            return str_replace(
                [PHP_EOL.'%%component%%', '%%/component%%', '{{ component }}'],
                ['', '', $component],
                $class
            );
        }

        return preg_replace('/\s%%component%%.*%%\/component%%/s', '', $class);
    }

    /**
     * Replace the multiple related code.
     *
     * @param  string  $class
     * @return string
     */
    protected function replaceMultiple(string $class): string
    {
        if ($this->option('multiple')) {
            $class = str_replace([PHP_EOL.'{{multiple}}', '{{/multiple}}'], '', $class);
        }

        return preg_replace('/\s{{multiple}}.*{{\/multiple}}/s', '', $class);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['component', null, InputOption::VALUE_OPTIONAL, 'The Vue component'],
            ['multiple', null, InputOption::VALUE_NONE, 'Mark the filter as multiple'],
        ];
    }
}
