<?php

namespace Cone\Root\Traits;

use Illuminate\Http\Request;

trait InteractsWithTurbo
{
    /**
     * Determine if the request is Turbo request.
     */
    public function isTurboRequest(Request $request): bool
    {
        return $request->hasHeader('Turbo-Frame');
    }
}
