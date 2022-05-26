<?php

namespace Cone\Root\Http\Resources;

use Cone\Root\Http\Requests\ResourceRequest;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\URL;

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
        if ($this->resource instanceof Pivot) {
            return URL::to(sprintf(
                '%s/%s/%s',
                $request->resolved()->getUri(),
                $this->resource->pivotParent->getKey(),
                $this->resource->related->getKey()
            ));
        }

        $path = sprintf('%s/%s', $request->resolved()->getUri(), $this->resource->parent->getKey());

        return $this->resource->exists
            ? URL::to(sprintf('%s/%s', $path, $this->resource->getKey()))
            : URL::to($path);
    }
}
