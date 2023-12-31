<?php

namespace Cone\Root\Traits;

use Illuminate\Http\Request;

trait InteractsWithTurbo
{
    /**
     * Determine if the request is Turbo Frame request.
     */
    public function isTurboFrameRequest(Request $request): bool
    {
        return $request->hasHeader('Turbo-Frame');
    }
}
