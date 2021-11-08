<?php

namespace Cone\Root\Extracts;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

abstract class Extract implements Arrayable
{
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
        $fields = $this->fields($request);

        if (empty($actions)) {
            return $resource->resolveFields($request);
        }

        return Fields::make($fields);
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
        $filters = $this->filters($request);

        if (empty($actions)) {
            return $resource->resolveFilters($request);
        }

        return Filters::make($filters);
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
        $actions = $this->actions($request);

        if (empty($actions)) {
            return $resource->resolveActions($request);
        }

        return Actions::make($actions);
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
     * @return \Inertia\Response
     */
    public function toIndex(Request $request, Resource $resource): Response
    {
        $filters = $this->resolveFilters($request, $resource);

        //

        return Inertia::render(
            'Resouce/Index',
            array_merge($this->toArray(), [
                'filters' => $filters->toArray(),
            ])
        );
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
