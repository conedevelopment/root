<?php

declare(strict_types=1);

namespace Cone\Root\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $name, ...$parameters): mixed
    {
        Gate::allowIf($request->route($name)->authorized(
            $request,
            ...array_values(Arr::only($request->route()->parameters(), $parameters))
        ));

        return $next($request);
    }
}
