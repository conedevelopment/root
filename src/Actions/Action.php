<?php

namespace Cone\Root\Actions;

use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\ResolvesVisibility;
use Cone\Root\Traits\ResourceRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class Action implements Arrayable
{
    use Authorizable;
    use ResolvesVisibility;
    use ResourceRoutable {
        ResourceRoutable::resolved as defaultResolved;
    }

    /**
     * The resolved store.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Make a new action instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Handle the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    abstract public function handle(Request $request, Collection $models): void;

    /**
     * Handle the event when the object is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        $this->defaultResolved($request, $resource, $key);

        $this->resolveFields($request)->resolved($request, $resource, "{$key}.fields");
    }

    /**
     * Regsiter the routes for the action.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    protected function routes(Resource $resource, string $uri): void
    {
        $resource->routes(function () use ($uri): void {
            Route::post($uri, ActionController::class);
        });
    }

    /**
     * Perform the action.
     *
     * @param  \Cone\Root\Http\Requests\ActionRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Http\RedirectResponse
     */
    public function perform(ActionRequest $request, Builder $query): RedirectResponse
    {
        $this->handle(
            $request,
            $query->findMany($request->input('models', []))
        );

        return Redirect::back();
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->lower()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename()->headline();
    }

    /**
     * Define the fields for the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        if (! isset($this->resolved['fields'])) {
            $this->resolved['fields'] = Fields::make($this->fields($request));
        }

        return $this->resolved['fields'];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'url' => call_user_func($this->urlResolver),
        ];
    }
}
