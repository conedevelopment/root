<?php

namespace Cone\Root\Traits;

use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

trait Resolvable
{
    /**
     * The key as the object has been resolved.
     *
     * @var string|null
     */
    protected ?string $resolvedAs = null;

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
        $this->resolvedAs = $key;
    }
}
