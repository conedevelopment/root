<?php

namespace Cone\Root\Http\Middleware;

use Closure;
use Cone\Root\Interfaces\TwoFactorAuthenticatable;
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
        if ($request->user() instanceof TwoFactorAuthenticatable
            && $request->user()->requiresTwoFactorAuthentication()
            && ! $request->session()->has('root.auth.two-factor')
        ) {
            return Redirect::route('root.auth.two-factor.show');
        }

        return $next($request);
    }
}
