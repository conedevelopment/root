<?php

namespace Cone\Root\Http\Middleware;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthorizeResolved
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
        $rootRequest = RootRequest::createFrom($request);

        $resolved = $request->route('resolved');

        Gate::allowIf(is_null($resolved) || $resolved->authorized($rootRequest));

        return $next($request);
    }
}
