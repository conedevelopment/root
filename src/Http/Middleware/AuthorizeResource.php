<?php

namespace Cone\Root\Http\Middleware;

use Closure;
use Cone\Root\Http\Requests\ResourceRequest;
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
        $resourceRequest = ResourceRequest::createFrom($request);

        Gate::allowIf($resourceRequest->resource()->authorized($resourceRequest));

        return $next($request);
    }
}
