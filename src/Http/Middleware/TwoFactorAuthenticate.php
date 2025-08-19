<?php

declare(strict_types=1);

namespace Cone\Root\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->shouldTwoFactorAuthenticate($request)) {
            return Redirect::route('root.auth.two-factor.show');
        }

        return $next($request);
    }
}
