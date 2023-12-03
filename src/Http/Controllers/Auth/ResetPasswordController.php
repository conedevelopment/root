<?php

namespace Cone\Root\Http\Controllers\Auth;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Cone\Root\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
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
     * Display the password reset view for the given token.
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
    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $response = Password::broker()->reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password): void {
                $this->resetPassword($user, $password);

                if (! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            }
        );

        return $response == Password::PASSWORD_RESET
            ? Redirect::route('root.dashboard')->with('message', __($response))
            : Redirect::back()->withInput($request->only(['email']))->withErrors(['email' => __($response)]);
    }

    /**
     * Reset the given user's password.
     */
    protected function resetPassword(User $user, string $password): void
    {
        $user->setAttribute('password', Hash::make($password));

        $user->setRememberToken(Str::random(60));

        $user->save();

        Event::dispatch(new PasswordReset($user));

        Auth::guard()->login($user);
    }
}
