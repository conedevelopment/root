<?php

namespace Cone\Root\Extracts;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Request $request, Resource $resource): Builder
    {
        return $resource->query();
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
        return $resource->resolveFields($request)->merge($this->filters($request));
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
        return $resource->resolveFilters($request)->merge($this->filters($request));
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
        return $resource->resolveActions($request)->merge($this->actions($request));
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
        return Inertia::render(
            'Resouce/Index',
            array_merge($this->toArray(), [
                //
            ])
        );
    }
}
