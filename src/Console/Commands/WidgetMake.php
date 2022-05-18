<?php

namespace Cone\Root\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class WidgetMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'root:widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Widget';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/Widget.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Root\\Widgets';
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

        $class = $this->replaceAsync($class);

        $class = $this->replaceComponent($class);

        return $this->replaceTemplate($class);
    }

    /**
     * Replace the async related code.
     *
     * @param  string  $class
     * @return string
     */
    protected function replaceAsync(string $class): string
    {
        if ($this->option('async')) {
            $class = str_replace([PHP_EOL.'%%async%%', '%%/async%%'], '', $class);
        }

        return preg_replace('/\n%%async%%.*%%\/async%%/s', '', $class);
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

        return preg_replace('/\n%%component%%.*%%\/component%%/s', '', $class);
    }

    /**
     * Replace the template related code.
     *
     * @param  string  $class
     * @return string
     */
    protected function replaceTemplate(string $class): string
    {
        $template = $this->option('template') ?: 'widgets.'.Str::kebab($this->getNameInput());

        return str_replace('{{ template }}', $template, $class);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['async', null, InputOption::VALUE_NONE, 'Mark the widget as async'],
            ['component', null, InputOption::VALUE_OPTIONAL, 'The Vue component'],
            ['template', null, InputOption::VALUE_OPTIONAL, 'The Blade template'],
        ];
    }
}
