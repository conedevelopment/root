<?php

namespace Cone\Root\Actions;

use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Action implements Arrayable
{
    use Authorizable;
    use ResolvesVisibility;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * Indicates if the action is inline (visible on table row).
     *
     * @var bool
     */
    protected bool $inline = false;

    /**
     * The resolved components.
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
     * Register the action routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->defaultRegisterRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        $router->post($this->getKey(), ActionController::class);
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return (string) Str::of(static::class)->classBasename()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) Str::of(static::class)->classBasename()->headline();
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
     * Set the inline attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function inline(bool $value = true): static
    {
        $this->inline = $value;

        return $this;
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

            $this->resolved['fields']->each->mergeAuthorizationResolver(function (Request $request): bool {
                return $this->authorized($request);
            });
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
            'url' => URL::to($this->getUri()),
        ];
    }

    /**
     * Get the form representation of the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toForm(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'fields' => $this->resolveFields($request)->available($request, $model)->mapToForm($request, $model)->toArray(),
        ]);
    }
}
