<?php

namespace Cone\Root\Resources;

use Cone\Root\Exceptions\ResourceResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Resources extends Collection
{
    /**
     * Discover the resources in the given paths.
     */
    public function discoverIn(string|array $paths): void
    {
        foreach ((array) $paths as $path) {
            if (is_dir($path)) {
                $this->discover($path);
            }
        }
    }

    /**
     * Discover and register the resources.
     */
    protected function discover(string $path): void
    {
        $namespace = App::getNamespace();

        foreach ((new Finder)->in($path)->files() as $resource) {
            $resource = str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getPathname(), App::path().DIRECTORY_SEPARATOR)
            );

            $resource = $namespace.$resource;

            if (is_subclass_of($resource, Resource::class) && (new ReflectionClass($resource))->isInstantiable()) {
                $this->register(new $resource);
            }
        }
    }

    /**
     * Register the given resource into the collection.
     */
    public function register(array|Resource $resources): void
    {
        foreach (Arr::wrap($resources) as $resource) {
            $this->put($resource->getKey(), $resource);
        }
    }

    /**
     * Get the registered resource for the given model.
     */
    public function forModel(string|Model $model): ?Resource
    {
        $model = is_string($model) ? $model : $model::class;

        return $this->first(static fn(Resource $resource): bool => $resource->getModel() === $model);
    }

    /**
     * Resolve the resource registered with the given key.
     */
    public function resolve(string $key): Resource
    {
        if (! $this->has($key)) {
            throw new ResourceResolutionException;
        }

        return $this->get($key);
    }

    /**
     * Filter the authorized resources.
     */
    public function authorized(Request $request): static
    {
        return $this->filter->authorized($request)->values();
    }
}
