<?php

namespace Cone\Root\Interfaces;

use Illuminate\Http\Request;

interface Routable
{
    /**
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $uri
     * @return void
     */
    public function routes(Request $request, ?string $uri = null): void;
}
