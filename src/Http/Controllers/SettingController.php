<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingController extends Controller
{
    /**
     * Show the form for the settings group.
     */
    public function edit(Request $request, string $group): Response
    {
        //
    }

    /**
     * Update the settings group.
     */
    public function update(Request $request, string $group): RedirectResponse
    {
        //
    }
}
