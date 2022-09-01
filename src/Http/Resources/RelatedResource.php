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
        $parent = $this->resource instanceof Pivot
            ? $this->resource->pivotParent
            : $this->resource->parent;

        return $request->resolved()->formatUri($parent, [$this->resource->getKey()]);
    }
}
