<?php

namespace Cone\Root\Extracts;

use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Http\Requests\ExtractRequest;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Extract implements Arrayable, Routable
{
    /**
     * The cache store.
     *
     * @var array
     */
    protected array $cache = [];

    /**
     * The URI for the field.
     *
     * @var string|null
     */
    protected ?string $uri = null;

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
        if (! isset($this->cache['fields'])) {
            $this->cache['fields'] = Fields::make($this->fields($request));
        }

        return $this->cache['fields'];
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
        if (! isset($this->cache['filters'])) {
            $this->cache['filters'] = Filters::make($this->filters($request));
        }

        return $this->cache['filters'];
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
        if (! isset($this->cache['actions'])) {
            $this->cache['actions'] = Actions::make($this->actions($request));
        }

        return $this->cache['actions'];
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
        if (! isset($this->cache['widgets'])) {
            $this->cache['widgets'] = Widgets::make($this->widgets($request));
        }

        return $this->cache['widgets'];
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

        $filters = $this->resolveFilters($request);

        $fields = $this->resolveFields($request)->filterVisible($request);

        $query = $this->query($request, $resource->query());

        $query = $filters->apply($request, $query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(static function (Model $model) use ($request, $resource, $fields): array {
                        return $model->toResourceDisplay($request, $resource, $fields);
                    });

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->filterVisible($request)->toArray(),
            'filters' => $filters->toArray(),
            'query' => $query->toArray(),
            'widgets' => $this->resolveWidgets($request)->filterVisible($request)->toArray(),
        ]);
    }

    /**
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function routes(Request $request): void
    {
        Route::get($this->getKey(), ExtractController::class);
    }

    /**
     * Set the URI attribute.
     *
     * @param  string  $uri
     * @return void
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Get the URI attribute.
     *
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }
}
