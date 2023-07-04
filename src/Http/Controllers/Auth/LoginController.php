<?php

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     */
    public function show(): Response
    {
        return ResponseFactory::view('root::auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): RedirectResponse
    {
        if (! $this->attemptLogin($request)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! Auth::guard()->user()->hasVerifiedEmail()) {
            $this->logout($request);

            throw ValidationException::withMessages([
                'email' => [__('auth.unverified')],
            ]);
        }

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return Redirect::intended(URL::route('dashboard'));
    }

    /**
     * Attempt to log the user into the application.
     */
    protected function attemptLogin(Request $request): bool
    {
        return Auth::guard()->attempt(
            $request->only(['email', 'password']),
            $request->filled('remember')
        );
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Redirect::route('login');
    }
}
