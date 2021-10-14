<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

trait InteractsWithResources
{
    /**
     * Get the Root resource display representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toRootDisplay(Request $request, Resource $resource, Fields $fields): array
    {
        return [
            'fields' => $fields->mapToDisplay($request, $this)->toArray(),
            'urls' => [
                'show' => URL::route('root.resource.show', [$resource->getKey(), $this]),
                'edit' => URL::route('root.resource.edit', [$resource->getKey(), $this]),
            ],
            'can' => [
                //
            ],
        ];
    }

    /**
     * Get the Root resource form representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toRootForm(Request $request, Resource $resource, Fields $fields): array
    {
        //

        return [];
    }

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toRootResource(): Resource
    {
        return new Resource(static::class);
    }
}
