<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Facades\Resource as Registry;

class ResourceRequest extends RootRequest
{
    /**
     * Resolve the resource for the request.
     */
    public function resource(): Resource
    {
        return Registry::resolve($this->route()->action['resource']);
    }
}
