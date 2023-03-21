<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Resources\Resource;
use Cone\Root\Root;
use Illuminate\Support\Facades\App;

class ResourceRequest extends RootRequest
{
    /**
     * Resolve the resource for the request.
     */
    public function resource(): Resource
    {
        return App::make(Root::class)->resources->resolve($this->route()->action['resource']);
    }
}
