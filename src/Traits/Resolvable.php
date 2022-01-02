<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

trait Resolvable
{
    /**
     * Handle the event when the object is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        //
    }
}
