<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Http\Request;

interface Resourceable
{
    /**
     * Map the abilities for the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapAbilities(Request $request): array;

    /**
     * Get the Root resource display representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toDisplay(Request $request, Fields $fields): array;

    /**
     * Get the Root resource form representation of the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toForm(Request $request, Fields $fields): array;

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
