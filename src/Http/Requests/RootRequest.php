<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Facades\Resource as Registry;
use Illuminate\Http\Request;

class RootRequest extends Request
{
    /**
     * Resolve the resource for the request.
     *
     * @return \Cone\Root\Resources\Resource
     */
    public function resource(): Resource
    {
        return Registry::resolve($this->route('resource'));
    }
}
