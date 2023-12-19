<?php

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
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
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:256'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard()->attempt($validated, $request->filled('remember'))) {
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

        Event::dispatch(
            new Login(Auth::getDefaultDriver(), $request->user(), $request->filled('remember'))
        );

        return Redirect::intended(URL::route('root.dashboard'));
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
