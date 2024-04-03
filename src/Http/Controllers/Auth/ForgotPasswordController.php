<?php

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function show(): Response
    {
        return ResponseFactory::view('root::auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'string', 'email']]);

        Password::broker()->sendResetLink($data, static function (User $user, string $token): void {
            $user->notify(new ResetPassword($token));
        });

        return Redirect::route('root.auth.password.request')->with('status', __(Password::RESET_LINK_SENT));
    }
}
