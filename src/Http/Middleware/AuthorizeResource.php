<?php

namespace Cone\Root\Http\Middleware;

use Closure;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class AuthorizeResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $resource = Resource::resolve(
            $request->route('resource') ?: ($request->route()->action['resource'] ?? null)
        );

        Gate::allowIf($resource->authorized($request));

        return $next($request);
    }
}
