<?php

namespace Cone\Root\Registries;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Interfaces\Registries\ResourceRegistry as Contract;
use Cone\Root\Interfaces\Resourceable;
use Cone\Root\Resources\Resource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ResourceRegistry extends Registry implements Contract
{
    /**
     * Resolve the resource by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Resources\Resource
     *
     * @throws \Cone\Root\Exceptions\ResourceResolutionException
     */
    public function resolve(string $key): Resource
    {
        if (! $this->has($key)) {
            throw new ResourceResolutionException("Unable to resolve resource with key [{$key}].");
        }

        return $this->get($key);
    }

    /**
     * Register all of the resources in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    public function discover(string|array $paths): void
    {
        $paths = array_filter(array_unique((array) $paths), 'is_dir');

        if (empty($paths)) {
            return;
        }

        $namespace = App::getNamespace();

        foreach ((new Finder())->in($paths)->files() as $model) {
            $model = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($model->getRealPath(), realpath(App::path()).DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($model, Resourceable::class) && ! (new ReflectionClass($model))->isAbstract()) {
                $this->register(
                    ($instance = $model::toRootResource())->getKey(), $instance
                );
            }
        }
    }
}
