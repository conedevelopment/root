<?php

namespace Cone\Root\Interfaces\Registries;

use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

interface ResourceRegistry
{
    /**
     * Resolve the resource by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Resources\Resource
     */
    public function resolve(string $key): Resource;

    /**
     * Filter the available resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function available(Request $request): array;
}
