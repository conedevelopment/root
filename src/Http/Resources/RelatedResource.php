<?php

declare(strict_types = 1);

namespace Cone\Root\Http\Resources;

use Cone\Root\Http\Requests\ResourceRequest;

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
