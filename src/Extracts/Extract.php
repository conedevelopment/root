<?php

namespace Cone\Root\Extracts;

use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable
{
    use Authorizable;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * The resolved store.
     *
     * @var array
     */
    protected array $resolved = [];

    /**
     * Make a new extract instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Register the extract routes.
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
            $this->resolveActions($request)->registerRoutes($request, $router);
            $this->resolveWidgets($request)->registerRoutes($request, $router);
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
        $router->get($this->getKey(), ExtractController::class);
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->plural()->kebab();
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
     * Get the query for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Request $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Define the fields for the extract.
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

            if (! $this->authorized($request)) {
                $this->resolved['fields']->each->authorize(static function (): bool {
                    return false;
                });
            }
        }

        return $this->resolved['fields'];
    }

    /**
     * Define the filters for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Filters
     */
    public function resolveFilters(Request $request): Filters
    {
        if (! isset($this->resolved['filters'])) {
            $this->resolved['filters'] = Filters::make($this->filters($request));

            if (! $this->authorized($request)) {
                $this->resolved['filters']->each->authorize(static function (): bool {
                    return false;
                });
            }
        }

        return $this->resolved['filters'];
    }

    /**
     * Define the actions for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Actions
     */
    public function resolveActions(Request $request): Actions
    {
        if (! isset($this->resolved['actions'])) {
            $this->resolved['actions'] = Actions::make($this->actions($request));

            if (! $this->authorized($request)) {
                $this->resolved['actions']->each->authorize(static function (): bool {
                    return false;
                });
            }
        }

        return $this->resolved['actions'];
    }

    /**
     * Define the widgets for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the widgets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Widgets
     */
    public function resolveWidgets(Request $request): Widgets
    {
        if (! isset($this->resolved['widgets'])) {
            $this->resolved['widgets'] = Widgets::make($this->widgets($request));
        }

        return $this->resolved['widgets'];
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
     * Get the index representation of the extract.
     *
     * @param  \Cone\Root\Http\Requests\ExtractRequest  $request
     * @return array
     */
    public function toIndex(ExtractRequest $request): array
    {
        $resource = $request->resource();

        $filters = $this->resolveFilters($request)->available($request);

        $fields = $this->resolveFields($request)->available($request);

        $query = $filters->apply($request, $this->query($request, $resource->query()))
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(static function (Model $model) use ($request, $resource, $fields): array {
                        return $model->toResourceDisplay($request, $resource, $fields);
                    });

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->available($request)->toArray(),
            'filters' => $filters->toArray(),
            'query' => $query->toArray(),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ]);
    }
}
