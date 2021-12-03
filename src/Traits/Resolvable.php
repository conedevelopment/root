<?php

namespace Cone\Root\Traits;

use Illuminate\Http\Request;

trait Resolvable
{
    /**
     * Handle the event when the object is resolved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function resolved(Request $request): void
    {
        //
    }
}
