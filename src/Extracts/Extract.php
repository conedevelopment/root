<?php

namespace Cone\Root\Extracts;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

abstract class Extract implements Arrayable
{
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
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  \Cone\Root\Support\Collections\Filters  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Request $request, Resource $resource, Filters $filters): Builder
    {
        return $resource->filteredQuery($request, $filters);
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
     * Resolve the filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request, Resource $resource): Fields
    {
        $fields = Fields::make($this->fields($request));

        if ($fields->isEmpty()) {
            return $resource->resolveFields($request);
        }

        return $fields;
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
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Cone\Root\Support\Collections\Filters
     */
    public function resolveFilters(Request $request, Resource $resource): Filters
    {
        $filters = Filters::make($this->filters($request));

        if ($filters->isEmpty()) {
            return $resource->resolveFilters($request);
        }

        return $filters;
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
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Cone\Root\Support\Collections\Actions
     */
    public function resolveActions(Request $request, Resource $resource): Actions
    {
        $actions = Actions::make($this->actions($request));

        if ($actions->isEmpty()) {
            return $resource->resolveActions($request);
        }

        return $actions;
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
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Cone\Root\Support\Collections\Widgets
     */
    public function resolveWidgets(Request $request, Resource $resource): Widgets
    {
        $widgets = Widgets::make($this->widgets($request));

        if ($widgets->isEmpty()) {
            return $resource->resolveWidgets($request);
        }

        return $widgets;
    }

    /**
     * Map the URLs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function mapUrls(Request $request, Resource $resource): array
    {
        return [
            'action' => URL::route('root.resource.extract.action', [$resource->getKey(), $this->getKey()]),
            'index' => URL::route('root.resource.extract.index', [$resource->getKey(), $this->getKey()]),
        ];
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
        ];
    }

    /**
     * Get the index representation of the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function toIndex(Request $request, Resource $resource): array
    {
        $filters = $this->resolveFilters($request, $resource);

        $fields = $this->resolveFields($request, $resource)->filterVisible($request);

        $query = $this->query($request, $resource, $filters)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(static function (Model $model) use ($request, $resource, $fields): array {
                        return $model->toResourceDisplay($request, $resource, $fields);
                    });

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request, $resource)->filterVisible($request)->toArray(),
            'filters' => $filters->toArray(),
            'query' => $query->toArray(),
            'urls' => $this->mapUrls($request, $resource),
            'widgets' => $this->resolveWidgets($request, $resource)->filterVisible($request)->toArray(),
        ]);
    }

    /**
     * Get the index representation of the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Inertia\Response
     */
    public function toIndexResponse(Request $request, Resource $resource): Response
    {
        return Inertia::render('Resource/Index', $this->toIndex($request, $resource));
    }

    /**
     * Handle the action request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleAction(Request $request, Resource $resource): RedirectResponse
    {
        $action = $this->resolveActions($request, $resource)
                    ->filterVisible($request)
                    ->resolveFromRequest($request);

        return $action->perform($request, $resource);
    }
}
