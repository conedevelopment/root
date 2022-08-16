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
        $path = sprintf(
            '%s/%s',
            $request->resolved()->getUri(),
            $this->resource instanceof Pivot ? $this->resource->pivotParent->getKey() : $this->resource->parent->getKey()
        );

        return $this->resource->exists
            ? sprintf('%s/%s', $path, $this->resource->getKey())
            : $path;
    }
}
