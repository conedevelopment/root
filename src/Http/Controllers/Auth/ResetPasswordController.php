<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset form for the given token.
     */
    public function show(Request $request): Response
    {
        return ResponseFactory::view('root::auth.reset-password', [
            'email' => $request->route()->parameter('email'),
            'token' => $request->route()->parameter('token'),
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
            'email' => ['required', 'string', 'email'],
        ]);

        $response = Password::broker()->reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, #[\SensitiveParameter] string $password): void {
                $this->resetPassword($user, $password);

                if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            }
        );

        return $response == Password::PASSWORD_RESET
            ? Redirect::route('root.dashboard')->with('status', __($response))
            : Redirect::back()->withInput($request->only(['email']))->withErrors(['email' => __($response)]);
    }

    /**
     * Reset the given user's password.
     */
    protected function resetPassword(User $user, #[\SensitiveParameter] string $password): void
    {
        $user->setAttribute('password', Hash::make($password));

        $user->setRememberToken(Str::random(60));

        $user->save();

        Event::dispatch(new PasswordReset($user));

        Auth::guard()->login($user);
    }
}
