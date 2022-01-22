<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Facades\Resource as Registry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

trait InteractsWithResource
{
    /**
     * Map the resource URLs for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function mapResourceUrls(Request $request, Resource $resource): array
    {
        if (! $this->exists) {
            return [];
        }

        return [
            'edit' => URL::route('root.resource.edit', [$resource->getKey(), $this]),
            'show' => URL::route('root.resource.show', [$resource->getKey(), $this]),
        ];
    }

    /**
     * Map the resource abilities for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function mapResourceAbilities(Request $request, Resource $resource): array
    {
        $policy = $resource->getPolicy();

        $abilities = $resource->getAbilities();

        return array_reduce(
            $abilities['scoped'],
            function (array $stack, string $ability) use ($request, $policy): array {
                return array_merge($stack, [
                    $ability => is_null($policy) || $request->user()->can($ability, $this),
                ]);
            },
            []
        );
    }

    /**
     * Get the available resource fields for the model the current request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function getAvailableResourceFields(Request $request, Resource $resource): Fields
    {
        return $resource->resolveFields($request)->available($request, $this);
    }

    /**
     * Get the resource display representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function toResourceDisplay(Request $request, Resource $resource): array
    {
        return [
            'abilities' => $this->mapResourceAbilities($request, $resource),
            'fields' => $this->getAvailableResourceFields($request, $resource)->mapToDisplay($request, $this)->toArray(),
            'id' => $this->getKey(),
            'urls' => $this->mapResourceUrls($request, $resource),
        ];
    }

    /**
     * Get the resource form representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function toResourceForm(Request $request, Resource $resource): array
    {
        return [
            'abilities' => $this->mapResourceAbilities($request, $resource),
            'fields' => $this->getAvailableResourceFields($request, $resource)->mapToForm($request, $this)->toArray(),
            'urls' => $this->mapResourceUrls($request, $resource),
        ];
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource
    {
        return new Resource(static::class);
    }

    /**
     * Register the resource for the model.
     *
     * @return void
     */
    public static function registerResource(): void
    {
        $instance = static::toResource();

        Registry::register($instance->getKey(), $instance);
    }
}
