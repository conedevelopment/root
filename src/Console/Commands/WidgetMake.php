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
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/Widget.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Root\\Widgets';
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

        $this->makeView();

        return $class;
    }

    /**
     * Replace the template related code.
     */
    protected function replaceTemplate(string $class): string
    {
        return str_replace('{{ template }}', $this->getView(), $class);
    }

    /**
     * Make the view for the component.
     */
    protected function makeView(): void
    {
        $path = $this->viewPath(str_replace('.', '/', $this->getView()).'.blade.php');

        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->components->error('View already exists.');

            return;
        }

        file_put_contents($path, '<div></div>');
    }

    /**
     * Get the view name relative to the components directory.
     */
    protected function getView(): string
    {
        if ($this->option('template')) {
            return $this->option('template');
        }

        $name = str_replace('\\', '/', $this->getNameInput());

        return 'widgets.'.implode('.', array_map([Str::class, 'kebab'], explode('/', $name)));
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the component already exists'],
            ['template', null, InputOption::VALUE_OPTIONAL, 'The Blade template'],
        ];
    }
}
