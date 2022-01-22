<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

interface Resourceable
{
    /**
     * Map the resource URLs for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function mapResourceUrls(Request $request, Resource $resource): array;

    /**
     * Map the resource abilities for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function mapResourceAbilities(Request $request, Resource $resource): array;

    /**
     * Get the Root resource display representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function toResourceDisplay(Request $request, Resource $resource): array;

    /**
     * Get the Root resource form representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return array
     */
    public function toResourceForm(Request $request, Resource $resource): array;

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource;

    /**
     * Register the resource for the model.
     *
     * @return void
     */
    public static function registerResource(): void;
}
