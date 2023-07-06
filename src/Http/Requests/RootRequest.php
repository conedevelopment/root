<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Resources\Resource;
use Cone\Root\Root;
use Illuminate\Http\Request;
use Throwable;

class RootRequest extends Request
{
    /**
     * Resolve the resource bound to the route.
     */
    public function resource(): ?Resource
    {
        if (empty($this->route()->action['__resource__'])) {
            return null;
        }

        try {
            return Root::instance()->resources->resolve($this->route()->action['__resource__']);
        } catch (Throwable $exception) {
            return null;
        }
    }
}
