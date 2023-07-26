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
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/Filter.stub';
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

        $class = $this->replaceTemplate($class);

        return $this->replaceMultiple($class);
    }

    /**
     * Replace the template related code.
     */
    protected function replaceTemplate(string $class): string
    {
        if ($template = $this->option('template')) {
            return str_replace(
                [PHP_EOL.'%%template%%', '%%/template%%', '{{ template }}'],
                ['', '', $template],
                $class
            );
        }

        return preg_replace('/\s%%template%%.*%%\/template%%/s', '', $class);
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
            ['template', null, InputOption::VALUE_OPTIONAL, 'The Blade template'],
            ['multiple', null, InputOption::VALUE_NONE, 'Mark the filter as multiple'],
        ];
    }
}
