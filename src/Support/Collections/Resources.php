<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Interfaces\Support\Collections\Resources as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Resources extends Collection implements Contract
{
    /**
     * Filter the available resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Resources
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Resource $resource) use ($request): bool {
            return $resource->authorized($request);
        });
    }
}
