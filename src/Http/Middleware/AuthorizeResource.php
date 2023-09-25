<?php

namespace Cone\Root\Http\Middleware;

use Closure;
use Cone\Root\Root;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthorizeResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Gate::allowIf(Root::instance()->getCurrentResource()->authorized($request));

        return $next($request);
    }
}
