<?php

declare(strict_types = 1);

namespace Cone\Root\Http\Requests;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Facades\Resource as Registry;

class ResourceRequest extends RootRequest
{
    /**
     * Resolve the resource for the request.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public function resource(): Resource
    {
        return Registry::resolve($this->route()->action['resource']);
    }
}
