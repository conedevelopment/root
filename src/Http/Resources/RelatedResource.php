<?php

namespace Cone\Root\Http\Resources;

use Cone\Root\Http\Requests\ResourceRequest;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RelatedResource extends ModelResource
{
    /**
     * Map the URL for the model.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return string
     */
    protected function mapUrl(ResourceRequest $request): string
    {
        return sprintf(
            '%s/%s',
            $request->resolved()->resolveUri($request),
            $this->resource->getKey()
        );
    }
}
