<?php

declare(strict_types=1);

namespace Cone\Root\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class FieldMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'root:field';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new field class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Field';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/Field.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Root\\Fields';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     */
    protected function buildClass($name): string
    {
        $class = parent::buildClass($name);

        return $this->replaceTemplate($class);
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

        return preg_replace('/\n%%template%%.*%%\/template%%/s', '', $class);
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['template', null, InputOption::VALUE_OPTIONAL, 'The Blade template'],
        ];
    }
}
