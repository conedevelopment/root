<?php

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\Auth\ConfirmRequest;
use Illuminate\Contracts\View\View as ViewResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class ConfirmPasswordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the password confirmation view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(): ViewResponse
    {
        return View::make('root::auth.passwords.confirm');
    }

    /**
     * Confirm the given user's password.
     *
     * @param  \Cone\Root\Http\Requests\Auth\ConfirmRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(ConfirmRequest $request): RedirectResponse
    {
        $request->session()->put('auth.password_confirmed_at', time());

        return Redirect::intended(URL::route('root.dashboard'));
    }
}
