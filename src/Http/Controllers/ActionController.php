<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Actions\Action;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Action $action): RedirectResponse
    {
        return $action->perform($request);
    }
}
