<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;

interface Resourceable
{
    /**
     * Map the abilities for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    public function mapAbilities(ResourceRequest $request): array;

    /**
     * Map the URLs for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    public function mapUrls(ResourceRequest $request): array;

    /**
     * Get the Root resource display representation of the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toDisplay(ResourceRequest $request, Fields $fields): array;

    /**
     * Get the Root resource form representation of the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return array
     */
    public function toForm(ResourceRequest $request, Fields $fields): array;

    /**
     * Get the resource representation of the model.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public static function toResource(): Resource;
}
