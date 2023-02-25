<?php

namespace Cone\Root\Resources;

use Cone\Root\Http\Requests\ResourceRequest;

class RelatedItem extends Item
{
    /**
     * Map the URL for the model.
     */
    protected function mapUrl(ResourceRequest $request): string
    {
        return sprintf(
            '%s/%s',
            $request->resolved()->resolveUri($request),
            $this->model->getKey()
        );
    }
}
