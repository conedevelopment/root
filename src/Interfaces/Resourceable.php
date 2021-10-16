<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Http\Request;

interface Resourceable
{
    /**
     * Get the Root resource display representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toResourceDisplay(Request $request, Resource $resource, Fields $fields): array;

    /**
     * Get the Root resource form representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toResourceForm(Request $request, Resource $resource, Fields $fields): array;

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource;
}
