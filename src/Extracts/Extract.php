<?php

namespace Cone\Root\Extracts;

use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Resolvable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable
{
    use Authorizable;
    use Resolvable {
        Resolvable::resolved as defaultResolved;
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
     * Handle the event when the object is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        $this->defaultResolved($request, $resource, $key);

        $resource->setReference($key, $this);

        if (! App::routesAreCached()) {
            $this->routes($key);
        }

        $this->resolveFields($request)->resolved($request, $resource, $key);
        $this->resolveActions($request)->resolved($request, $resource, $key);
        $this->resolveWidgets($request)->resolved($request, $resource, $key);
    }

    /**
     * Regsiter the routes for the extract.
     *
     * @param  string  $path
     * @return void
     */
    protected function routes(string $path): void
    {
        Route::get("root/{$path}", ExtractController::class)
            ->middleware('root')
            ->setDefaults([
                'resource' => explode('/', $path, 2)[0],
                'reference' => $path,
            ]);
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
           'url' => URL::to("root/{$this->resolvedAs}"),
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

        $query = $this->query($request, $resource->query());

        $query = $filters->apply($request, $query)
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
