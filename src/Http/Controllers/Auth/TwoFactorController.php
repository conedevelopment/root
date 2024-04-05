<?php

namespace Cone\Root\Http\Controllers\Auth;

use Closure;
use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Middleware\Authenticate;
use Cone\Root\Interfaces\TwoFactorAuthenticatable;
use Cone\Root\Notifications\TwoFactorLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class TwoFactorController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(Authenticate::class);
        $this->middleware('throttle:6,1')->only(['resend']);
        $this->middleware(static function (Request $request, Closure $next): BaseResponse {
            if (! $request->user() instanceof TwoFactorAuthenticatable
                || $request->session()->has('root.auth.two-factor')
            ) {
                return ResponseFactory::redirectToIntended(URL::route('root.dashboard'));
            }

            return $next($request);
        });
    }

    /**
     * Show the verification resend form.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        return ResponseFactory::view('root::auth.two-factor');
    }

    /**
     * Verify the link.
     */
    public function verify(Request $request): RedirectResponse
    {
        if (! $request->hasValidSignature() || ! hash_equals($request->input('hash'), sha1($request->user()->email))) {
            return ResponseFactory::redirectToRoute('root.auth.two-factor.show')
                ->with('status', __('The authentication link is not valid! Please request a new link!'));
        }

        $request->session()->put('root.auth.two-factor', true);

        return ResponseFactory::redirectToIntended(URL::route('root.dashboard'));
    }

    /**
     * Resend the verification link.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->user()->notify(new TwoFactorLink());

        return ResponseFactory::redirectToRoute('root.auth.two-factor.show')
            ->with('status', __('The two factor authentication link has been sent!'));
    }
}
