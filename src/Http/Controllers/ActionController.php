<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var \Cone\Root\Actions\Action $action */
        $action = $request->route('action');

        Gate::allowIf($action->authorized($request));

        return $action->perform($request);
    }
}
