<?php

declare(strict_types = 1);

namespace Cone\Root\Support\Collections;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Interfaces\Support\Collections\Resources as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Support\Collection;

class Resources extends Collection implements Contract
{
    /**
     * Filter the available resources.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Support\Collections\Resources
     */
    public function available(RootRequest $request): static
    {
        return $this->filter(static function (Resource $resource) use ($request): bool {
            return $resource->authorized($request);
        });
    }
}
