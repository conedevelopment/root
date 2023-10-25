<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ActionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $action = $request->route('action');

        Gate::allowIf($action->authorized($request));

        return $action->perform($request);
    }
}
