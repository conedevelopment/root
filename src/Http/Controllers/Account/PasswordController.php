<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class PasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        Auth::logoutOtherDevices($data['password']);

        return Redirect::route('root.account')->with('message', __('Password updated!'));
    }
}
