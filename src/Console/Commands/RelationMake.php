<?php

namespace Cone\Root\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class RelationMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'root:relation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new relation class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Relation';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/Relation.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Root\\Relations';
    }
}
