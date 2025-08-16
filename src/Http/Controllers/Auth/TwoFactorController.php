<?php

namespace Cone\Root\Http\Controllers\Auth;

use Closure;
use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Middleware\Authenticate;
use Cone\Root\Notifications\AuthCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Date;
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
            if (! $request->user()->shouldTwoFactorAuthenticate($request)) {
                return ResponseFactory::redirectToIntended(URL::route('root.dashboard'));
            }

            return $next($request);
        });
    }

    /**
     * Show the verification resend form.
     */
    public function show(Request $request): Response
    {
        return ResponseFactory::view('root::auth.two-factor', [
            'code' => $request->input('code'),
        ]);
    }

    /**
     * Verify the link.
     */
    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'numeric'],
        ]);

        if ($request->user()->authCode?->code !== (int) $data['code']) {
            return ResponseFactory::redirectToRoute('root.auth.two-factor.show')
                ->withErrors(['code' => __('The authentication code is not valid!')]);
        }

        $request->session()->put('root.auth.two-factor', true);

        $request->user()->authCodes()->delete();

        if ($request->boolean('trust')) {
            Cookie::queue(
                'device_token',
                $request->user()->generateDeviceToken($request),
                Date::now()->addYear()->diffInMinutes(absolute: true),
            );
        }

        return ResponseFactory::redirectToIntended(URL::route('root.dashboard'));
    }

    /**
     * Resend the verification link.
     */
    public function resend(Request $request): RedirectResponse
    {
        $code = $request->user()->generateAuthCode();

        $request->user()->notify(new AuthCodeNotification($code));

        return ResponseFactory::redirectToRoute('root.auth.two-factor.show')
            ->with('status', __('The authentication code has been sent!'));
    }
}
