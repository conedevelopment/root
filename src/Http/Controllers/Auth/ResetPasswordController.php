<?php

namespace Cone\Root\Http\Controllers\Auth;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\Auth\ResetRequest;
use Cone\Root\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View as ViewResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request): ViewResponse
    {
        return View::make('root::auth.passwords.reset', [
            'email' => $request->route()->parameter('email'),
            'token' => $request->route()->parameter('token'),
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Cone\Root\Http\Requests\Auth\ResetRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(ResetRequest $request): RedirectResponse
    {
        $response = Password::broker()->reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']),
            function (User $user, string $password): void {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? Redirect::route('root.dashboard')->with('message', __($response))
                    : Redirect::back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => __($response)]);;
    }

    /**
     * Reset the given user's password.
     *
     * @param  \App\Models\User  $user
     * @param  string  $password
     * @return void
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
