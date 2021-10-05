<?php

namespace Cone\Root\Registries;

use Cone\Root\Interfaces\Registries\ResourceRegistry as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ResourceRegistry extends Registry implements Contract
{
    /**
     * Register an item into the registry.
     *
     * @param  string  $key
     * @param  object  $item
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function register(string $key, object $item): void
    {
        if (! $item instanceof Resource) {
            throw new InvalidArgumentException('The item must be an instance of [Root\\Resources\\Resource].');
        }

        parent::register($key, $item);
    }

    /**
     * Register all of the resources in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    public function discover(array|string $paths): void
    {
        $paths = array_filter(array_unique((array) $paths), 'is_dir');

        if (empty($paths)) {
            return;
        }

        $namespace = App::getNamespace();

        foreach ((new Finder())->in($paths)->files() as $resource) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getRealPath(), realpath(App::path()).DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($resource, Resource::class) && ! (new ReflectionClass($resource))->isAbstract()) {
                $this->register($resource::getKey(), new $resource());
            }
        }
    }
}
