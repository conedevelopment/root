<?php

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Contracts\View\View as ViewResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(): ViewResponse
    {
        return View::make('root::auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Cone\Root\Http\Requests\Auth\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(ForgotPasswordRequest $request): RedirectResponse
    {
        $response = Password::broker()->sendResetLink(
            $request->only(['email'])
        );

        return $response == Password::RESET_LINK_SENT
                    ? Redirect::back()->with('message', __($response))
                    : Redirect::back()
                            ->withInput($request->only(['email']))
                            ->withErrors(['email' => __($response)]);
    }
}
